<?php

namespace App\Http\Controllers;

use App\Models\Transaccion;
use Illuminate\Http\Request;

class TransaccionController extends Controller
{
    /**
     * Listar transacciones del usuario
     */
    public function index(Request $request)
    {
        $transacciones = $request->user()->transacciones()
            ->with(['cuentaOrigen', 'cuentaDestino', 'categoria', 'subcategoria'])
            ->orderBy('fecha_transaccion', 'desc')
            ->paginate(20);

        return response()->json($transacciones);
    }

    /**
     * Crear nueva transacción
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cuenta_origen_id' => 'sometimes|exists:cuentas,id',
            'cuenta_destino_id' => 'sometimes|exists:cuentas,id',
            'tipo' => 'required|in:ingreso,gasto,transferencia,deposito,retiro',
            'categoria_id' => 'sometimes|exists:categorias,id',
            'subcategoria_id' => 'sometimes|exists:subcategorias,id',
            'moneda' => 'required|string|max:3',
            'monto' => 'required|numeric|min:0.01',
            'descripcion' => 'sometimes|string',
            'notas' => 'sometimes|string',
            'fecha_transaccion' => 'required|datetime',
            'es_recurrente' => 'sometimes|boolean',
            'frecuencia' => 'sometimes|in:diaria,semanal,mensual,anual',
        ]);

        // Validar que las cuentas pertenecen al usuario
        if (isset($validated['cuenta_origen_id'])) {
            $request->user()->cuentas()->findOrFail($validated['cuenta_origen_id']);
        }
        if (isset($validated['cuenta_destino_id'])) {
            $request->user()->cuentas()->findOrFail($validated['cuenta_destino_id']);
        }

        $transaccion = $request->user()->transacciones()->create([
            ...$validated,
            'estado' => 'completada',
        ]);

        // Actualizar saldos de cuentas
        $this->actualizarSaldos($transaccion);

        return response()->json([
            'message' => 'Transacción creada',
            'transaccion' => $transaccion->load(['cuentaOrigen', 'cuentaDestino', 'categoria']),
        ], 201);
    }

    /**
     * Obtener una transacción
     */
    public function show(Request $request, Transaccion $transaccion)
    {
        $this->authorize('view', $transaccion);

        return response()->json($transaccion->load(['cuentaOrigen', 'cuentaDestino', 'categoria', 'subcategoria']));
    }

    /**
     * Actualizar transacción
     */
    public function update(Request $request, Transaccion $transaccion)
    {
        $this->authorize('update', $transaccion);

        $validated = $request->validate([
            'cuenta_origen_id' => 'sometimes|exists:cuentas,id',
            'cuenta_destino_id' => 'sometimes|exists:cuentas,id',
            'tipo' => 'sometimes|in:ingreso,gasto,transferencia,deposito,retiro',
            'categoria_id' => 'sometimes|exists:categorias,id',
            'subcategoria_id' => 'sometimes|exists:subcategorias,id',
            'moneda' => 'sometimes|string|max:3',
            'monto' => 'sometimes|numeric|min:0.01',
            'descripcion' => 'sometimes|string',
            'notas' => 'sometimes|string',
            'fecha_transaccion' => 'sometimes|datetime',
        ]);

        // Revertir saldos anteriores
        $this->revertirSaldos($transaccion);

        $transaccion->update($validated);

        // Actualizar saldos con nuevos valores
        $this->actualizarSaldos($transaccion);

        return response()->json([
            'message' => 'Transacción actualizada',
            'transaccion' => $transaccion->load(['cuentaOrigen', 'cuentaDestino', 'categoria']),
        ]);
    }

    /**
     * Eliminar transacción
     */
    public function destroy(Request $request, Transaccion $transaccion)
    {
        $this->authorize('delete', $transaccion);

        // Revertir saldos
        $this->revertirSaldos($transaccion);

        $transaccion->delete();

        return response()->json([
            'message' => 'Transacción eliminada',
        ]);
    }

    /**
     * Filtrar transacciones
     */
    public function filtrar(Request $request)
    {
        $query = $request->user()->transacciones();

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('cuenta_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('cuenta_origen_id', $request->cuenta_id)
                    ->orWhere('cuenta_destino_id', $request->cuenta_id);
            });
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha_transaccion', [
                $request->fecha_inicio,
                $request->fecha_fin
            ]);
        }

        $transacciones = $query->with(['cuentaOrigen', 'cuentaDestino', 'categoria'])
            ->orderBy('fecha_transaccion', 'desc')
            ->paginate(20);

        return response()->json($transacciones);
    }

    /**
     * Resumen mensual
     */
    public function resumenMensual(Request $request)
    {
        $validated = $request->validate([
            'mes' => 'required|integer|min:1|max:12',
            'año' => 'required|integer',
        ]);

        $transacciones = $request->user()->transacciones()
            ->whereYear('fecha_transaccion', $validated['año'])
            ->whereMonth('fecha_transaccion', $validated['mes'])
            ->with('categoria')
            ->get();

        $ingresos = $transacciones->where('tipo', 'ingreso')->sum('monto');
        $gastos = $transacciones->where('tipo', 'gasto')->sum('monto');
        $balance = $ingresos - $gastos;

        return response()->json([
            'ingresos' => $ingresos,
            'gastos' => $gastos,
            'balance' => $balance,
            'transacciones' => $transacciones,
        ]);
    }

    /**
     * Actualizar saldos de cuentas
     */
    private function actualizarSaldos(Transaccion $transaccion)
    {
        if ($transaccion->tipo === 'ingreso' && $transaccion->cuenta_destino_id) {
            $transaccion->cuentaDestino->increment('saldo_actual', $transaccion->monto);
        } elseif ($transaccion->tipo === 'gasto' && $transaccion->cuenta_origen_id) {
            $transaccion->cuentaOrigen->decrement('saldo_actual', $transaccion->monto);
        } elseif ($transaccion->tipo === 'transferencia') {
            if ($transaccion->cuenta_origen_id) {
                $transaccion->cuentaOrigen->decrement('saldo_actual', $transaccion->monto);
            }
            if ($transaccion->cuenta_destino_id) {
                $transaccion->cuentaDestino->increment('saldo_actual', $transaccion->monto);
            }
        } elseif ($transaccion->tipo === 'deposito' && $transaccion->cuenta_destino_id) {
            $transaccion->cuentaDestino->increment('saldo_actual', $transaccion->monto);
        } elseif ($transaccion->tipo === 'retiro' && $transaccion->cuenta_origen_id) {
            $transaccion->cuentaOrigen->decrement('saldo_actual', $transaccion->monto);
        }
    }

    /**
     * Revertir saldos de cuentas
     */
    private function revertirSaldos(Transaccion $transaccion)
    {
        if ($transaccion->tipo === 'ingreso' && $transaccion->cuenta_destino_id) {
            $transaccion->cuentaDestino->decrement('saldo_actual', $transaccion->monto);
        } elseif ($transaccion->tipo === 'gasto' && $transaccion->cuenta_origen_id) {
            $transaccion->cuentaOrigen->increment('saldo_actual', $transaccion->monto);
        } elseif ($transaccion->tipo === 'transferencia') {
            if ($transaccion->cuenta_origen_id) {
                $transaccion->cuentaOrigen->increment('saldo_actual', $transaccion->monto);
            }
            if ($transaccion->cuenta_destino_id) {
                $transaccion->cuentaDestino->decrement('saldo_actual', $transaccion->monto);
            }
        } elseif ($transaccion->tipo === 'deposito' && $transaccion->cuenta_destino_id) {
            $transaccion->cuentaDestino->decrement('saldo_actual', $transaccion->monto);
        } elseif ($transaccion->tipo === 'retiro' && $transaccion->cuenta_origen_id) {
            $transaccion->cuentaOrigen->increment('saldo_actual', $transaccion->monto);
        }
    }
}
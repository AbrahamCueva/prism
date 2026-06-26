<?php

namespace App\Http\Controllers;

use App\Models\Ahorro;
use Illuminate\Http\Request;

class AhorroController extends Controller
{
    /**
     * Listar ahorros del usuario
     */
    public function index(Request $request)
    {
        $ahorros = $request->user()->ahorros()
            ->with('cuenta')
            ->get()
            ->map(function ($ahorro) {
                $ahorro->progreso_porcentaje = ($ahorro->monto_actual / $ahorro->monto_objetivo) * 100;
                return $ahorro;
            });

        return response()->json($ahorros);
    }

    /**
     * Crear nueva meta de ahorro
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'sometimes|string',
            'monto_objetivo' => 'required|numeric|min:0.01',
            'moneda' => 'required|string|max:3',
            'cuenta_id' => 'required|exists:cuentas,id',
            'fecha_inicio' => 'required|date',
            'fecha_objetivo' => 'required|date|after:fecha_inicio',
            'icono' => 'sometimes|string|max:10',
            'color' => 'sometimes|string|max:7',
        ]);

        // Verificar que la cuenta pertenece al usuario
        $request->user()->cuentas()->findOrFail($validated['cuenta_id']);

        $ahorro = $request->user()->ahorros()->create([
            ...$validated,
            'monto_actual' => 0,
            'estado' => 'activa',
            'progreso_porcentaje' => 0,
        ]);

        return response()->json([
            'message' => 'Meta de ahorro creada',
            'ahorro' => $ahorro->load('cuenta'),
        ], 201);
    }

    /**
     * Obtener una meta de ahorro
     */
    public function show(Request $request, Ahorro $ahorro)
    {
        $this->authorize('view', $ahorro);

        $ahorro->progreso_porcentaje = ($ahorro->monto_actual / $ahorro->monto_objetivo) * 100;

        return response()->json($ahorro->load('cuenta'));
    }

    /**
     * Actualizar meta de ahorro
     */
    public function update(Request $request, Ahorro $ahorro)
    {
        $this->authorize('update', $ahorro);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'monto_objetivo' => 'sometimes|numeric|min:0.01',
            'moneda' => 'sometimes|string|max:3',
            'cuenta_id' => 'sometimes|exists:cuentas,id',
            'fecha_inicio' => 'sometimes|date',
            'fecha_objetivo' => 'sometimes|date',
            'icono' => 'sometimes|string|max:10',
            'color' => 'sometimes|string|max:7',
            'estado' => 'sometimes|in:activa,completada,cancelada',
        ]);

        if (isset($validated['cuenta_id'])) {
            $request->user()->cuentas()->findOrFail($validated['cuenta_id']);
        }

        $ahorro->update($validated);

        return response()->json([
            'message' => 'Meta de ahorro actualizada',
            'ahorro' => $ahorro->load('cuenta'),
        ]);
    }

    /**
     * Eliminar meta de ahorro
     */
    public function destroy(Request $request, Ahorro $ahorro)
    {
        $this->authorize('delete', $ahorro);

        $ahorro->delete();

        return response()->json([
            'message' => 'Meta de ahorro eliminada',
        ]);
    }

    /**
     * Obtener progreso de la meta
     */
    public function getProgreso(Request $request, Ahorro $ahorro)
    {
        $this->authorize('view', $ahorro);

        $progreso = ($ahorro->monto_actual / $ahorro->monto_objetivo) * 100;
        $faltante = $ahorro->monto_objetivo - $ahorro->monto_actual;

        return response()->json([
            'monto_objetivo' => $ahorro->monto_objetivo,
            'monto_actual' => $ahorro->monto_actual,
            'faltante' => max(0, $faltante),
            'progreso_porcentaje' => min(100, $progreso),
            'estado' => $ahorro->estado,
        ]);
    }

    /**
     * Depositar a una meta de ahorro
     */
    public function depositar(Request $request, Ahorro $ahorro)
    {
        $this->authorize('update', $ahorro);

        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'descripcion' => 'sometimes|string',
        ]);

        // Verificar que no exceda el objetivo
        $nuevoMonto = $ahorro->monto_actual + $validated['monto'];
        if ($nuevoMonto > $ahorro->monto_objetivo) {
            return response()->json([
                'message' => 'El monto excede el objetivo de ahorro',
            ], 422);
        }

        // Restar de la cuenta
        $cuenta = $ahorro->cuenta;
        if ($cuenta->saldo_actual < $validated['monto']) {
            return response()->json([
                'message' => 'Saldo insuficiente en la cuenta',
            ], 422);
        }

        $ahorro->update([
            'monto_actual' => $nuevoMonto,
            'progreso_porcentaje' => ($nuevoMonto / $ahorro->monto_objetivo) * 100,
        ]);

        $cuenta->decrement('saldo_actual', $validated['monto']);

        // Crear transacción de depósito a ahorro
        $request->user()->transacciones()->create([
            'cuenta_origen_id' => $cuenta->id,
            'tipo' => 'gasto',
            'moneda' => $ahorro->moneda,
            'monto' => $validated['monto'],
            'descripcion' => 'Depósito a ahorro: ' . $ahorro->nombre,
            'notas' => $validated['descripcion'] ?? null,
            'fecha_transaccion' => now(),
            'estado' => 'completada',
        ]);

        // Completar meta si alcanza el objetivo
        if ($nuevoMonto >= $ahorro->monto_objetivo) {
            $ahorro->update(['estado' => 'completada']);
        }

        return response()->json([
            'message' => 'Depósito realizado',
            'ahorro' => $ahorro,
        ]);
    }
}
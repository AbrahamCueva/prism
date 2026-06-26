<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use Illuminate\Http\Request;

class CuentaController extends Controller
{
    /**
     * Listar todas las cuentas del usuario
     */
    public function index(Request $request)
    {
        $cuentas = $request->user()->cuentas()->get();

        return response()->json($cuentas);
    }

    /**
     * Crear nueva cuenta
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:bancaria,billetera_digital,fondo,efectivo',
            'moneda' => 'required|string|max:3',
            'saldo_actual' => 'required|numeric|min:0',
            'numero_cuenta' => 'sometimes|string',
            'banco' => 'sometimes|string|max:255',
            'color' => 'sometimes|string|max:7',
        ]);

        $cuenta = $request->user()->cuentas()->create([
            ...$validated,
            'saldo_inicial' => $validated['saldo_actual'],
            'numero_cuenta' => encrypt($validated['numero_cuenta'] ?? null),
        ]);

        return response()->json([
            'message' => 'Cuenta creada',
            'cuenta' => $cuenta,
        ], 201);
    }

    /**
     * Obtener una cuenta
     */
    public function show(Request $request, Cuenta $cuenta)
    {
        $this->authorize('view', $cuenta);

        return response()->json($cuenta);
    }

    /**
     * Actualizar cuenta
     */
    public function update(Request $request, Cuenta $cuenta)
    {
        $this->authorize('update', $cuenta);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'tipo' => 'sometimes|in:bancaria,billetera_digital,fondo,efectivo',
            'moneda' => 'sometimes|string|max:3',
            'saldo_actual' => 'sometimes|numeric|min:0',
            'numero_cuenta' => 'sometimes|string',
            'banco' => 'sometimes|string|max:255',
            'color' => 'sometimes|string|max:7',
            'activa' => 'sometimes|boolean',
        ]);

        if (isset($validated['numero_cuenta'])) {
            $validated['numero_cuenta'] = encrypt($validated['numero_cuenta']);
        }

        $cuenta->update($validated);

        return response()->json([
            'message' => 'Cuenta actualizada',
            'cuenta' => $cuenta,
        ]);
    }

    /**
     * Eliminar cuenta
     */
    public function destroy(Request $request, Cuenta $cuenta)
    {
        $this->authorize('delete', $cuenta);

        $cuenta->delete();

        return response()->json([
            'message' => 'Cuenta eliminada',
        ]);
    }

    /**
     * Obtener saldo actual de una cuenta
     */
    public function getSaldo(Request $request, Cuenta $cuenta)
    {
        $this->authorize('view', $cuenta);

        return response()->json([
            'saldo_actual' => $cuenta->saldo_actual,
            'moneda' => $cuenta->moneda,
        ]);
    }
}
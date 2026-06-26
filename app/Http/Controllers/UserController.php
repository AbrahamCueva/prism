<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Obtener datos del usuario
     */
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Actualizar datos del usuario
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'apellido' => 'sometimes|string|max:255',
            'fecha_nacimiento' => 'sometimes|date',
            'foto' => 'sometimes|string',
            'telefono' => 'sometimes|string|max:20',
            'pais' => 'sometimes|string|max:2',
        ]);

        $request->user()->update($validated);

        return response()->json([
            'message' => 'Usuario actualizado',
            'user' => $request->user(),
        ]);
    }

    /**
     * Obtener configuración del usuario
     */
    public function getConfiguracion(Request $request)
    {
        $configuracion = $request->user()->configuracion;

        return response()->json($configuracion);
    }

    /**
     * Actualizar configuración del usuario
     */
    public function updateConfiguracion(Request $request)
    {
        $validated = $request->validate([
            'idioma' => 'sometimes|string|max:5',
            'moneda_predeterminada' => 'sometimes|string|max:3',
            'tema' => 'sometimes|in:light,dark,auto',
            'notificaciones_habilitadas' => 'sometimes|boolean',
            'privacidad_datos' => 'sometimes|in:publico,privado',
        ]);

        $request->user()->configuracion()->update($validated);

        return response()->json([
            'message' => 'Configuración actualizada',
            'configuracion' => $request->user()->configuracion,
        ]);
    }
}
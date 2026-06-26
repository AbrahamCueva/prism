<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Listar categorías del usuario
     */
    public function index(Request $request)
    {
        $categorias = $request->user()->categorias()
            ->with('subcategorias')
            ->get();

        return response()->json($categorias);
    }

    /**
     * Crear nueva categoría
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'sometimes|string',
            'icono' => 'sometimes|string|max:10',
            'color' => 'sometimes|string|max:7',
            'tipo' => 'required|in:gasto,ingreso,transferencia',
        ]);

        $categoria = $request->user()->categorias()->create($validated);

        return response()->json([
            'message' => 'Categoría creada',
            'categoria' => $categoria,
        ], 201);
    }

    /**
     * Obtener una categoría
     */
    public function show(Request $request, Categoria $categoria)
    {
        $this->authorize('view', $categoria);

        return response()->json($categoria->load('subcategorias'));
    }

    /**
     * Actualizar categoría
     */
    public function update(Request $request, Categoria $categoria)
    {
        $this->authorize('update', $categoria);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'icono' => 'sometimes|string|max:10',
            'color' => 'sometimes|string|max:7',
            'tipo' => 'sometimes|in:gasto,ingreso,transferencia',
        ]);

        $categoria->update($validated);

        return response()->json([
            'message' => 'Categoría actualizada',
            'categoria' => $categoria,
        ]);
    }

    /**
     * Eliminar categoría
     */
    public function destroy(Request $request, Categoria $categoria)
    {
        $this->authorize('delete', $categoria);

        $categoria->delete();

        return response()->json([
            'message' => 'Categoría eliminada',
        ]);
    }
}
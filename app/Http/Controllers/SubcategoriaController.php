<?php

namespace App\Http\Controllers;

use App\Models\Subcategoria;
use App\Models\Categoria;
use Illuminate\Http\Request;

class SubcategoriaController extends Controller
{
    /**
     * Listar subcategorías
     */
    public function index(Request $request)
    {
        $subcategorias = Subcategoria::with('categoria')
            ->whereHas('categoria', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->get();

        return response()->json($subcategorias);
    }

    /**
     * Crear nueva subcategoría
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'sometimes|string',
            'icono' => 'sometimes|string|max:10',
        ]);

        // Verificar que la categoría pertenece al usuario
        $categoria = Categoria::where('id', $validated['categoria_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $subcategoria = $categoria->subcategorias()->create($validated);

        return response()->json([
            'message' => 'Subcategoría creada',
            'subcategoria' => $subcategoria,
        ], 201);
    }

    /**
     * Obtener una subcategoría
     */
    public function show(Request $request, Subcategoria $subcategoria)
    {
        return response()->json($subcategoria->load('categoria'));
    }

    /**
     * Actualizar subcategoría
     */
    public function update(Request $request, Subcategoria $subcategoria)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'icono' => 'sometimes|string|max:10',
        ]);

        $subcategoria->update($validated);

        return response()->json([
            'message' => 'Subcategoría actualizada',
            'subcategoria' => $subcategoria,
        ]);
    }

    /**
     * Eliminar subcategoría
     */
    public function destroy(Request $request, Subcategoria $subcategoria)
    {
        $subcategoria->delete();

        return response()->json([
            'message' => 'Subcategoría eliminada',
        ]);
    }

    /**
     * Obtener subcategorías por categoría
     */
    public function porCategoria(Request $request, $categoriaId)
    {
        $categoria = Categoria::where('id', $categoriaId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $subcategorias = $categoria->subcategorias()->get();

        return response()->json($subcategorias);
    }
}
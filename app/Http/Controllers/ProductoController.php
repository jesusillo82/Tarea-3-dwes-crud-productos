<?php

namespace App\Http\Controllers;

use App\Models\Familia;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(): View
    {
        $productos = Producto::with('familiaRelacion')->orderBy('id')->paginate(10);

        return view('productos.index', compact('productos'));
    }

    public function create(): View
    {
        $familias = Familia::orderBy('nombre')->get();

        return view('productos.create', compact('familias'));
    }

    public function store(Request $request): RedirectResponse
    {
        $producto = Producto::create($this->validatedData($request));

        return redirect()
            ->route('productos.show', $producto)
            ->with('status', 'Producto creado correctamente.');
    }

    public function show(Producto $producto): View
    {
        $producto->load('familiaRelacion');

        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto): View
    {
        $familias = Familia::orderBy('nombre')->get();

        return view('productos.edit', compact('producto', 'familias'));
    }

    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $producto->update($this->validatedData($request));

        return redirect()
            ->route('productos.show', $producto)
            ->with('status', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        $producto->delete();

        return redirect()
            ->route('productos.index')
            ->with('status', 'Producto eliminado correctamente.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:200'],
            'nombre_corto' => ['required', 'string', 'max:50'],
            'descripcion' => ['nullable', 'string'],
            'pvp' => ['required', 'numeric', 'min:0'],
            'familia' => ['required', 'string', 'exists:familias,cod'],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 200 caracteres.',
            'nombre_corto.required' => 'El nombre corto es obligatorio.',
            'nombre_corto.max' => 'El nombre corto no puede superar los 50 caracteres.',
            'pvp.required' => 'El precio es obligatorio.',
            'pvp.numeric' => 'El precio debe ser un número válido.',
            'pvp.min' => 'El precio no puede ser negativo.',
            'familia.required' => 'Selecciona una familia.',
            'familia.exists' => 'La familia seleccionada no existe.',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Familia;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Atiende las operaciones web de consulta y mantenimiento de productos.
 */
class ProductoController extends Controller
{
    /**
     * Muestra los productos ordenados por identificador, junto con su familia.
     */
    public function index(): View
    {
        $productos = Producto::with('familiaRelacion')->orderBy('id')->paginate(10);

        return view('productos.index', compact('productos'));
    }

    /**
     * Muestra el formulario de alta y las familias disponibles para seleccionar.
     */
    public function create(): View
    {
        $familias = Familia::orderBy('nombre')->get();

        return view('productos.create', compact('familias'));
    }

    /**
     * Valida y guarda un producto nuevo y redirige a su ficha.
     */
    public function store(Request $request): RedirectResponse
    {
        $producto = Producto::create($this->validatedData($request));

        return redirect()
            ->route('productos.show', $producto)
            ->with('status', 'Producto creado correctamente.');
    }

    /**
     * Muestra los datos de un producto y carga su familia relacionada.
     */
    public function show(Producto $producto): View
    {
        $producto->load('familiaRelacion');

        return view('productos.show', compact('producto'));
    }

    /**
     * Muestra el formulario de edición y las familias disponibles.
     */
    public function edit(Producto $producto): View
    {
        $familias = Familia::orderBy('nombre')->get();

        return view('productos.edit', compact('producto', 'familias'));
    }

    /**
     * Valida y guarda los cambios del producto existente.
     */
    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $producto->update($this->validatedData($request));

        return redirect()
            ->route('productos.show', $producto)
            ->with('status', 'Producto actualizado correctamente.');
    }

    /**
     * Elimina el producto y vuelve al listado con un mensaje de confirmación.
     */
    public function destroy(Producto $producto): RedirectResponse
    {
        $producto->delete();

        return redirect()
            ->route('productos.index')
            ->with('status', 'Producto eliminado correctamente.');
    }

    /**
     * Valida los campos editables y devuelve únicamente los datos permitidos.
     *
     * @return array<string, mixed>
     */
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

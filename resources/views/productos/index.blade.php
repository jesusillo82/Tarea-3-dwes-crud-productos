@extends('layouts.app')

@section('title', 'Listado de productos')

@section('content')
    <h1 class="h3 mb-3">Listado de productos</h1>

    <div class="table-responsive">
        <table class="table table-light table-striped align-middle">
            <thead>
                <tr>
                    <th scope="col">Código</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Familia</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                    <tr>
                        <td>{{ $producto->id }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->familiaRelacion?->nombre ?? 'Sin familia' }}</td>
                        <td>{{ number_format((float) $producto->pvp, 2, ',', '.') }} €</td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-info" href="{{ route('productos.show', $producto) }}">Detalle</a>
                            <a class="btn btn-sm btn-warning" href="{{ route('productos.edit', $producto) }}">Editar</a>
                            <form class="d-inline" action="{{ route('productos.destroy', $producto) }}" method="post" onsubmit="return confirm('¿Seguro que quieres eliminar este producto?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-4" colspan="5">No hay productos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $productos->links('pagination::bootstrap-5') }}
@endsection

@extends('layouts.app')

@section('title', 'Editar producto')

@section('content')
    <h1 class="h2 mb-3">Editar producto</h1>

    <form action="{{ route('productos.update', $producto) }}" method="post">
        @method('PUT')
        @include('productos._form', ['submitLabel' => 'Guardar cambios'])
    </form>
@endsection

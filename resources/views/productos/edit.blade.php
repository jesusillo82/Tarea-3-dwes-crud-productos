@extends('layouts.app')

@section('title', 'Editar producto')

@section('content')
    <article class="card">
        <div class="card-header">
            <h1 class="h3 mb-0">Editar producto</h1>
        </div>
        <form action="{{ route('productos.update', $producto) }}" method="post">
            <div class="card-body">
                @method('PUT')
                @include('productos._form', ['submitLabel' => 'Guardar cambios'])
            </div>
        </form>
    </article>
@endsection

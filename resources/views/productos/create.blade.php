@extends('layouts.app')

@section('title', 'Crear producto')

@section('content')
    <h1 class="h2 mb-3">Crear producto</h1>

    @if ($familias->isEmpty())
        <div class="alert alert-warning">No hay familias disponibles. Añade una familia antes de crear productos.</div>
    @endif

    <form action="{{ route('productos.store') }}" method="post">
        @include('productos._form', ['producto' => null, 'submitLabel' => 'Crear'])
    </form>
@endsection

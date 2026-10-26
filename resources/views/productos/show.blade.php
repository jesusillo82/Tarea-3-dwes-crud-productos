@extends('layouts.app')

@section('title', 'Detalle del producto')

@section('content')
    <article class="card">
        <div class="card-header">
            <h1 class="h3 mb-0">{{ $producto->nombre }}</h1>
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Código</dt>
                <dd class="col-sm-9">{{ $producto->id }}</dd>

                <dt class="col-sm-3">Nombre corto</dt>
                <dd class="col-sm-9">{{ $producto->nombre_corto }}</dd>

                <dt class="col-sm-3">Familia</dt>
                <dd class="col-sm-9">{{ $producto->familiaRelacion?->nombre ?? 'Sin familia' }}</dd>

                <dt class="col-sm-3">Precio</dt>
                <dd class="col-sm-9">{{ number_format((float) $producto->pvp, 2, ',', '.') }} €</dd>

                <dt class="col-sm-3">Descripción</dt>
                <dd class="col-sm-9">{{ $producto->descripcion ?: 'Sin descripción' }}</dd>
            </dl>
        </div>
        <div class="card-footer d-flex gap-2">
            <a class="btn btn-warning" href="{{ route('productos.edit', $producto) }}">Editar</a>
            <a class="btn btn-light" href="{{ route('productos.index') }}">Volver al listado</a>
        </div>
    </article>
@endsection

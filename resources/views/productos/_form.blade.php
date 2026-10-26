@csrf

<div class="mb-3">
    <label class="form-label fw-bold" for="nombre">Nombre</label>
    <input class="form-control" type="text" name="nombre" id="nombre" value="{{ old('nombre', $producto?->nombre) }}" maxlength="200" required>
    @error('nombre')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-bold" for="nombre_corto">Nombre corto</label>
    <input class="form-control" type="text" name="nombre_corto" id="nombre_corto" value="{{ old('nombre_corto', $producto?->nombre_corto) }}" maxlength="50" required>
    @error('nombre_corto')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label fw-bold" for="pvp">Precio (€)</label>
        <input class="form-control" type="number" name="pvp" id="pvp" value="{{ old('pvp', $producto?->pvp) }}" min="0" step="0.01" required>
        @error('pvp')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold" for="familia">Familia</label>
        <select class="form-select" name="familia" id="familia" required>
            <option value="">Selecciona una familia</option>
            @foreach ($familias as $familia)
                <option value="{{ $familia->cod }}" @selected((string) old('familia', $producto?->familia) === (string) $familia->cod)>
                    {{ $familia->nombre }}
                </option>
            @endforeach
        </select>
        @error('familia')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <label class="form-label fw-bold" for="descripcion">Descripción</label>
        <textarea class="form-control" name="descripcion" id="descripcion" rows="5">{{ old('descripcion', $producto?->descripcion) }}</textarea>
        @error('descripcion')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
<a class="btn btn-light" href="{{ route('productos.index') }}">Volver al listado</a>

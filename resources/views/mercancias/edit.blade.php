@extends('layouts.app')

@section('title', 'Editar Mercancía')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h4>Editar Mercancía</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('mercancias.update', $mercancia) }}">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nombre">Nombre mercancía:</label>
                        <input type="text" class="form-control" name="nombre" value="{{ old('nombre', $mercancia->nombre) }}" required>
                        @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="cantidad">Stock:</label>
                        <input type="number" class="form-control" name="cantidad" value="{{ old('cantidad', $mercancia->cantidad) }}" required min="1">
                        @error('cantidad')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="costo_compra">Costo de Compra:</label>
                        <input type="number" step="0.01" class="form-control" name="costo_compra" value="{{ old('costo_compra', $mercancia->costo_compra) }}" required>
                        @error('costo_compra')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="precio_venta">Precio de Venta:</label>
                        <input type="number" step="0.01" class="form-control" name="precio_venta" value="{{ old('precio_venta', $mercancia->precio_venta) }}" required>
                        @error('precio_venta')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="kilos_litros">Kilos/Litros:</label>
                    <input type="number" step="0.01" class="form-control" name="kilos_litros" value="{{ old('kilos_litros', $mercancia->kilos_litros) }}" required>
                    @error('kilos_litros')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Actualizar Mercancia</button>
                    <a href="{{ route('mercancias.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

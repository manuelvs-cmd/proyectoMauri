@extends('layouts.app')

@section('title', 'Crear Mercancía')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h4>Crear Nueva Mercancía</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('mercancias.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nombre">Nombre mercancía:</label>
                        <input type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required>
                        @error('nombre')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="cantidad">Stock:</label>
                        <input type="number" class="form-control" name="cantidad" value="{{ old('cantidad') }}" required min="1">
                        @error('cantidad')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="costo_compra">Costo de Compra:</label>
                        <input type="number" step="0.01" class="form-control" name="costo_compra" value="{{ old('costo_compra') }}" required>
                        @error('costo_compra')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="precio_venta">Precio de Venta:</label>
                        <input type="number" step="0.01" class="form-control" name="precio_venta" value="{{ old('precio_venta') }}" required>
                        @error('precio_venta')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="kilos_litros">Kilos/Litros:</label>
                    <input type="number" step="0.01" class="form-control" name="kilos_litros" value="{{ old('kilos_litros') }}" required>
                    @error('kilos_litros')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Crear Mercancía</button>
                    <a href="{{ route('mercancias.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Detalles de la Mercancia')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h4>Detalles de la Mercancía</h4>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Nombre:</dt>
                <dd class="col-sm-9">{{ $mercancia->nombre }}</dd>

                <dt class="col-sm-3">Cantidad:</dt>
                <dd class="col-sm-9">{{ $mercancia->cantidad }}</dd>

                <dt class="col-sm-3">Costo de Compra:</dt>
                <dd class="col-sm-9">${{ number_format($mercancia->costo_compra, 0, ',', '.') }}</dd>

                <dt class="col-sm-3">Precio de Venta:</dt>
                <dd class="col-sm-9">${{ number_format($mercancia->precio_venta, 0, ',', '.') }}</dd>

                <dt class="col-sm-3">Rentabilidad:</dt>
                <dd class="col-sm-9">{{ number_format($mercancia->rentabilidad, 2) }}%</dd>

                <dt class="col-sm-3">Kilos/Litros:</dt>
                <dd class="col-sm-9">{{ number_format($mercancia->kilos_litros, 2) }}</dd>
            </dl>
        </div>
        <div class="card-footer">
            <a href="{{ route('mercancias.edit', $mercancia) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar Mercancía
            </a>
            <a href="{{ route('mercancias.index') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> Volver a Lista
            </a>
        </div>
    </div>
</div>
@endsection

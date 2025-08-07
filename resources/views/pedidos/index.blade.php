@extends('layouts.app')

@section('title', 'Gestión de Pedidos')

@section('content-class', 'full-width')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Gestión de Pedidos</h4>
                    <a href="{{ route('pedidos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Pedido
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <!-- Resumen estadístico -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $pedidos->count() }}</h5>
                                    <p class="card-text">Total Pedidos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $pedidos->filter(fn($p) => !$p->tieneFactura())->count() }}</h5>
                                    <p class="card-text">Pendientes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $pedidos->filter(fn($p) => $p->tieneFactura())->count() }}</h5>
                                    <p class="card-text">Facturados</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">${{ number_format($pedidos->sum(fn($p) => $p->calcularTotal()), 0, ',', '.') }}</h5>
                                    <p class="card-text">Total Ventas</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($pedidos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Mercancías</th>
                                        <th>Artículos</th>
                                        <th>Total</th>
                                        <th>Fecha Entrega</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pedidos as $pedido)
                                        <tr>
                                            <td>
                                                <strong>{{ $pedido->cliente->razon_social }}</strong><br>
                                                <small class="text-muted">{{ $pedido->cliente->rut }}</small>
                                            </td>
                                            <td>
                                                @if($pedido->mercancias->count() == 1)
                                                    {{ $pedido->mercancias->first()->nombre }}
                                                    @if($pedido->tienePreciosPersonalizados())
                                                        <br><small class="text-danger"><i class="fas fa-tag"></i> Precio personalizado</small>
                                                    @endif
                                                @else
                                                    <div class="d-flex flex-wrap">
                                                        @foreach($pedido->mercancias->take(2) as $mercancia)
                                                            <span class="badge badge-secondary mr-1 mb-1">{{ $mercancia->nombre }}</span>
                                                        @endforeach
                                                        @if($pedido->mercancias->count() > 2)
                                                            <span class="badge badge-info">+{{ $pedido->mercancias->count() - 2 }} más</span>
                                                        @endif
                                                    </div>
                                                    @if($pedido->tienePreciosPersonalizados())
                                                        <small class="text-danger"><i class="fas fa-tag"></i> Precios personalizados</small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ $pedido->getCantidadTotal() }} artículos</td>
                                            <td>
                                                <span class="{{ $pedido->tienePreciosPersonalizados() ? 'font-weight-bold text-danger' : '' }}">
                                                    ${{ number_format($pedido->calcularTotal(), 0, ',', '.') }}
                                                    @if($pedido->tienePreciosPersonalizados())
                                                        <i class="fas fa-star" title="Precios personalizados"></i>
                                                    @endif
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y') }}</td>
                                            <td>
                                                @if($pedido->tieneFactura())
                                                    <span class="badge badge-success">Facturado</span>
                                                @else
                                                    <span class="badge badge-warning">Pendiente</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if(!$pedido->tieneFactura())
                                                        <a href="{{ route('facturas.create', ['pedido_id' => $pedido->id]) }}" class="btn btn-success btn-sm" title="Crear factura">
                                                            <i class="fas fa-file-invoice"></i>
                                                        </a>
                                                    @endif
                                                    <form action="{{ route('pedidos.destroy', $pedido->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este pedido?')" title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No hay pedidos registrados.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

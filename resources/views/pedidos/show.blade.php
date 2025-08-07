@extends('layouts.app')

@section('title', 'Detalles del Pedido')

@section('content-class', 'full-width')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Detalles del Pedido</h4>
                </div>
                <div class="card-body">
                    <!-- Datos del Cliente -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="text-primary"><i class="fas fa-user"></i> Datos del Cliente</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">RUT:</dt>
                                        <dd class="col-sm-8">{{ $pedido->cliente->rut }}</dd>
                                        <dt class="col-sm-4">Cliente:</dt>
                                        <dd class="col-sm-8">{{ $pedido->cliente->razon_social }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Datos de las Mercancías -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="text-success"><i class="fas fa-boxes"></i> Mercancías del Pedido</h5>
                            
                            @if($pedido->mercancias->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Producto</th>
                                                <th class="text-center">Cantidad</th>
                                                <th>Precio Unitario</th>
                                                <th class="text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pedido->mercancias as $index => $mercancia)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <strong>{{ $mercancia->nombre }}</strong>
                                                        @if($mercancia->pivot->precio_unitario !== null)
                                                            <i class="fas fa-star text-warning ml-2" title="Precio personalizado"></i>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $mercancia->pivot->cantidad_solicitada ?? 1 }}</td>
                                                    <td>
                                                        @if($mercancia->pivot->precio_unitario !== null)
                                                            <span class="text-danger font-weight-bold">
                                                                ${{ number_format($mercancia->pivot->precio_unitario, 0, ',', '.') }}
                                                            </span>
                                                            <small class="text-muted d-block">(Personalizado - Base: ${{ number_format($mercancia->precio_venta, 0, ',', '.') }})</small>
                                                        @else
                                                            ${{ number_format($mercancia->precio_venta, 0, ',', '.') }}
                                                        @endif
                                                    </td>
                                                    <td class="text-right font-weight-bold">
                                                        @php
                                                            $precio = $mercancia->pivot->precio_unitario ?? $mercancia->precio_venta;
                                                            $cantidad = $mercancia->pivot->cantidad_solicitada ?? 1;
                                                            $subtotal = $precio * $cantidad;
                                                        @endphp
                                                        ${{ number_format($subtotal, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-light">
                                                <td colspan="4" class="text-right font-weight-bold">TOTAL DEL PEDIDO:</td>
                                                <td class="text-right font-weight-bold text-success h5">
                                                    ${{ number_format($pedido->calcularTotal(), 0, ',', '.') }}
                                                    @if($pedido->tienePrecioPersonalizado())
                                                        <i class="fas fa-star text-warning ml-1" title="Contiene precios personalizados"></i>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                <!-- Resumen de cantidades -->
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="alert alert-info">
                                            <strong><i class="fas fa-info-circle"></i> Resumen:</strong><br>
                                            <span class="badge badge-primary">{{ $pedido->mercancias->count() }}</span> productos diferentes<br>
                                            <span class="badge badge-secondary">{{ $pedido->getCantidadTotal() }}</span> unidades en total
                                        </div>
                                    </div>
                                    @if($pedido->tienePrecioPersonalizado())
                                        <div class="col-md-6">
                                            <div class="alert alert-warning">
                                                <strong><i class="fas fa-exclamation-triangle"></i> Atención:</strong><br>
                                                Este pedido contiene precios personalizados marcados con <i class="fas fa-star text-warning"></i>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> No hay mercancías asignadas a este pedido.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Datos del Pedido -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="text-info"><i class="fas fa-clipboard-list"></i> Datos del Pedido</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-5">Fecha entrega:</dt>
                                        <dd class="col-sm-7">{{ \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y') }}</dd>
                                        <dt class="col-sm-5">Horario:</dt>
                                        <dd class="col-sm-7">
                                            <span class="badge badge-{{ $pedido->horario_entrega == 'Mañana' ? 'warning' : 'info' }}">{{ $pedido->horario_entrega }}</span>
                                        </dd>
                                        <dt class="col-sm-5">Dirección:</dt>
                                        <dd class="col-sm-7">{{ $pedido->direccion_entrega }}</dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-5">Condición pago:</dt>
                                        <dd class="col-sm-7">
                                            <span class="badge badge-{{ $pedido->condicion_pago == 'Pagado' ? 'success' : 'warning' }}">{{ $pedido->condicion_pago }}</span>
                                        </dd>
                                        <dt class="col-sm-5">Medio pago:</dt>
                                        <dd class="col-sm-7">
                                            <span class="badge badge-primary">{{ $pedido->formas_pago }}</span>
                                        </dd>
                                        <dt class="col-sm-5">Estado:</dt>
                                        <dd class="col-sm-7">
                                            @if($pedido->tieneFactura())
                                                <span class="badge badge-success"><i class="fas fa-check"></i> Facturado</span>
                                            @else
                                                <span class="badge badge-warning"><i class="fas fa-clock"></i> Pendiente</span>
                                            @endif
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    @if($pedido->observacion)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-secondary"><i class="fas fa-sticky-note"></i> Observaciones</h5>
                                <div class="alert alert-light">
                                    {{ $pedido->observacion }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Acciones -->
                    <div class="mt-3">
                        <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Pedido
                        </a>
                        @if(!$pedido->tieneFactura())
                            <a href="{{ route('facturas.create', ['pedido_id' => $pedido->id]) }}" class="btn btn-success">
                                <i class="fas fa-file-invoice"></i> Crear Factura
                            </a>
                        @endif
                        <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

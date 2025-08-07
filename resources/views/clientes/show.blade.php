@extends('layouts.app')

@section('title', 'Detalles del Cliente')

@section('content-class', 'full-width')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Detalles del Cliente</h4>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">RUT:</dt>
                                <dd class="col-sm-8">{{ $cliente->rut }}</dd>
                                <dt class="col-sm-4">Razón Social:</dt>
                                <dd class="col-sm-8">{{ $cliente->razon_social }}</dd>
                                <dt class="col-sm-4">Giro:</dt>
                                <dd class="col-sm-8">{{ $cliente->giro }}</dd>
                                <dt class="col-sm-4">Ciudad:</dt>
                                <dd class="col-sm-8">{{ $cliente->ciudad ?? '-' }}</dd>
                                <dt class="col-sm-4">Comuna:</dt>
                                <dd class="col-sm-8">{{ $cliente->comuna ?? '-' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Dirección:</dt>
                                <dd class="col-sm-8">{{ $cliente->direccion_exacta ?? '-' }}</dd>
                                <dt class="col-sm-4">Correo:</dt>
                                <dd class="col-sm-8">{{ $cliente->correo_electronico ?? '-' }}</dd>
                                <dt class="col-sm-4">Teléfono:</dt>
                                <dd class="col-sm-8">{{ $cliente->telefono }}</dd>
                                <dt class="col-sm-4">Orden Atención:</dt>
                                <dd class="col-sm-8">{{ $cliente->orden_atencion }}</dd>
                                <dt class="col-sm-4">Tipo Atención:</dt>
                                <dd class="col-sm-8">{{ $cliente->tipo_atencion }}</dd>
                            </dl>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <dl class="row">
                                <dt class="col-sm-2">Lista de Precios:</dt>
                                <dd class="col-sm-10">
                                    <span class="badge badge-info">{{ $cliente->lista_precios }}</span>
                                </dd>
                                <dt class="col-sm-2">Formas de Pago:</dt>
                                <dd class="col-sm-10">
                                    @php
                                        $formasPago = json_decode($cliente->formas_pago, true) ?? [];
                                    @endphp
                                    @foreach($formasPago as $forma)
                                        <span class="badge badge-primary mr-1">{{ $forma }}</span>
                                    @endforeach
                                </dd>
                                <dt class="col-sm-2">Condición de Pago:</dt>
                                <dd class="col-sm-10">
                                    @php
                                        $condicionpago = json_decode($cliente->condicion_pago, true) ?? [];
                                    @endphp
                                    @foreach($condicionpago as $condicion)
                                        <span class="badge badge-success mr-1">{{ $condicion }}</span>
                                    @endforeach
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Cliente
                        </a>
                        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

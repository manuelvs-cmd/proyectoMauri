@extends('layouts.app')

@section('content-class', 'full-width')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Gestión de Facturas y Boletas</h4>
                    <a href="{{ route('facturas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Factura/Boleta
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
                                    <h5 class="card-title">{{ $facturas->count() }}</h5>
                                    <p class="card-text">Total Documentos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $facturas->where('tipo_documento', 'factura')->count() }}</h5>
                                    <p class="card-text">Facturas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $facturas->where('tipo_documento', 'boleta')->count() }}</h5>
                                    <p class="card-text">Boletas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">${{ number_format($facturas->sum('total'), 0, ',', '.') }}</h5>
                                    <p class="card-text">Total Facturado</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($facturas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>N° Documento</th>
                                        <th>Tipo</th>
                                        <th>Cliente</th>
                                        <th>Mercancía</th>
                                        <th>Fecha Emisión</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($facturas as $factura)
                                    <tr>
                                        <td>{{ $factura->numero_documento }}</td>
                                        <td>
                                            <span class="badge badge-{{ $factura->tipo_documento === 'factura' ? 'primary' : 'secondary' }}">
                                                {{ ucfirst($factura->tipo_documento) }}
                                            </span>
                                        </td>
                                        <td>{{ $factura->pedido->cliente->razon_social }}</td>
                                        <td>{{ $factura->pedido->mercancia->nombre }}</td>
                                        <td>{{ $factura->fecha_emision->format('d/m/Y') }}</td>
                                        <td>${{ number_format($factura->total, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge badge-{{ $factura->estado === 'pagada' ? 'success' : ($factura->estado === 'anulada' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($factura->estado) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('facturas.show', $factura->id) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('facturas.edit', $factura->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('facturas.preview', $factura->id) }}" class="btn btn-secondary btn-sm" target="_blank" title="Vista previa">
                                                    <i class="fas fa-search"></i>
                                                </a>
                                                <a href="{{ route('facturas.pdf', $factura->id) }}" class="btn btn-success btn-sm" title="Descargar PDF">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <form action="{{ route('facturas.destroy', $factura->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta factura?')" title="Eliminar">
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
                            <i class="fas fa-info-circle"></i> No hay facturas registradas.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Gestión de Clientes')

@section('content-class', 'full-width')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Gestión de Clientes</h4>
                    <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Cliente
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
                                    <h5 class="card-title">{{ $clientes->count() }}</h5>
                                    <p class="card-text">Total Clientes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $clientes->where('telefono', '!=', '')->count() }}</h5>
                                    <p class="card-text">Con Teléfono</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $clientes->whereNotNull('correo_electronico')->where('correo_electronico', '!=', '')->count() }}</h5>
                                    <p class="card-text">Con Email</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($clientes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>RUT</th>
                                        <th>Razón Social</th>
                                        <th>Correo</th>
                                        <th>Teléfono</th>
                                        @superadmin
                                            <th>Vendedor</th>
                                        @endsuperadmin
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clientes as $cliente)
                                        <tr>
                                            <td>{{ $cliente->rut }}</td>
                                            <td>{{ $cliente->razon_social }}</td>
                                            <td>{{ $cliente->correo_electronico ?? '-' }}</td>
                                            <td>{{ $cliente->telefono ?? '-' }}</td>
                                            @superadmin
                                                <td>
                                                    @if($cliente->user)
                                                        <span class="badge badge-light text-dark">{{ $cliente->user->name }}</span>
                                                    @else
                                                        <span class="badge badge-light text-muted">Sin asignar</span>
                                                    @endif
                                                </td>
                                            @endsuperadmin
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-warning btn-sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este cliente?')" title="Eliminar">
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
                            <i class="fas fa-info-circle"></i> No hay clientes registrados.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

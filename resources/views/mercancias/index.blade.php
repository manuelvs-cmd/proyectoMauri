@extends('layouts.app')

@section('title', 'Gestión de Mercancías')

@section('content')

<div class="container mt-5">
    <!-- Notifications -->
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

    <!-- Create Button -->
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('mercancias.create') }}" class="btn btn-success">
            <i class="fas fa-plus-circle"></i> Crear Nueva Mercancía
        </a>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Listado de Mercancías</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Cantidad</th>
                            <th>Costo</th>
                            <th>Venta</th>
                            <th>Rentabilidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mercancias as $m)
                            <tr>
                                <td>{{ $m->nombre }}</td>
                                <td>{{ $m->cantidad }}</td>
                                <td>${{ number_format($m->costo_compra, 0, ',', '.') }}</td>
                                <td>${{ number_format($m->precio_venta, 0, ',', '.') }}</td>
                                <td>{{ number_format($m->rentabilidad, 2) }}%</td>
                                <td>
                                    <a href="{{ route('mercancias.show', $m->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="{{ route('mercancias.edit', $m->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form action="{{ route('mercancias.destroy', $m->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro que deseas eliminar esta mercancía?')">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

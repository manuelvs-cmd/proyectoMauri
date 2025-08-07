@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content-class', 'wide-content')

@section('content')

<div class="container mt-4">
    <h1>Gestión de Usuarios</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Crear Nuevo Usuario</a>

    <div class="card">
        <div class="card-header">
            <strong>Lista de Usuarios</strong>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Fecha de Registro</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td><strong>{{ $user->username ?? 'N/A' }}</strong></td>
                        <td>{{ $user->email ?? 'Sin email' }}</td>
                        <td>
                            @if($user->roles->isNotEmpty())
                                <span class="badge badge-secondary role-{{ $user->roles->first()->name }}">
                                    {{ ucfirst($user->roles->first()->name) }}
                                </span>
                            @else
                                <span class="badge badge-dark role-none">Sin rol</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-sm">Ver</a>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">Editar</a>
                            @if($user->id !== Auth::id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro que deseas eliminar este usuario?')">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

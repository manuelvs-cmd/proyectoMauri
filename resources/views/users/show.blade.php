@extends('layouts.app')

@section('title', 'Detalles del Usuario')

@section('content')

<div class="container mt-4">
    <h1>Detalles del Usuario</h1>

    <div class="card">
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <strong>ID:</strong> {{ $user->id }}
                </li>
                <li class="list-group-item">
                    <strong>Nombre:</strong> {{ $user->name }}
                </li>
                <li class="list-group-item">
                    <strong>Email:</strong> {{ $user->email }}
                </li>
                <li class="list-group-item">
                    <strong>Rol:</strong> 
                    @if($user->roles->isNotEmpty())
                        <span class="badge badge-secondary role-{{ $user->roles->first()->name }}">
                            {{ ucfirst($user->roles->first()->name) }}
                        </span>
                    @else
                        <span class="badge badge-dark role-none">Sin rol</span>
                    @endif
                </li>
                <li class="list-group-item">
                    <strong>Fecha de registro:</strong> {{ $user->created_at->format('d/m/Y H:i') }}
                </li>
                <li class="list-group-item">
                    <strong>Última actualización:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}
                </li>
            </ul>

            <div class="mt-3">
                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">Editar Usuario</a>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Volver a Lista</a>
            </div>
        </div>
    </div>
</div>

@endsection

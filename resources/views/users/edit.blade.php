@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')

<div class="container mt-4">
    <h2>Editar Usuario: {{ $user->name }}</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="username">Nombre de Usuario:</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control" required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="name">Nombre:</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email">Correo electrónico (opcional):</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Nueva Contraseña (opcional):</label>
                    <input type="password" name="password" class="form-control">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirmar Nueva Contraseña:</label>
                    <input type="password" name="password_confirmation" class="form-control">
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="role">Rol:</label>
                    <select name="role" class="form-control" required>
                        <option value="">Selecciona un rol</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}"
                                {{ old('role', $user->roles->first()->name ?? '') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group d-flex justify-content-between">
                    <input type="submit" value="Actualizar Usuario" class="btn btn-success">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@extends('layouts.app')

@section('title', 'Crear Usuario')

@section('content-class', 'form-container')

@section('content')
<div class="container mt-4">
    <h2>Crear Nuevo Usuario</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                
                <div class="form-group">
                    <label for="name">Nombre:</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="username">Nombre de usuario:</label>
                    <input type="text" name="username" value="{{ old('username') }}" class="form-control" required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email">Correo electrónico (opcional):</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" name="password" class="form-control" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirmar Contraseña:</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="role">Rol:</label>
                    <select name="role" class="form-control" required>
                        <option value="">Selecciona un rol</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group d-flex justify-content-between">
                    <input type="submit" value="Crear Usuario" class="btn btn-success">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Editar Factura/Boleta</h4>
                </div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('facturas.update', $factura->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label><strong>Información de la Factura:</strong></label>
                            <div class="card">
                                <div class="card-body">
                                    <p><strong>N° Documento:</strong> {{ $factura->numero_documento }}</p>
                                    <p><strong>Tipo:</strong> {{ ucfirst($factura->tipo_documento) }}</p>
                                    <p><strong>Cliente:</strong> {{ $factura->pedido->cliente->razon_social }}</p>
                                    <p><strong>Mercancía:</strong> {{ $factura->pedido->mercancia->nombre }}</p>
                                    <p><strong>Total:</strong> ${{ number_format($factura->total, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado *</label>
                            <select name="estado" id="estado" class="form-control" required>
                                <option value="emitida" {{ $factura->estado == 'emitida' ? 'selected' : '' }}>Emitida</option>
                                <option value="pagada" {{ $factura->estado == 'pagada' ? 'selected' : '' }}>Pagada</option>
                                <option value="anulada" {{ $factura->estado == 'anulada' ? 'selected' : '' }}>Anulada</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="observaciones">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" class="form-control" rows="3">{{ $factura->observaciones }}</textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="{{ route('facturas.show', $factura->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

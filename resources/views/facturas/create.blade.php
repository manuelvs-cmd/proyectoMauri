@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Crear Nueva Factura/Boleta</h4>
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

                    <form method="POST" action="{{ route('facturas.store') }}">
                        @csrf

                        @if(isset($pedido))
                            <!-- Si viene un pedido específico -->
                            <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
                            
                            <div class="form-group">
                                <label><strong>Información del Pedido:</strong></label>
                                <div class="card">
                                    <div class="card-body">
                                        <p><strong>Cliente:</strong> {{ $pedido->cliente->razon_social }}</p>
                                        <p><strong>Mercancía:</strong> {{ $pedido->mercancia->nombre }}</p>
                                        <p><strong>Precio:</strong> ${{ number_format($pedido->calcularTotal(), 0, ',', '.') }}</p>
                                        <p><strong>Fecha de Entrega:</strong> {{ $pedido->fecha_entrega }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Si no hay pedido específico, mostrar selector -->
                            <div class="form-group">
                                <label for="pedido_id">Pedido *</label>
                                <select name="pedido_id" id="pedido_id" class="form-control" required>
                                    <option value="">Seleccione un pedido</option>
                                    @foreach($pedidos as $pedido)
                                        <option value="{{ $pedido->id }}" data-precio="{{ $pedido->calcularTotal() }}">
                                            {{ $pedido->cliente->razon_social }} - {{ $pedido->mercancia->nombre }} - ${{ number_format($pedido->calcularTotal(), 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="tipo_documento">Tipo de Documento *</label>
                            <select name="tipo_documento" id="tipo_documento" class="form-control" required>
                                <option value="">Seleccione tipo</option>
                                <option value="factura">Factura</option>
                                <option value="boleta">Boleta</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fecha_emision">Fecha de Emisión *</label>
                            <input type="date" name="fecha_emision" id="fecha_emision" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="observaciones">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Crear Factura/Boleta
                            </button>
                            <a href="{{ route('facturas.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Actualizar información del pedido cuando se selecciona uno
document.getElementById('pedido_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const precio = selectedOption.getAttribute('data-precio');
    
    if (precio) {
        console.log('Precio seleccionado:', precio);
        // Aquí puedes agregar lógica adicional si necesitas mostrar más información
    }
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'Editar Pedido')

@section('content-class', 'full-width')

@section('content')
<!-- Incluye CSS y JS de Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Editar Pedido</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('pedidos.update', $pedido) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Datos del Cliente (Solo lectura) -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-primary"><i class="fas fa-user"></i> Datos del Cliente (solo lectura)</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>RUT:</label>
                                            <input type="text" class="form-control" value="{{ $pedido->cliente->rut }}" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Razón Social:</label>
                                            <input type="text" class="form-control" value="{{ $pedido->cliente->razon_social }}" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Datos de la Mercancía (Solo lectura) -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-success"><i class="fas fa-box"></i> Datos de la Mercancía</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Mercancía:</label>
                                            <input type="text" class="form-control" value="{{ $pedido->mercancia->nombre }}" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Cantidad solicitada:</label>
                                            <input type="number" name="cantidad_solicitada" class="form-control" 
                                                   value="{{ old('cantidad_solicitada', $pedido->cantidad_solicitada ?? 1) }}" 
                                                   min="1" max="{{ $stock_disponible }}" required>
                                            <small class="form-text text-muted">
                                                Stock disponible: {{ $stock_disponible }} unidades 
                                                ({{ $pedido->mercancia->cantidad }} en stock + {{ $pedido->cantidad_solicitada ?? 1 }} del pedido actual)
                                            </small>
                                            @error('cantidad_solicitada')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                @if(auth()->user()->hasRole('superadmin'))
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Precio de Mercancía (Original):</label>
                                            <input type="text" class="form-control" value="${{ number_format($pedido->mercancia->precio_venta, 0, ',', '.') }}" disabled>
                                            <small class="form-text text-muted">Precio base registrado en el inventario</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Precio Unitario Personalizado:</label>
                                            <input type="number" name="precio_unitario" class="form-control" 
                                                   value="{{ old('precio_unitario', $pedido->precio_unitario) }}" 
                                                   min="0" step="0.01" placeholder="Dejar vacío para usar precio original">
                                            <small class="form-text text-muted">
                                                @if($pedido->tienePrecioPersonalizado())
                                                    <span class="text-info">✓ Este pedido tiene precio personalizado</span>
                                                @else
                                                    Modificar solo si necesita un precio especial para este pedido
                                                @endif
                                            </small>
                                            @error('precio_unitario')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Datos del Pedido -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-info"><i class="fas fa-clipboard-list"></i> Datos del Pedido</h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha de Entrega:</label>
                                            <input type="text" name="fecha_entrega" id="fecha_entrega" class="form-control" 
                                                   value="{{ old('fecha_entrega', $pedido->fecha_entrega) }}" required>
                                            @error('fecha_entrega')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Horario de Entrega:</label>
                                            <select name="horario_entrega" class="form-control" required>
                                                <option value="">Seleccione...</option>
                                                <option value="Mañana" {{ old('horario_entrega', $pedido->horario_entrega) == 'Mañana' ? 'selected' : '' }}>Mañana</option>
                                                <option value="Tarde" {{ old('horario_entrega', $pedido->horario_entrega) == 'Tarde' ? 'selected' : '' }}>Tarde</option>
                                            </select>
                                            @error('horario_entrega')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Dirección de Entrega:</label>
                                    <div class="input-group">
                                        <input type="text" name="direccion_entrega" id="direccion_entrega" class="form-control" 
                                               value="{{ old('direccion_entrega', $pedido->direccion_entrega) }}" required>
                                        <div class="input-group-append">
                                            <button type="button" id="actualizar_direccion" class="btn btn-outline-primary">
                                                <i class="fas fa-sync"></i> Actualizar desde cliente
                                            </button>
                                        </div>
                                    </div>
                                    @error('direccion_entrega')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Condición de pago:</label>
                                            <select name="condicion_pago" class="form-control" required>
                                                <option value="">Seleccione...</option>
                                                <option value="Pagado" {{ old('condicion_pago', $pedido->condicion_pago) == 'Pagado' ? 'selected' : '' }}>Pagado</option>
                                                <option value="Por pagar" {{ old('condicion_pago', $pedido->condicion_pago) == 'Por pagar' ? 'selected' : '' }}>Por pagar</option>
                                            </select>
                                            @error('condicion_pago')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Medio de Pago:</label>
                                            <select name="formas_pago" class="form-control" required>
                                                <option value="">Seleccione...</option>
                                                <option value="Efectivo" {{ old('formas_pago', $pedido->formas_pago) == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                                                <option value="Transferencia" {{ old('formas_pago', $pedido->formas_pago) == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                                                <option value="Tarjeta" {{ old('formas_pago', $pedido->formas_pago) == 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                            </select>
                                            @error('formas_pago')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Observación:</label>
                                    <textarea name="observacion" class="form-control" rows="3">{{ old('observacion', $pedido->observacion) }}</textarea>
                                    @error('observacion')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Actualizar Pedido
                                    </button>
                                    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    flatpickr("#fecha_entrega", {
        dateFormat: "Y-m-d",
        locale: "es"
    });
    
    // Validación en tiempo real de la cantidad
    const cantidadInput = document.querySelector('input[name="cantidad_solicitada"]');
    const stockDisponible = {{ $stock_disponible }};
    
    if (cantidadInput) {
        cantidadInput.addEventListener('input', function() {
            const valor = parseInt(this.value);
            
            if (valor > stockDisponible) {
                this.style.borderColor = 'red';
                this.setCustomValidity('La cantidad excede el stock disponible (' + stockDisponible + ' unidades)');
            } else {
                this.style.borderColor = '';
                this.setCustomValidity('');
            }
        });
    }
    
    // Funcionalidad para actualizar la dirección desde el cliente
    document.getElementById('actualizar_direccion').addEventListener('click', function() {
        const clienteId = {{ $pedido->cliente_id }};
        const direccionInput = document.getElementById('direccion_entrega');
        
        // Mostrar que se está cargando
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
        this.disabled = true;
        direccionInput.value = 'Cargando...';
        direccionInput.disabled = true;
        
        // Hacer petición AJAX para obtener la dirección
        fetch(`/pedidos/cliente/${clienteId}/direccion`)
            .then(response => response.json())
            .then(data => {
                if (data.direccion_completa) {
                    direccionInput.value = data.direccion_completa;
                } else {
                    alert('No hay dirección completa registrada para este cliente');
                }
                // Restaurar el botón
                this.innerHTML = originalText;
                this.disabled = false;
                direccionInput.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar la dirección del cliente');
                // Restaurar el botón
                this.innerHTML = originalText;
                this.disabled = false;
                direccionInput.disabled = false;
            });
    });
</script>

@endsection

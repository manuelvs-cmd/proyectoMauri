@extends('layouts.app')

@section('title', 'Crear Pedido - Múltiples Mercancías')

@section('content-class', 'full-width')

@section('content')
<!-- Incluye CSS y JS de Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<!-- Estilos personalizados -->
<style>
    .mercancia-item {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f8f9fa;
        position: relative;
    }
    
    .mercancia-item.active {
        border-color: #007bff;
        background-color: #e7f3ff;
    }
    
    .remove-mercancia {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    
    .add-mercancia-btn {
        background: #28a745;
        border: 2px dashed #28a745;
        color: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        margin: 15px 0;
    }
    
    .add-mercancia-btn:hover {
        background: #218838;
        border-color: #218838;
    }
    
    .position-relative {
        position: relative;
    }
    
    .dropdown-search {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
    }
    
    .dropdown-item {
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    .dropdown-item:last-child {
        border-bottom: none;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .total-pedido {
        background: #007bff;
        color: white;
        padding: 15px;
        border-radius: 8px;
        margin: 20px 0;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Crear Nuevo Pedido</h4>
                    <small class="text-muted">Permite seleccionar múltiples mercancías</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('pedidos.store') }}" method="POST" id="pedido-form">
                        @csrf
                        
                        <!-- Cliente -->
                        <div class="form-group">
                            <label>Cliente: <span class="text-danger">*</span></label>
                            <select name="cliente_id" class="form-control" required>
                                <option value="">-- Selecciona un cliente --</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->razon_social }} - RUT: {{ $cliente->rut }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Mercancías -->
                        <div class="form-group">
                            <label>Mercancías: <span class="text-danger">*</span></label>
                            <div id="mercancias-container">
                                <!-- Las mercancías se agregarán aquí dinámicamente -->
                            </div>
                            
                            <div class="add-mercancia-btn" id="add-mercancia-btn">
                                <i class="fas fa-plus"></i> Agregar Mercancía
                            </div>
                            
                            @error('mercancias')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Total del pedido -->
                        <div class="total-pedido" id="total-pedido" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Total de artículos: <span id="total-articulos">0</span></strong>
                                </div>
                                <div class="col-md-6 text-right">
                                    <strong>Total del pedido: $<span id="total-precio">0</span></strong>
                                </div>
                            </div>
                        </div>

                        <!-- Información del pedido -->
                        <hr>
                        <h5>Información de Entrega</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha de Entrega: <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_entrega" id="fecha_entrega" class="form-control" value="{{ old('fecha_entrega', date('Y-m-d')) }}" required>
                                    @error('fecha_entrega')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Horario de Entrega: <span class="text-danger">*</span></label>
                                    <select name="horario_entrega" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <option value="Mañana" {{ old('horario_entrega') == 'Mañana' ? 'selected' : '' }}>Mañana</option>
                                        <option value="Tarde" {{ old('horario_entrega') == 'Tarde' ? 'selected' : '' }}>Tarde</option>
                                    </select>
                                    @error('horario_entrega')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Dirección de Entrega:</label>
                            <input type="text" name="direccion_entrega" id="direccion_entrega" class="form-control" 
                                   value="{{ old('direccion_entrega') }}" 
                                   placeholder="Se llenará automáticamente al seleccionar un cliente">
                            <small class="form-text text-muted">La dirección se completará automáticamente con los datos del cliente seleccionado</small>
                            @error('direccion_entrega')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Condición de pago: <span class="text-danger">*</span></label>
                                    <select name="condicion_pago" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <option value="Pagado" {{ old('condicion_pago') == 'Pagado' ? 'selected' : '' }}>Pagado</option>
                                        <option value="Por pagar" {{ old('condicion_pago') == 'Por pagar' ? 'selected' : '' }}>Por pagar</option>
                                    </select>
                                    @error('condicion_pago')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Medio de Pago: <span class="text-danger">*</span></label>
                                    <select name="formas_pago" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <option value="Efectivo" {{ old('formas_pago') == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                                        <option value="Transferencia" {{ old('formas_pago') == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                                        <option value="Tarjeta" {{ old('formas_pago') == 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                    </select>
                                    @error('formas_pago')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Observación (opcional):</label>
                            <textarea name="observacion" class="form-control" rows="3">{{ old('observacion') }}</textarea>
                            @error('observacion')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Crear Pedido
                            </button>
                            <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let mercanciaCounter = 0;
let mercanciasData = @json($mercancias);

// Inicializar Flatpickr
flatpickr("#fecha_entrega", {
    dateFormat: "Y-m-d",
    minDate: "today",
    locale: "es"
});

// Función para llenar automáticamente la dirección de entrega
document.querySelector('select[name="cliente_id"]').addEventListener('change', function() {
    const clienteId = this.value;
    const direccionInput = document.getElementById('direccion_entrega');
    
    if (clienteId) {
        direccionInput.value = 'Cargando...';
        direccionInput.disabled = true;
        
        fetch(`/pedidos/cliente/${clienteId}/direccion`)
            .then(response => response.json())
            .then(data => {
                if (data.direccion_completa) {
                    direccionInput.value = data.direccion_completa;
                } else {
                    direccionInput.value = '';
                    direccionInput.placeholder = 'No hay dirección registrada para este cliente';
                }
                direccionInput.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                direccionInput.value = '';
                direccionInput.placeholder = 'Error al cargar la dirección';
                direccionInput.disabled = false;
            });
    } else {
        direccionInput.value = '';
        direccionInput.placeholder = 'Se llenará automáticamente al seleccionar un cliente';
        direccionInput.disabled = false;
    }
});

// Función para agregar una nueva mercancía
function agregarMercancia() {
    console.log('agregarMercancia() llamada - contador:', mercanciaCounter);
    mercanciaCounter++;
    const container = document.getElementById('mercancias-container');
    console.log('Container encontrado:', container);
    
    const mercanciaDiv = document.createElement('div');
    mercanciaDiv.className = 'mercancia-item';
    mercanciaDiv.setAttribute('data-index', mercanciaCounter);
    
    const isSupeadmin = {{ auth()->user()->hasRole('superadmin') ? 'true' : 'false' }};
    const precioColumn = isSupeadmin ? `
        <div class="col-md-3">
            <div class="form-group">
                <label>Precio Personalizado</label>
                <input type="number" name="mercancias[${mercanciaCounter}][precio_unitario]" 
                       class="form-control precio-input" min="0" step="0.01" placeholder="Opcional" 
                       data-index="${mercanciaCounter}">
                <small class="form-text text-muted precio-info">Precio normal: $-</small>
            </div>
        </div>
    ` : '';
    
    mercanciaDiv.innerHTML = `
        <button type="button" class="remove-mercancia" data-index="${mercanciaCounter}" title="Eliminar mercancía">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Mercancía <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="text" class="form-control mercancia-search" placeholder="Buscar mercancía..." 
                               data-index="${mercanciaCounter}" autocomplete="off">
                        <div class="dropdown-search" id="dropdown-${mercanciaCounter}" style="display: none;"></div>
                        <input type="hidden" name="mercancias[${mercanciaCounter}][mercancia_id]" class="mercancia-id">
                    </div>
                    <small class="form-text text-muted mercancia-info">Selecciona una mercancía</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Cantidad <span class="text-danger">*</span></label>
                    <input type="number" name="mercancias[${mercanciaCounter}][cantidad_solicitada]" 
                           class="form-control cantidad-input" min="1" value="1" required 
                           data-index="${mercanciaCounter}">
                    <small class="form-text text-muted stock-info">Stock: -</small>
                </div>
            </div>
            ${precioColumn}
        </div>
        
        <div class="row mt-2">
            <div class="col-12">
                <div class="alert alert-info mercancia-selected" style="display: none;">
                    <strong>Seleccionado:</strong> <span class="mercancia-nombre">-</span>
                    <span class="float-right subtotal">Subtotal: $<span class="subtotal-amount">0</span></span>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(mercanciaDiv);
    
    // Configurar eventos para esta nueva mercancía
    configurarEventosMercancia(mercanciaCounter);
    
    // Configurar evento del botón remover
    const removeBtn = mercanciaDiv.querySelector('.remove-mercancia');
    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            removerMercancia(mercanciaCounter);
        });
    }
    
    actualizarTotal();
}

// Función para remover una mercancía
function removerMercancia(index) {
    const mercanciaDiv = document.querySelector(`[data-index="${index}"]`);
    if (mercanciaDiv) {
        mercanciaDiv.remove();
        actualizarTotal();
    }
}

// Función para configurar eventos de una mercancía
function configurarEventosMercancia(index) {
    const searchInput = document.querySelector(`[data-index="${index}"] .mercancia-search`);
    const dropdown = document.getElementById(`dropdown-${index}`);
    const cantidadInput = document.querySelector(`[data-index="${index}"] .cantidad-input`);
    const precioInput = document.querySelector(`[data-index="${index}"] .precio-input`);
    
    // Evento de búsqueda
    let timeoutBusqueda = null;
    searchInput.addEventListener('input', function() {
        const termino = this.value.trim();
        
        if (timeoutBusqueda) {
            clearTimeout(timeoutBusqueda);
        }
        
        timeoutBusqueda = setTimeout(() => {
            buscarMercancias(termino, index);
        }, 300);
    });
    
    // Mostrar todas al hacer focus
    searchInput.addEventListener('focus', function() {
        mostrarTodasMercancias(index);
    });
    
    // Eventos de cantidad y precio
    if (cantidadInput) {
        cantidadInput.addEventListener('input', () => actualizarSubtotal(index));
    }
    if (precioInput) {
        precioInput.addEventListener('input', () => actualizarSubtotal(index));
    }
}

// Función para buscar mercancías
function buscarMercancias(termino, index) {
    const dropdown = document.getElementById(`dropdown-${index}`);
    
    if (termino.length === 0) {
        mostrarTodasMercancias(index);
        return;
    }
    
    const resultados = mercanciasData.filter(m => 
        m.nombre.toLowerCase().includes(termino.toLowerCase())
    );
    
    mostrarResultados(resultados, index);
}

// Función para mostrar todas las mercancías
function mostrarTodasMercancias(index) {
    mostrarResultados(mercanciasData, index);
}

// Función para mostrar resultados
function mostrarResultados(resultados, index) {
    const dropdown = document.getElementById(`dropdown-${index}`);
    dropdown.innerHTML = '';
    
    if (resultados.length === 0) {
        dropdown.innerHTML = '<div class="dropdown-item text-muted">No se encontraron mercancías</div>';
    } else {
        resultados.forEach(mercancia => {
            const item = document.createElement('div');
            item.className = 'dropdown-item';
            item.style.cursor = mercancia.cantidad > 0 ? 'pointer' : 'not-allowed';
            
            const stockBadge = mercancia.cantidad > 0 
                ? `<span class="badge badge-success">Stock: ${mercancia.cantidad}</span>`
                : `<span class="badge badge-danger">Sin stock</span>`;
            
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>${mercancia.nombre}</strong><br>
                        <small class="text-muted">Precio: $${new Intl.NumberFormat('es-CL').format(mercancia.precio_venta)}</small>
                    </div>
                    <div class="text-right">
                        ${stockBadge}
                    </div>
                </div>
            `;
            
            if (mercancia.cantidad > 0) {
                item.addEventListener('click', () => seleccionarMercancia(mercancia, index));
            }
            
            dropdown.appendChild(item);
        });
    }
    
    dropdown.style.display = 'block';
}

// Función para seleccionar una mercancía
function seleccionarMercancia(mercancia, index) {
    const container = document.querySelector(`[data-index="${index}"]`);
    const searchInput = container.querySelector('.mercancia-search');
    const hiddenInput = container.querySelector('.mercancia-id');
    const dropdown = document.getElementById(`dropdown-${index}`);
    const infoElement = container.querySelector('.mercancia-info');
    const stockInfo = container.querySelector('.stock-info');
    const precioInfo = container.querySelector('.precio-info');
    const selectedAlert = container.querySelector('.mercancia-selected');
    const nombreSpan = container.querySelector('.mercancia-nombre');
    const cantidadInput = container.querySelector('.cantidad-input');
    
    // Actualizar valores
    searchInput.value = mercancia.nombre;
    hiddenInput.value = mercancia.id;
    dropdown.style.display = 'none';
    
    // Actualizar información
    infoElement.textContent = `${mercancia.nombre} - $${new Intl.NumberFormat('es-CL').format(mercancia.precio_venta)}`;
    stockInfo.textContent = `Stock: ${mercancia.cantidad}`;
    if (precioInfo) {
        precioInfo.textContent = `Precio normal: $${new Intl.NumberFormat('es-CL').format(mercancia.precio_venta)}`;
    }
    
    // Configurar cantidad máxima
    cantidadInput.max = mercancia.cantidad;
    if (parseInt(cantidadInput.value) > mercancia.cantidad) {
        cantidadInput.value = mercancia.cantidad;
    }
    
    // Mostrar mercancía seleccionada
    nombreSpan.textContent = mercancia.nombre;
    selectedAlert.style.display = 'block';
    container.classList.add('active');
    
    // Actualizar subtotal
    actualizarSubtotal(index);
    actualizarTotal();
}

// Función para actualizar subtotal de una mercancía
function actualizarSubtotal(index) {
    const container = document.querySelector(`[data-index="${index}"]`);
    const mercanciaId = container.querySelector('.mercancia-id').value;
    const cantidad = parseInt(container.querySelector('.cantidad-input').value) || 0;
    const precioPersonalizado = container.querySelector('.precio-input') ? 
        parseFloat(container.querySelector('.precio-input').value) : null;
    const subtotalSpan = container.querySelector('.subtotal-amount');
    
    if (!mercanciaId) return;
    
    const mercancia = mercanciasData.find(m => m.id == mercanciaId);
    if (!mercancia) return;
    
    const precio = precioPersonalizado || mercancia.precio_venta;
    const subtotal = cantidad * precio;
    
    if (subtotalSpan) {
        subtotalSpan.textContent = new Intl.NumberFormat('es-CL').format(subtotal);
    }
    
    actualizarTotal();
}

// Función para actualizar total del pedido
function actualizarTotal() {
    let totalArticulos = 0;
    let totalPrecio = 0;
    let hayMercancias = false;
    
    document.querySelectorAll('.mercancia-item').forEach(container => {
        const mercanciaId = container.querySelector('.mercancia-id').value;
        const cantidad = parseInt(container.querySelector('.cantidad-input').value) || 0;
        const precioPersonalizado = container.querySelector('.precio-input') ? 
            parseFloat(container.querySelector('.precio-input').value) : null;
        
        if (mercanciaId && cantidad > 0) {
            hayMercancias = true;
            const mercancia = mercanciasData.find(m => m.id == mercanciaId);
            if (mercancia) {
                const precio = precioPersonalizado || mercancia.precio_venta;
                totalArticulos += cantidad;
                totalPrecio += cantidad * precio;
            }
        }
    });
    
    const totalPedidoDiv = document.getElementById('total-pedido');
    const totalArticulosSpan = document.getElementById('total-articulos');
    const totalPrecioSpan = document.getElementById('total-precio');
    
    if (hayMercancias) {
        totalPedidoDiv.style.display = 'block';
        totalArticulosSpan.textContent = totalArticulos;
        totalPrecioSpan.textContent = new Intl.NumberFormat('es-CL').format(totalPrecio);
    } else {
        totalPedidoDiv.style.display = 'none';
    }
}

// Ocultar dropdowns al hacer clic fuera
document.addEventListener('click', function(e) {
    if (!e.target.closest('.position-relative')) {
        document.querySelectorAll('.dropdown-search').forEach(dropdown => {
            dropdown.style.display = 'none';
        });
    }
});

// Agregar al menos una mercancía al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, configurando eventos...');
    
    // Configurar el botón agregar mercancía
    const addBtn = document.getElementById('add-mercancia-btn');
    if (addBtn) {
        console.log('Botón encontrado, agregando event listener');
        addBtn.addEventListener('click', agregarMercancia);
    } else {
        console.error('Botón add-mercancia-btn no encontrado');
    }
    
    // Agregar la primera mercancía
    agregarMercancia();
});
</script>

@endsection

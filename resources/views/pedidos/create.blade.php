@extends('layouts.app')

@section('title', 'Crear Pedido')

@section('content-class', 'full-width')

@section('content')
<!-- Incluye CSS y JS de Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<!-- Estilos personalizados para la búsqueda de mercancías -->
<style>
    .position-relative {
        position: relative;
    }
    
    #resultados_busqueda {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
    }
    
    .dropdown-item {
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
        transition: background-color 0.2s;
    }
    
    .dropdown-item:last-child {
        border-bottom: none;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    .dropdown-item.text-muted {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }
    
    #buscar_mercancia:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    
    .dropdown-header {
        padding: 8px 15px;
        margin: 0;
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .dropdown-divider {
        height: 0;
        margin: 0.5rem 0;
        overflow: hidden;
        border-top: 1px solid #e9ecef;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    #limpiar_busqueda {
        border-left: none;
    }
    
    .input-group-append .btn:not(:last-child) {
        border-right: none;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Crear Nuevo Pedido</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('pedidos.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Cliente:</label>
                            <select name="cliente_id" class="form-control" required>
                                <option value="">-- Selecciona un cliente --</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">
                                        {{ $cliente->razon_social }} - RUT: {{ $cliente->rut }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Mercancía:</label>
                            <div class="position-relative">
                                <!-- Campo de búsqueda con botón para mostrar todas -->
                                <div class="input-group">
                                    <input type="text" id="buscar_mercancia" class="form-control" placeholder="Buscar mercancía por nombre o ver todas..." autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="button" id="mostrar_todas" class="btn btn-outline-secondary" title="Mostrar todas las mercancías">
                                            <i class="fas fa-list"></i> Ver todas
                                        </button>
                                        <button type="button" id="limpiar_busqueda" class="btn btn-outline-danger" title="Limpiar búsqueda" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Select oculto para almacenar el valor -->
                                <select name="mercancia_id" id="mercancia_id" class="form-control d-none" required>
                                    <option value="">-- Selecciona una mercancía --</option>
                                    @foreach ($mercancias as $mercancia)
                                        <option value="{{ $mercancia->id }}">
                                            {{ $mercancia->nombre }} - Stock: {{ $mercancia->cantidad ?? 0 }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                <!-- Dropdown de resultados -->
                                <div id="resultados_busqueda" class="dropdown-menu" style="width: 100%; max-height: 300px; overflow-y: auto; display: none;">
                                    <!-- Los resultados se cargarán aquí dinámicamente -->
                                </div>
                                
                                <!-- Mercancía seleccionada -->
                                <div id="mercancia_seleccionada" class="alert alert-info mt-2" style="display: none;">
                                    <strong>Mercancía seleccionada:</strong> <span id="nombre_seleccionada"></span><br>
                                    <small><span id="detalles_seleccionada"></span></small>
                                    <button type="button" class="btn btn-sm btn-outline-secondary float-right" onclick="limpiarSeleccionMercancia()">Cambiar</button>
                                </div>
                            </div>
                            @error('mercancia_id')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cantidad solicitada:</label>
                                    <input type="number" name="cantidad_solicitada" id="cantidad_solicitada" class="form-control" value="{{ old('cantidad_solicitada', 1) }}" min="1" required>
                                    <small id="stock_info" class="form-text text-muted">Selecciona una mercancía para ver el stock disponible</small>
                                    @error('cantidad_solicitada')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                    @error('error')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @if(auth()->user()->hasRole('superadmin'))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Precio Unitario Personalizado (opcional):</label>
                                    <input type="number" name="precio_unitario" id="precio_unitario" class="form-control" value="{{ old('precio_unitario') }}" min="0" step="0.01" placeholder="Dejar vacío para usar precio de mercancía">
                                    <small id="precio_info" class="form-text text-muted">Precio normal: <span id="precio_mercancia">-</span></small>
                                    @error('precio_unitario')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha de Entrega:</label>
                                    <input type="date" name="fecha_entrega" id="fecha_entrega" class="form-control" value="{{ old('fecha_entrega', date('Y-m-d')) }}" required>
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
                                        <option value="Mañana">Mañana</option>
                                        <option value="Tarde">Tarde</option>
                                    </select>
                                    @error('horario_entrega')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Dirección de Entrega:</label>
                            <input type="text" name="direccion_entrega" id="direccion_entrega" class="form-control" placeholder="Se llenará automáticamente al seleccionar un cliente">
                            <small class="form-text text-muted">La dirección se completará automáticamente con los datos del cliente seleccionado</small>
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
                                        <option value="Pagado">Pagado</option>
                                        <option value="Por pagar">Por pagar</option>
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
                                        <option value="Efectivo">Efectivo</option>
                                        <option value="Transferencia">Transferencia</option>
                                        <option value="Tarjeta">Tarjeta</option>
                                    </select>
                                    @error('formas_pago')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Observación (opcional):</label>
                            <textarea name="observacion" class="form-control" rows="3"></textarea>
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
                // Mostrar que se está cargando
                direccionInput.value = 'Cargando...';
                direccionInput.disabled = true;
                
                // Hacer petición AJAX para obtener la dirección
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
                // Limpiar el campo si no hay cliente seleccionado
                direccionInput.value = '';
                direccionInput.placeholder = 'Se llenará automáticamente al seleccionar un cliente';
                direccionInput.disabled = false;
            }
        });
        
        // Variables globales para el manejo de búsqueda
        let timeoutBusqueda = null;
        let mercanciaSeleccionada = null;
        
        // Función para buscar mercancías
        function buscarMercancias(termino, mostrarTodas = false) {
            let url = '/pedidos/buscar-mercancias';
            let params = new URLSearchParams();
            
            if (mostrarTodas) {
                params.append('all', 'true');
            } else if (termino && termino.length > 0) {
                params.append('q', termino);
            } else {
                // Si no hay término y no se pide mostrar todas, ocultar dropdown
                document.getElementById('resultados_busqueda').style.display = 'none';
                return;
            }
            
            if (params.toString()) {
                url += '?' + params.toString();
            }
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    mostrarResultados(data, mostrarTodas, termino);
                })
                .catch(error => {
                    console.error('Error en búsqueda:', error);
                    const dropdown = document.getElementById('resultados_busqueda');
                    dropdown.innerHTML = '<div class="dropdown-item text-danger">Error al cargar mercancías</div>';
                    dropdown.style.display = 'block';
                });
        }
        
        // Función para mostrar los resultados
        function mostrarResultados(data, mostrarTodas, termino) {
            const dropdown = document.getElementById('resultados_busqueda');
            dropdown.innerHTML = '';
            
            // Agregar encabezado informativo
            const header = document.createElement('div');
            header.className = 'dropdown-header';
            header.style.backgroundColor = '#f8f9fa';
            header.style.borderBottom = '1px solid #dee2e6';
            header.style.fontWeight = 'bold';
            
            if (mostrarTodas) {
                header.textContent = `Todas las mercancías (${data.length})`;
            } else if (termino) {
                header.textContent = `Resultados para "${termino}" (${data.length})`;
            }
            dropdown.appendChild(header);
            
            if (data.length === 0) {
                const noResults = document.createElement('div');
                noResults.className = 'dropdown-item text-muted';
                noResults.textContent = mostrarTodas ? 'No hay mercancías registradas' : 'No se encontraron mercancías';
                dropdown.appendChild(noResults);
            } else {
                // Separar mercancías disponibles y sin stock
                const disponibles = data.filter(m => m.disponible);
                const sinStock = data.filter(m => !m.disponible);
                
                // Mostrar primero las disponibles
                if (disponibles.length > 0) {
                    disponibles.forEach(mercancia => {
                        dropdown.appendChild(crearItemMercancia(mercancia, true));
                    });
                }
                
                // Luego las sin stock, si las hay
                if (sinStock.length > 0) {
                    if (disponibles.length > 0) {
                        const separator = document.createElement('div');
                        separator.className = 'dropdown-divider';
                        dropdown.appendChild(separator);
                        
                        const sinStockHeader = document.createElement('div');
                        sinStockHeader.className = 'dropdown-header text-muted';
                        sinStockHeader.textContent = 'Sin stock';
                        dropdown.appendChild(sinStockHeader);
                    }
                    
                    sinStock.forEach(mercancia => {
                        dropdown.appendChild(crearItemMercancia(mercancia, false));
                    });
                }
            }
            
            dropdown.style.display = 'block';
        }
        
        // Función para crear un item de mercancía
        function crearItemMercancia(mercancia, disponible) {
            const item = document.createElement('div');
            item.className = 'dropdown-item ' + (disponible ? '' : 'text-muted');
            item.style.cursor = disponible ? 'pointer' : 'not-allowed';
            
            const stockBadge = disponible 
                ? `<span class="badge badge-success">Stock: ${mercancia.stock}</span>`
                : `<span class="badge badge-danger">Sin stock</span>`;
                
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>${mercancia.nombre}</strong>
                        <br>
                        <small class="text-muted">Precio: $${new Intl.NumberFormat('es-CL').format(mercancia.precio_venta)}</small>
                    </div>
                    <div class="text-right">
                        ${stockBadge}
                    </div>
                </div>
            `;
            
            if (disponible) {
                item.addEventListener('click', () => seleccionarMercancia(mercancia));
                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#e9ecef';
                });
                item.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            }
            
            return item;
        }
        
        // Función para seleccionar una mercancía
        function seleccionarMercancia(mercancia) {
            mercanciaSeleccionada = mercancia;
            
            // Actualizar el select oculto
            document.getElementById('mercancia_id').value = mercancia.id;
            
            // Ocultar el campo de búsqueda y dropdown
            document.getElementById('buscar_mercancia').style.display = 'none';
            document.getElementById('resultados_busqueda').style.display = 'none';
            
            // Mostrar la mercancía seleccionada
            document.getElementById('nombre_seleccionada').textContent = mercancia.nombre;
            document.getElementById('detalles_seleccionada').textContent = 
                `Stock: ${mercancia.stock} unidades - Precio: $${new Intl.NumberFormat('es-CL').format(mercancia.precio_venta)}`;
            document.getElementById('mercancia_seleccionada').style.display = 'block';
            
            // Actualizar información de stock
            actualizarStockInfo(mercancia);
        }
        
        // Función para limpiar la selección
        function limpiarSeleccionMercancia() {
            mercanciaSeleccionada = null;
            document.getElementById('mercancia_id').value = '';
            document.getElementById('buscar_mercancia').value = '';
            document.getElementById('buscar_mercancia').style.display = 'block';
            document.getElementById('mercancia_seleccionada').style.display = 'none';
            document.getElementById('resultados_busqueda').style.display = 'none';
            
            // Limpiar información de stock
            const cantidadInput = document.getElementById('cantidad_solicitada');
            const stockInfo = document.getElementById('stock_info');
            const precioMercanciaSpan = document.getElementById('precio_mercancia');
            
            cantidadInput.max = '';
            cantidadInput.disabled = false;
            cantidadInput.value = 1;
            stockInfo.textContent = 'Selecciona una mercancía para ver el stock disponible';
            stockInfo.style.color = '#666';
            if (precioMercanciaSpan) precioMercanciaSpan.textContent = '-';
        }
        
        // Función para actualizar información de stock
        function actualizarStockInfo(mercancia) {
            const cantidadInput = document.getElementById('cantidad_solicitada');
            const stockInfo = document.getElementById('stock_info');
            const precioMercanciaSpan = document.getElementById('precio_mercancia');
            
            cantidadInput.max = mercancia.stock;
            cantidadInput.disabled = false;
            
            // Mostrar información del precio
            if (precioMercanciaSpan && mercancia.precio_venta) {
                precioMercanciaSpan.textContent = `$${new Intl.NumberFormat('es-CL').format(mercancia.precio_venta)}`;
            }
            
            // Mostrar información del stock
            if (mercancia.stock > 0) {
                stockInfo.textContent = `Stock disponible: ${mercancia.stock} unidades`;
                stockInfo.style.color = '#666';
            } else {
                stockInfo.textContent = '⚠️ Sin stock disponible';
                stockInfo.style.color = 'red';
                cantidadInput.disabled = true;
                cantidadInput.value = 0;
            }
            
            // Si la cantidad actual es mayor al stock, ajustarla
            if (parseInt(cantidadInput.value) > mercancia.stock) {
                cantidadInput.value = mercancia.stock;
            }
        }
        
        // Event listeners para el campo de búsqueda
        document.getElementById('buscar_mercancia').addEventListener('input', function() {
            const termino = this.value.trim();
            
            // Mostrar/ocultar botón de limpiar
            const btnLimpiar = document.getElementById('limpiar_busqueda');
            if (termino.length > 0) {
                btnLimpiar.style.display = 'block';
            } else {
                btnLimpiar.style.display = 'none';
            }
            
            // Cancelar timeout anterior
            if (timeoutBusqueda) {
                clearTimeout(timeoutBusqueda);
            }
            
            // Establecer nuevo timeout para evitar muchas peticiones
            timeoutBusqueda = setTimeout(() => {
                buscarMercancias(termino);
            }, 300);
        });
        
        // Event listener para el botón "Ver todas"
        document.getElementById('mostrar_todas').addEventListener('click', function() {
            buscarMercancias('', true);
            document.getElementById('buscar_mercancia').focus();
        });
        
        // Event listener para el botón "Limpiar búsqueda"
        document.getElementById('limpiar_busqueda').addEventListener('click', function() {
            document.getElementById('buscar_mercancia').value = '';
            document.getElementById('resultados_busqueda').style.display = 'none';
            this.style.display = 'none';
            document.getElementById('buscar_mercancia').focus();
        });
        
        // Mostrar dropdown al hacer focus en el campo de búsqueda
        document.getElementById('buscar_mercancia').addEventListener('focus', function() {
            const termino = this.value.trim();
            if (termino.length === 0) {
                // Si no hay texto, mostrar todas las mercancías
                buscarMercancias('', true);
            } else {
                // Si hay texto, buscar según el término
                buscarMercancias(termino);
            }
        });
        
        // Ocultar dropdown cuando se hace clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#buscar_mercancia') && 
                !e.target.closest('#resultados_busqueda') && 
                !e.target.closest('#mostrar_todas') && 
                !e.target.closest('#limpiar_busqueda')) {
                document.getElementById('resultados_busqueda').style.display = 'none';
            }
        });
        
        // Manejar teclas en el campo de búsqueda
        document.getElementById('buscar_mercancia').addEventListener('keydown', function(e) {
            const dropdown = document.getElementById('resultados_busqueda');
            
            if (e.key === 'Escape') {
                dropdown.style.display = 'none';
                this.blur();
            }
            
            // TODO: Implementar navegación con flechas si se desea
        });
        
        // Función legacy para mantener compatibilidad (si es necesaria)
        document.getElementById('mercancia_id').addEventListener('change', function() {
            // Esta función ahora será manejada por seleccionarMercancia
            // Se mantiene por compatibilidad pero generalmente no se ejecutará
        });
        
        // Validación en tiempo real de la cantidad
        document.getElementById('cantidad_solicitada').addEventListener('input', function() {
            const maxStock = parseInt(this.max);
            const currentValue = parseInt(this.value);
            const stockInfo = document.getElementById('stock_info');
            
            if (maxStock && currentValue > maxStock) {
                this.style.borderColor = 'red';
                stockInfo.textContent = `⚠️ La cantidad excede el stock disponible (${maxStock} unidades)`;
                stockInfo.style.color = 'red';
            } else if (maxStock) {
                this.style.borderColor = '';
                stockInfo.textContent = `Stock disponible: ${maxStock} unidades`;
                stockInfo.style.color = '#666';
            }
        });
    </script>

@endsection

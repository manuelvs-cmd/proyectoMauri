@extends('layouts.app')

@section('title', 'Dashboard - Resumen del Día')

@section('content-class', 'full-width')

@section('content')

    <div class="dashboard-container">
        <div class="dashboard-header">
            <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="resumen-tab" data-bs-toggle="tab" data-bs-target="#resumen" type="button" role="tab" aria-controls="resumen" aria-selected="true">Resumen del Día</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ventas-vendedor-tab" data-bs-toggle="tab" data-bs-target="#ventas-vendedor" type="button" role="tab" aria-controls="ventas-vendedor" aria-selected="false">
                        @if(auth()->user()->hasRole('superadmin'))
                            Ventas por Vendedor
                        @else
                            Mis Ventas Detalladas
                        @endif
                    </button>
                </li>
                @if(auth()->user()->hasRole('superadmin'))
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="comisiones-tab" data-bs-toggle="tab" data-bs-target="#comisiones" type="button" role="tab" aria-controls="comisiones" aria-selected="false">Comisiones Acumuladas</button>
                    </li>
                @else
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="mis-ventas-tab" data-bs-toggle="tab" data-bs-target="#mis-ventas" type="button" role="tab" aria-controls="mis-ventas" aria-selected="false">Mis Ventas</button>
                    </li>
                @endif
            </ul>
            
            <!-- Filtro de fecha -->
            <div class="date-filter">
                <form method="GET" action="{{ route('dashboard') }}" class="filter-form">
                    <div class="form-group">
                        <label for="fecha">📅 Seleccionar fecha:</label>
                        <input 
                            type="date" 
                            id="fecha" 
                            name="fecha" 
                            value="{{ $fechaSeleccionada }}" 
                            class="date-input"
                            onchange="this.form.submit()"
                        >
                    </div>
                    <div class="quick-filters">
                        <a href="{{ route('dashboard', ['fecha' => \Carbon\Carbon::today()->format('Y-m-d')]) }}" 
                           class="quick-btn {{ $fechaSeleccionada == \Carbon\Carbon::today()->format('Y-m-d') ? 'active' : '' }}">
                            Hoy
                        </a>
                        <a href="{{ route('dashboard', ['fecha' => \Carbon\Carbon::tomorrow()->format('Y-m-d')]) }}" 
                           class="quick-btn {{ $fechaSeleccionada == \Carbon\Carbon::tomorrow()->format('Y-m-d') ? 'active' : '' }}">
                            Mañana
                        </a>
                        <a href="{{ route('dashboard', ['fecha' => \Carbon\Carbon::yesterday()->format('Y-m-d')]) }}" 
                           class="quick-btn {{ $fechaSeleccionada == \Carbon\Carbon::yesterday()->format('Y-m-d') ? 'active' : '' }}">
                            Ayer
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Botones para PDFs -->
            <div class="pdf-buttons">
                <div class="pdf-section">
                    <h3>📄 Reportes PDF</h3>
                    <div class="buttons-group">
                        <a href="{{ route('dashboard.pedidos-pdf', ['fecha' => $fechaSeleccionada, 'horario' => 'todos']) }}" class="btn-pdf btn-pedidos" target="_blank">
                            <i class="fas fa-file-pdf"></i>
                            Pedidos (Todos)
                        </a>
                        <a href="{{ route('dashboard.pedidos-pdf', ['fecha' => $fechaSeleccionada, 'horario' => 'Mañana']) }}" class="btn-pdf btn-pedidos" target="_blank">
                            <i class="fas fa-file-pdf"></i>
                            Pedidos (Mañana)
                        </a>
                        <a href="{{ route('dashboard.pedidos-pdf', ['fecha' => $fechaSeleccionada, 'horario' => 'Tarde']) }}" class="btn-pdf btn-pedidos" target="_blank">
                            <i class="fas fa-file-pdf"></i>
                            Pedidos (Tarde)
                        </a>
                        <a href="{{ route('dashboard.mercancias-pdf', ['fecha' => $fechaSeleccionada, 'horario' => 'todos']) }}" class="btn-pdf btn-mercancias" target="_blank">
                            <i class="fas fa-file-pdf"></i>
                            Mercancías (Todos)
                        </a>
                        <a href="{{ route('dashboard.mercancias-pdf', ['fecha' => $fechaSeleccionada, 'horario' => 'Mañana']) }}" class="btn-pdf btn-mercancias" target="_blank">
                            <i class="fas fa-file-pdf"></i>
                            Mercancías (Mañana)
                        </a>
                        <a href="{{ route('dashboard.mercancias-pdf', ['fecha' => $fechaSeleccionada, 'horario' => 'Tarde']) }}" class="btn-pdf btn-mercancias" target="_blank">
                            <i class="fas fa-file-pdf"></i>
                            Mercancías (Tarde)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="dashboardTabContent">
            <div class="tab-pane fade show active" id="resumen" role="tabpanel" aria-labelledby="resumen-tab">
                <!-- Aquí va todo el contenido de Resumen del Día -->
                <h2>📅 Resumen para {{ $fechaCarbon->format('d/m/Y') }}</h2>
                <div class="estadisticas">
                    <div class="stat-card">
                        <h3>{{ $estadisticas['total_pedidos'] }}</h3>
                        <p>Pedidos Hoy</p>
                    </div>
                    <div class="stat-card">
                        <h3>{{ $estadisticas['total_mercancias'] }}</h3>
                        <p>Mercancías Solicitadas</p>
                    </div>
                    <div class="stat-card">
                        <h3>${{ number_format($estadisticas['valor_total_pedidos'], 0, ',', '.') }}</h3>
                        <p>Valor Total Pedidos</p>
                    </div>
                    <div class="stat-card">
                        <h3>{{ $estadisticas['pedidos_manana'] }} / {{ $estadisticas['pedidos_tarde'] }}</h3>
                        <p>Mañana / Tarde</p>
                    </div>
                </div>
                
                <!-- Contenido principal en dos columnas -->
                <div class="dashboard-content">
                    <!-- Columna izquierda: Pedidos de la fecha seleccionada -->
                    <div class="column-left">
                        <div class="pedidos-header">
                            <h2>📅 Pedidos para {{ $fechaCarbon->format('d/m/Y') }}</h2>
                            
                            <!-- Filtros de horario -->
                            <div class="horario-filters">
                                <button class="horario-filter-btn active" data-horario="todos">Todos ({{ $pedidosHoy->count() }})</button>
                                <button class="horario-filter-btn" data-horario="Mañana">🌅 Mañana ({{ $estadisticas['pedidos_manana'] }})</button>
                                <button class="horario-filter-btn" data-horario="Tarde">🌇 Tarde ({{ $estadisticas['pedidos_tarde'] }})</button>
                            </div>
                        </div>
                        
                        @if($pedidosHoy->count() > 0)
                            <div class="pedidos-list">
                                @foreach($pedidosHoy as $pedido)
                                    <div class="pedido-card" data-horario="{{ $pedido->horario_entrega }}">
                                        <div class="pedido-header">
                                            <span class="cliente">{{ $pedido->cliente->razon_social }}</span>
                                            <span class="horario {{ $pedido->horario_entrega == 'Mañana' ? 'horario-manana' : 'horario-tarde' }}">
                                                {{ $pedido->horario_entrega }}
                                            </span>
                                        </div>
                                        <div class="pedido-body">
                                            <div class="mercancia">
                                                <strong>{{ $pedido->mercancia->nombre }}</strong>
                                                <span class="cantidad">Cantidad: {{ $pedido->cantidad_solicitada ?? 1 }}</span>
                                            </div>
                                            <div class="direccion">
                                                📍 {{ $pedido->direccion_entrega }}
                                            </div>
                                            <div class="detalles">
                                                <span class="total">${{ number_format($pedido->calcularTotal(), 0, ',', '.') }}</span>
                                                <span class="pago">{{ $pedido->condicion_pago }}</span>
                                            </div>
                                        </div>
                                        <div class="pedido-actions">
                                            <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn-small">Ver</a>
                                            <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn-small">Editar</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="no-data">
                                <p>🎉 No hay pedidos programados para {{ $fechaCarbon->format('d/m/Y') }}</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Columna derecha: Mercancías solicitadas en la fecha seleccionada -->
                    <div class="column-right">
                        <div class="mercancias-header">
                            <h2>📦 Mercancías Solicitadas {{ $fechaCarbon->format('d/m/Y') }}</h2>
                            
                            <!-- Filtros de horario para mercancías -->
                            <div class="horario-filters">
                                <button class="horario-filter-btn-mercancias active" data-horario="todos">Todos ({{ $mercanciasHoy->count() }})</button>
                                <button class="horario-filter-btn-mercancias" data-horario="Mañana">🌅 Mañana ({{ $mercanciasManana->count() }})</button>
                                <button class="horario-filter-btn-mercancias" data-horario="Tarde">🌇 Tarde ({{ $mercanciasTarde->count() }})</button>
                            </div>
                        </div>
                        
                        @if($mercanciasHoy->count() > 0)
                            <!-- Mostrar todas las mercancías (por defecto) -->
                            <div class="mercancias-list todos-mercancias" data-horario-mercancias="todos">
                                @each('partials.mercancia-card', $mercanciasHoy, 'mercancia')
                            </div>
                            
                            <!-- Mostrar mercancías de Mañana -->
                            @if($mercanciasManana->count() > 0)
                                <div class="mercancias-list manana-mercancias" data-horario-mercancias="Mañana" style="display: none;">
                                    @each('partials.mercancia-card', $mercanciasManana, 'mercancia')
                                </div>
                            @endif
                            
                            <!-- Mostrar mercancías de Tarde -->
                            @if($mercanciasTarde->count() > 0)
                                <div class="mercancias-list tarde-mercancias" data-horario-mercancias="Tarde" style="display: none;">
                                    @each('partials.mercancia-card', $mercanciasTarde, 'mercancia')
                                </div>
                            @endif
                        @else
                            <div class="no-data">
                                <p>📦 No hay mercancías solicitadas para {{ $fechaCarbon->format('d/m/Y') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- Nueva pestaña de Ventas por Vendedor --}}
            <div class="tab-pane fade" id="ventas-vendedor" role="tabpanel" aria-labelledby="ventas-vendedor-tab">
                <div class="ventas-vendedor-container">
                    <div class="ventas-header">
                        <h2>🏪 @if($ventasPorVendedor['es_admin']) Ventas por Vendedor @else Mis Ventas Detalladas @endif - {{ ucfirst($ventasPorVendedor['mes_actual']) }}</h2>
                        
                        @if($ventasPorVendedor['es_admin'] && $ventasPorVendedor['vendedores_disponibles']->count() > 1)
                            {{-- Filtro por vendedor (solo para administradores) --}}
                            <div class="vendedor-filter">
                                <form method="GET" action="{{ route('dashboard') }}" class="filter-form-vendedor">
                                    <input type="hidden" name="fecha" value="{{ $fechaSeleccionada }}">
                                    <input type="hidden" name="tab" value="ventas-vendedor">
                                    <div class="form-group">
                                        <label for="vendedor_id">👤 Filtrar por vendedor:</label>
                                        <select name="vendedor_id" id="vendedor_id" class="vendedor-select" onchange="this.form.submit()">
                                            <option value="">Todos los vendedores</option>
                                            @foreach($ventasPorVendedor['vendedores_disponibles'] as $vendedor)
                                                <option value="{{ $vendedor->id }}" 
                                                    {{ $ventasPorVendedor['vendedor_seleccionado'] == $vendedor->id ? 'selected' : '' }}>
                                                    {{ $vendedor->name }} ({{ $vendedor->username }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                            </div>
                        @endif
                        
                        @if($ventasPorVendedor['es_admin'])
                            {{-- Botón para generar PDF (solo para administradores) --}}
                            <div class="pdf-button-ventas">
                                <a href="{{ route('dashboard.ventas-vendedor-pdf', array_merge(
                                    ['fecha' => $fechaSeleccionada], 
                                    $ventasPorVendedor['vendedor_seleccionado'] ? ['vendedor_id' => $ventasPorVendedor['vendedor_seleccionado']] : []
                                )) }}" 
                                   class="btn-pdf btn-ventas" target="_blank" title="Descargar PDF de ventas">
                                    <i class="fas fa-file-pdf"></i>
                                    @if($ventasPorVendedor['vendedor_seleccionado'])
                                        PDF Vendedor Seleccionado
                                    @else
                                        PDF Todos los Vendedores
                                    @endif
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Resumen total (solo para administradores con múltiples vendedores) --}}
                    @if($ventasPorVendedor['es_admin'] && !$ventasPorVendedor['vendedor_seleccionado'])
                        <div class="resumen-total">
                            <div class="estadisticas-ventas">
                                <div class="stat-card stat-vendedores">
                                    <h3>{{ $ventasPorVendedor['resumen_total']['total_vendedores'] }}</h3>
                                    <p>Vendedores Activos</p>
                                </div>
                                <div class="stat-card stat-pedidos">
                                    <h3>{{ $ventasPorVendedor['resumen_total']['total_pedidos'] }}</h3>
                                    <p>Total Pedidos</p>
                                </div>
                                <div class="stat-card stat-neto">
                                    <h3>${{ number_format($ventasPorVendedor['resumen_total']['total_neto'], 0, ',', '.') }}</h3>
                                    <p>Total Bruto (con IVA)</p>
                                </div>
                                <div class="stat-card stat-bruto">
                                    <h3>${{ number_format($ventasPorVendedor['resumen_total']['total_bruto'], 0, ',', '.') }}</h3>
                                    <p>Total Neto (sin IVA)</p>
                                </div>
                                <div class="stat-card stat-costos">
                                    <h3>${{ number_format($ventasPorVendedor['resumen_total']['total_costos'], 0, ',', '.') }}</h3>
                                    <p>Total Costos</p>
                                </div>
                                <div class="stat-card stat-rentabilidad">
                                    <h3>{{ number_format($ventasPorVendedor['resumen_total']['rentabilidad_general'], 1) }}%</h3>
                                    <p>Rentabilidad</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    {{-- Contenido de ventas por vendedor --}}
                    @if(count($ventasPorVendedor['ventas']) > 0)
                        <div class="ventas-list">
                            @foreach($ventasPorVendedor['ventas'] as $ventaVendedor)
                                <div class="vendedor-ventas-card">
                                    {{-- Header del vendedor --}}
                                    <div class="vendedor-header">
                                        <div class="vendedor-info">
                                            <h3>{{ $ventaVendedor['vendedor_nombre'] }}</h3>
                                            <span class="vendedor-username">{{ $ventaVendedor['vendedor_username'] }}</span>
                                        </div>
                                        <div class="vendedor-stats">
                                            <div class="stat-item">
                                                <span class="stat-number">{{ $ventaVendedor['total_pedidos'] }}</span>
                                                <span class="stat-label">Pedidos</span>
                                            </div>
                                            <div class="stat-item">
                                                <span class="stat-number">${{ number_format($ventaVendedor['total_neto'], 0, ',', '.') }}</span>
                                                <span class="stat-label">Total Bruto</span>
                                            </div>
                                            @if($ventasPorVendedor['es_admin'])
                                                <div class="stat-item">
                                                    <span class="stat-number">${{ number_format($ventaVendedor['total_bruto'], 0, ',', '.') }}</span>
                                                    <span class="stat-label">Total Neto</span>
                                                </div>
                                                <div class="stat-item">
                                                    <span class="stat-number">{{ number_format($ventaVendedor['rentabilidad_porcentaje'], 1) }}%</span>
                                                    <span class="stat-label">Rentabilidad</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    {{-- Métricas del vendedor --}}
                                    <div class="vendedor-metrics">
                                        <div class="metric-item">
                                            <i class="fas fa-chart-line"></i>
                                            <span>Promedio por pedido: ${{ number_format($ventaVendedor['promedio_por_pedido'], 0, ',', '.') }}</span>
                                        </div>
                                        @if($ventasPorVendedor['es_admin'])
                                            <div class="metric-item">
                                                <i class="fas fa-dollar-sign"></i>
                                                <span>Total costos: ${{ number_format($ventaVendedor['total_costos'], 0, ',', '.') }}</span>
                                            </div>
                                            <div class="metric-item">
                                                <i class="fas fa-chart-bar"></i>
                                                <span>Ganancia neta: ${{ number_format($ventaVendedor['ganancia_neta'], 0, ',', '.') }}</span>
                                            </div>
                                            <div class="metric-item">
                                                <i class="fas fa-percentage"></i>
                                                <span>Comisión estimada: ${{ number_format($ventaVendedor['comision_estimada'], 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                        <div class="metric-item">
                                            <i class="fas fa-users"></i>
                                            <span>{{ $ventaVendedor['clientes_unicos'] }} clientes únicos</span>
                                        </div>
                                        <div class="metric-item">
                                            <i class="fas fa-boxes"></i>
                                            <span>{{ $ventaVendedor['productos_unicos'] }} productos diferentes</span>
                                        </div>
                                        <div class="metric-item">
                                            <i class="fas fa-clock"></i>
                                            <span>{{ $ventaVendedor['pedidos_manana'] }} mañana / {{ $ventaVendedor['pedidos_tarde'] }} tarde</span>
                                        </div>
                                    </div>
                                    
                                    {{-- Detalles de pedidos --}}
                                    <div class="pedidos-detalle-vendedor">
                                        <h4>📋 Detalle de pedidos:</h4>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Cliente</th>
                                                        <th>Producto</th>
                                                        <th>Cant.</th>
                                                        <th>Precio Unit.</th>
                                                        <th>Total</th>
                                                        <th>Horario</th>
                                                        <th>Pago</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($ventaVendedor['pedidos'] as $pedido)
                                                        <tr class="{{ $pedido['tiene_precio_personalizado'] ? 'precio-personalizado' : '' }}">
                                                            <td>
                                                                <div class="cliente-info">
                                                                    <strong>{{ $pedido['cliente'] }}</strong>
                                                                    <small class="text-muted d-block">{{ $pedido['cliente_rut'] }}</small>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="producto-info">
                                                                    {{ $pedido['mercancia'] }}
                                                                    @if($pedido['tiene_precio_personalizado'])
                                                                        <i class="fas fa-star text-warning" title="Precio personalizado"></i>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="text-center">{{ $pedido['cantidad'] }}</td>
                                                            <td>
                                                                @if($pedido['tiene_multiples_productos'])
                                                                    <span class="precios-individuales" title="Precios individuales de cada producto">
                                                                        {{ $pedido['precios_unitarios_formateados'] }}
                                                                    </span>
                                                                @elseif($pedido['tiene_precio_personalizado'])
                                                                    <div class="precio-info">
                                                                        <span class="precio-personalizado-texto">${{ number_format($pedido['precio_unitario'], 0, ',', '.') }}</span>
                                                                        <small class="text-muted d-block">Base: ${{ number_format($pedido['precio_base'], 0, ',', '.') }}</small>
                                                                    </div>
                                                                @else
                                                                    ${{ number_format($pedido['precio_unitario'], 0, ',', '.') }}
                                                                @endif
                                                            </td>
                                                            <td class="text-right">
                                                                <strong class="total-pedido {{ $pedido['tiene_precio_personalizado'] ? 'precio-personalizado-total' : '' }}">
                                                                    ${{ number_format($pedido['total'], 0, ',', '.') }}
                                                                </strong>
                                                            </td>
                                                            <td>
                                                                <span class="horario {{ $pedido['horario_entrega'] == 'Mañana' ? 'horario-manana' : 'horario-tarde' }}">
                                                                    {{ $pedido['horario_entrega'] }}
                                                                </span>
                                                            </td>
                                                            <td class="condicion-pago">{{ $pedido['condicion_pago'] }}</td>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <a href="{{ route('pedidos.show', $pedido['id']) }}" class="btn btn-sm btn-outline-primary" title="Ver pedido">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('pedidos.edit', $pedido['id']) }}" class="btn btn-sm btn-outline-secondary" title="Editar pedido">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-data">
                            <p>🏪 No hay ventas registradas 
                            @if($ventasPorVendedor['vendedor_seleccionado'])
                                para el vendedor seleccionado 
                            @endif
                            en {{ ucfirst($ventasPorVendedor['mes_actual']) }}</p>
                            @if(!$ventasPorVendedor['es_admin'])
                                <p><small>Las ventas aparecen aquí cuando los pedidos están asignados a tu usuario.</small></p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            
            @if(auth()->user()->hasRole('superadmin'))
                <div class="tab-pane fade" id="comisiones" role="tabpanel" aria-labelledby="comisiones-tab">
                    <!-- Contenido de Comisiones Acumuladas para Administradores -->
                    <h2>💰 Comisiones por Vendedor - {{ $fechaCarbon->format('d/m/Y') }}</h2>
                    @if($comisionesPorVendedor->count() > 0)
                        <div class="estadisticas">
                            @foreach ($comisionesPorVendedor as $vendedor)
                                <div class="stat-card comision-card">
                                    <h3>{{ $vendedor['vendedor_nombre'] }}</h3>
                                    <p><strong>Total Ventas:</strong> ${{ number_format($vendedor['total_ventas'], 0, ',', '.') }}</p>
                                    <p><strong>Pedidos:</strong> {{ $vendedor['total_pedidos'] }}</p>
                                    <p class="comision-monto"><strong>Comisión ({{ $vendedor['comision_porcentaje'] }}%):</strong> ${{ number_format($vendedor['comision_monto'], 0, ',', '.') }}</p>
                                    
                                    <!-- Detalles de pedidos -->
                                    <div class="pedidos-detalle">
                                        <h5>Pedidos del día:</h5>
                                        @foreach ($vendedor['pedidos'] as $pedido)
                                            <div class="pedido-detalle">
                                                <span class="cliente-nombre">{{ $pedido['cliente'] }}</span>
                                                <span class="mercancia-nombre">{{ $pedido['mercancia'] }}</span>
                                                <span class="cantidad">Cant: {{ $pedido['cantidad'] }}</span>
                                                <span class="total">${{ number_format($pedido['total'], 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-data">
                            <p>💰 No hay comisiones para calcular en {{ $fechaCarbon->format('d/m/Y') }}</p>
                        </div>
                    @endif
                </div>
            @else
                <div class="tab-pane fade" id="mis-ventas" role="tabpanel" aria-labelledby="mis-ventas-tab">
                    <!-- Contenido de Mis Ventas para Vendedores -->
                    <h2>📋 Mis Ventas - {{ $fechaCarbon->format('d/m/Y') }}</h2>
                    @if($misVentas && $misVentas['total_pedidos'] > 0)
                        <div class="mis-ventas-resumen">
                            <div class="stat-card vendedor-card">
                                <h3>{{ $misVentas['vendedor_nombre'] }}</h3>
                                <p><strong>Total de Ventas:</strong> ${{ number_format($misVentas['total_ventas'], 0, ',', '.') }}</p>
                                <p><strong>Pedidos Realizados:</strong> {{ $misVentas['total_pedidos'] }}</p>
                                <p><strong>Promedio por Pedido:</strong> ${{ number_format($misVentas['total_ventas'] / $misVentas['total_pedidos'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                        
                        <!-- Detalles de los pedidos del vendedor -->
                        <div class="mis-pedidos-detalle">
                            <h3>Detalle de mis pedidos del día:</h3>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unit.</th>
                                            <th>Total</th>
                                            <th>Horario</th>
                                            <th>Pago</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($misVentas['pedidos'] as $pedido)
                                            <tr>
                                                <td class="cliente-nombre">{{ $pedido['cliente'] }}</td>
                                                <td class="mercancia-nombre">{{ $pedido['mercancia'] }}</td>
                                                <td class="text-center">{{ $pedido['cantidad'] }}</td>
                                                <td>
                                                    @if($pedido['tiene_multiples_productos'])
                                                        <span class="precios-individuales" title="Precios individuales de cada producto">
                                                            {{ $pedido['precios_unitarios_formateados'] }}
                                                        </span>
                                                    @else
                                                        ${{ number_format($pedido['precio_unitario'], 0, ',', '.') }}
                                                    @endif
                                                </td>
                                                <td class="text-right total-pedido">${{ number_format($pedido['total'], 0, ',', '.') }}</td>
                                                <td>
                                                    <span class="horario {{ $pedido['horario_entrega'] == 'Mañana' ? 'horario-manana' : 'horario-tarde' }}">
                                                        {{ $pedido['horario_entrega'] }}
                                                    </span>
                                                </td>
                                                <td class="condicion-pago">{{ $pedido['condicion_pago'] }}</td>
                                                <td>
                                                    <a href="{{ route('pedidos.show', $pedido['id']) }}" class="btn btn-sm btn-primary">Ver</a>
                                                    <a href="{{ route('pedidos.edit', $pedido['id']) }}" class="btn btn-sm btn-secondary">Editar</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="no-data">
                            <p>📋 No tienes ventas registradas para {{ $fechaCarbon->format('d/m/Y') }}</p>
                            <p><small>Los pedidos aparecen aquí cuando están asignados a tu usuario.</small></p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .dashboard-header {
            margin-bottom: 30px;
        }
        
        /* Estilos para las pestañas */
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 20px;
        }
        
        .nav-tabs .nav-item {
            margin-bottom: -2px;
        }
        
        .nav-tabs .nav-link {
            background: none;
            border: 2px solid transparent;
            color: #495057;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 8px 8px 0 0;
            transition: all 0.3s ease;
        }
        
        .nav-tabs .nav-link:hover {
            border-color: #e9ecef #e9ecef #dee2e6;
            background-color: #f8f9fa;
            color: #007bff;
        }
        
        .nav-tabs .nav-link.active {
            color: #007bff;
            background-color: #fff;
            border-color: #007bff #007bff #fff;
            border-bottom-color: transparent;
        }
        
        .tab-content {
            border: none;
        }
        
        .tab-pane {
            padding: 20px 0;
        }
        
        /* Estilos para el filtro de fecha */
        .date-filter {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #17a2b8;
            margin-bottom: 20px;
        }
        
        .filter-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .form-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .form-group label {
            color: #17a2b8;
            font-weight: 600;
            margin: 0;
            white-space: nowrap;
        }
        
        .date-input {
            padding: 8px 12px;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            font-size: 1rem;
            background: white;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }
        
        .date-input:focus {
            outline: none;
            border-color: #17a2b8;
            box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.1);
        }
        
        .quick-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .quick-btn {
            padding: 6px 12px;
            background: #e9ecef;
            color: #495057;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .quick-btn:hover {
            background: #17a2b8;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
        }
        
        .quick-btn.active {
            background: #17a2b8;
            color: white;
            border-color: #138496;
        }
        
        .dashboard-header h1 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .pdf-buttons {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            margin-bottom: 20px;
        }
        
        .pdf-section h3 {
            margin: 0 0 15px 0;
            color: #007bff;
            font-size: 1.2em;
        }
        
        .buttons-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .btn-pdf {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .btn-pedidos {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
        }
        
        .btn-pedidos:hover {
            background-color: #c82333;
            border-color: #c82333;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        
        .btn-mercancias {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }
        
        .btn-mercancias:hover {
            background-color: #218838;
            border-color: #218838;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }
        
        .btn-pdf i {
            font-size: 1.2em;
        }
        
        .estadisticas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .estadisticas-ventas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #007bff;
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 2em;
            color: #007bff;
        }
        
        .stat-card p {
            margin: 0;
            color: #6c757d;
            font-weight: 500;
        }
        
        /* Estilos específicos para las nuevas métricas financieras */
        .stat-bruto {
            border-left-color: #007bff;
        }
        
        .stat-bruto h3 {
            color: #007bff;
        }
        
        .stat-costos {
            border-left-color: #dc3545;
        }
        
        .stat-costos h3 {
            color: #dc3545;
        }
        
        .stat-neto {
            border-left-color: #28a745;
        }
        
        .stat-neto h3 {
            color: #28a745;
        }
        
        .stat-rentabilidad {
            border-left-color: #17a2b8;
        }
        
        .stat-rentabilidad h3 {
            color: #17a2b8;
        }
        
        .dashboard-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .column-left, .column-right {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .column-left h2, .column-right h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
        }
        
        .pedido-card, .mercancia-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .pedido-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .cliente {
            font-weight: bold;
            color: #333;
        }
        
        .horario {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: bold;
        }
        
        .horario-manana {
            background: #d4edda;
            color: #155724;
        }
        
        .horario-tarde {
            background: #fff3cd;
            color: #856404;
        }
        
        .pedido-body {
            margin-bottom: 15px;
        }
        
        .mercancia {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .cantidad {
            color: #007bff;
            font-weight: bold;
        }
        
        .direccion {
            color: #6c757d;
            font-size: 0.9em;
            margin-bottom: 8px;
        }
        
        .detalles {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .total {
            font-weight: bold;
            color: #28a745;
            font-size: 1.1em;
        }
        
        .pago {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.85em;
        }
        
        .pedido-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-small {
            padding: 5px 10px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.85em;
        }
        
        .btn-small:hover {
            background: #0056b3;
        }
        
        .mercancia-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .nombre {
            font-weight: bold;
            color: #333;
        }
        
        .pedidos-count {
            background: #007bff;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8em;
        }
        
        .cantidad-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .cantidad-pedida, .stock-actual {
            display: flex;
            justify-content: space-between;
        }
        
        .numero {
            font-weight: bold;
            color: #007bff;
        }
        
        .sin-stock {
            color: #dc3545 !important;
        }
        
        .precio-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .precio-unitario {
            color: #6c757d;
            font-size: 0.9em;
        }
        
        .total-mercancia {
            font-weight: bold;
            color: #28a745;
        }
        
        .alerta-stock {
            background: #f8d7da;
            color: #721c24;
            padding: 8px;
            border-radius: 4px;
            margin-top: 10px;
            font-size: 0.9em;
        }
        
        .no-data {
            text-align: center;
            color: #6c757d;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        /* Estilos para las tarjetas de comisiones */
        .comision-card {
            border-left: 4px solid #28a745;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .comision-monto {
            color: #28a745;
            font-size: 1.1em;
            font-weight: bold;
        }
        
        .pedidos-detalle {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        }
        
        .pedidos-detalle h5 {
            color: #495057;
            font-size: 0.9em;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .pedido-detalle {
            display: grid;
            grid-template-columns: 2fr 2fr 1fr 1fr;
            gap: 8px;
            padding: 6px 0;
            border-bottom: 1px solid #f8f9fa;
            font-size: 0.85em;
        }
        
        .pedido-detalle:last-child {
            border-bottom: none;
        }
        
        .cliente-nombre {
            font-weight: 600;
            color: #495057;
        }
        
        .mercancia-nombre {
            color: #6c757d;
        }
        
        .precios-individuales {
            color: #007bff;
            font-weight: 500;
            cursor: help;
            line-height: 1.3;
        }
        
        /* Mantener el estilo anterior para compatibilidad */
        .precio-promedio {
            color: #6f42c1;
            font-weight: 600;
            cursor: help;
        }
        
        .precio-promedio small {
            color: #6c757d;
            font-size: 0.75em;
            font-style: italic;
        }
        
        .pedido-detalle .cantidad {
            color: #007bff;
            font-weight: 500;
        }
        
        .pedido-detalle .total {
            color: #28a745;
            font-weight: bold;
            text-align: right;
        }
        
        /* Estilos para la vista de vendedores */
        .vendedor-card {
            border-left: 4px solid #007bff;
            background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
        }
        
        .mis-ventas-resumen {
            margin-bottom: 30px;
        }
        
        .mis-pedidos-detalle {
            margin-top: 30px;
        }
        
        .mis-pedidos-detalle h3 {
            color: #495057;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            padding: 12px;
        }
        
        .table tbody td {
            padding: 12px;
            vertical-align: middle;
        }
        
        .total-pedido {
            color: #28a745;
            font-weight: bold;
        }
        
        .condicion-pago {
            font-size: 0.9em;
            color: #6c757d;
        }
        
        .btn-sm {
            margin-right: 5px;
        }
        
        /* Estilos para la nueva pestaña de Ventas por Vendedor */
        .ventas-vendedor-container {
            padding: 20px 0;
        }
        
        .ventas-header {
            margin-bottom: 30px;
        }
        
        .ventas-header h2 {
            color: #495057;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #007bff;
        }
        
        .vendedor-filter {
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            margin-top: 20px;
        }
        
        .pdf-button-ventas {
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
            margin-top: 20px;
            text-align: center;
        }
        
        .btn-ventas {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
        }
        
        .btn-ventas:hover {
            background-color: #c82333;
            border-color: #c82333;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        
        .filter-form-vendedor .form-group {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 0;
        }
        
        .filter-form-vendedor label {
            color: #007bff;
            font-weight: 600;
            margin: 0;
            white-space: nowrap;
        }
        
        .vendedor-select {
            padding: 8px 12px;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            font-size: 1rem;
            background: white;
            cursor: pointer;
            transition: border-color 0.3s ease;
            min-width: 250px;
        }
        
        .vendedor-select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        
        .resumen-total {
            margin-bottom: 30px;
        }
        
        .estadisticas-ventas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .stat-vendedores {
            border-left-color: #6f42c1;
        }
        
        .stat-vendedores h3 {
            color: #6f42c1;
        }
        
        .stat-pedidos {
            border-left-color: #17a2b8;
        }
        
        .stat-pedidos h3 {
            color: #17a2b8;
        }
        
        .stat-ventas {
            border-left-color: #28a745;
        }
        
        .stat-ventas h3 {
            color: #28a745;
        }
        
        .stat-comisiones {
            border-left-color: #ffc107;
        }
        
        .stat-comisiones h3 {
            color: #856404;
        }
        
        .ventas-list {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .vendedor-ventas-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .vendedor-ventas-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        
        .vendedor-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .vendedor-info h3 {
            margin: 0 0 5px 0;
            font-size: 1.5em;
            font-weight: 700;
        }
        
        .vendedor-username {
            display: block;
            opacity: 0.9;
            font-size: 0.95em;
            font-weight: 500;
        }
        
        .vendedor-email {
            display: block;
            opacity: 0.8;
            font-size: 0.85em;
            margin-top: 2px;
        }
        
        .vendedor-stats {
            display: flex;
            gap: 25px;
            align-items: center;
        }
        
        .stat-item {
            text-align: center;
            min-width: 80px;
        }
        
        .stat-number {
            display: block;
            font-size: 1.4em;
            font-weight: 700;
            line-height: 1.2;
        }
        
        .stat-label {
            display: block;
            font-size: 0.8em;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .vendedor-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        
        .metric-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #495057;
        }
        
        .metric-item i {
            color: #007bff;
            width: 16px;
            text-align: center;
        }
        
        .pedidos-detalle-vendedor {
            padding: 20px;
        }
        
        .pedidos-detalle-vendedor h4 {
            color: #495057;
            margin-bottom: 20px;
            font-size: 1.2em;
            font-weight: 600;
        }
        
        .precio-personalizado {
            background-color: #fff3cd !important;
        }
        
        .precio-personalizado-texto {
            color: #856404;
            font-weight: bold;
        }
        
        .precio-personalizado-total {
            color: #856404 !important;
        }
        
        .cliente-info strong {
            color: #495057;
        }
        
        .producto-info {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .precio-info {
            text-align: right;
        }
        
        .btn-outline-primary {
            color: #007bff;
            border-color: #007bff;
        }
        
        .btn-outline-primary:hover {
            background-color: #007bff;
            color: white;
        }
        
        .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
        }
        
        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }
        
        /* Estilos para filtros de horario */
        .pedidos-header, .mercancias-header {
            margin-bottom: 20px;
        }
        
        .pedidos-header h2, .mercancias-header h2 {
            margin-bottom: 15px;
        }
        
        .horario-filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .horario-filter-btn {
            padding: 8px 16px;
            border: 2px solid #e9ecef;
            background: #f8f9fa;
            color: #495057;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9em;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .horario-filter-btn:hover {
            background: #e9ecef;
            border-color: #007bff;
            color: #007bff;
            transform: translateY(-1px);
        }
        
        .horario-filter-btn.active {
            background: #007bff;
            border-color: #007bff;
            color: white;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
        }
        
        /* Estilos para filtros de horario de MERCANCÍAS */
        .horario-filter-btn-mercancias {
            padding: 8px 16px;
            border: 2px solid #e9ecef;
            background: #f8f9fa;
            color: #495057;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9em;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .horario-filter-btn-mercancias:hover {
            background: #e9ecef;
            border-color: #28a745;
            color: #28a745;
            transform: translateY(-1px);
        }
        
        .horario-filter-btn-mercancias.active {
            background: #28a745;
            border-color: #28a745;
            color: white;
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
        }
        
        .pedido-card.hidden {
            display: none;
        }
        
        @media (max-width: 768px) {
            .dashboard-content {
                grid-template-columns: 1fr;
            }
            
            .estadisticas {
                grid-template-columns: 1fr 1fr;
            }
            
            .cantidad-info {
                grid-template-columns: 1fr;
            }
            
            .buttons-group {
                flex-direction: column;
            }
            
            .btn-pdf {
                justify-content: center;
                text-align: center;
            }
            
            /* Responsivo para filtro de fecha */
            .form-group {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .date-input {
                width: 100%;
                max-width: 200px;
            }
            
            .quick-filters {
                justify-content: flex-start;
            }
            
            .quick-btn {
                flex: 1;
                text-align: center;
                min-width: 60px;
            }
            
            /* Responsive para la nueva pestaña de ventas */
            .filter-form-vendedor .form-group {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .vendedor-select {
                width: 100%;
                min-width: unset;
            }
            
            .estadisticas-ventas {
                grid-template-columns: 1fr 1fr;
            }
            
            .vendedor-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .vendedor-stats {
                justify-content: center;
                gap: 15px;
            }
            
            .vendedor-metrics {
                grid-template-columns: 1fr;
            }
            
            .table-responsive {
                font-size: 0.85em;
            }
            
            .vendedor-ventas-card {
                margin: 0 -10px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar si hay un parámetro 'tab' en la URL
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab');
            
            if (activeTab) {
                // Desactivar la pestaña activa actual
                const currentActiveTab = document.querySelector('.nav-tabs .nav-link.active');
                const currentActivePane = document.querySelector('.tab-pane.show.active');
                
                if (currentActiveTab) {
                    currentActiveTab.classList.remove('active');
                    currentActiveTab.setAttribute('aria-selected', 'false');
                }
                if (currentActivePane) {
                    currentActivePane.classList.remove('show', 'active');
                }
                
                // Activar la pestaña especificada usando data-bs-target
                const targetTabLink = document.querySelector(`[data-bs-target="#${activeTab}"]`);
                const targetTabPane = document.querySelector(`#${activeTab}`);
                
                if (targetTabLink && targetTabPane) {
                    targetTabLink.classList.add('active');
                    targetTabLink.setAttribute('aria-selected', 'true');
                    targetTabPane.classList.add('show', 'active');
                }
            }
            
            // Funcionalidad de filtros de horario
            const horarioFilterBtns = document.querySelectorAll('.horario-filter-btn');
            const pedidoCards = document.querySelectorAll('.pedido-card');
            
            horarioFilterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remover clase active de todos los botones
                    horarioFilterBtns.forEach(b => b.classList.remove('active'));
                    
                    // Agregar clase active al botón clickeado
                    this.classList.add('active');
                    
                    // Obtener el horario a filtrar
                    const horarioFiltro = this.getAttribute('data-horario');
                    
                    // Mostrar/ocultar pedidos según el filtro
                    pedidoCards.forEach(card => {
                        const horarioPedido = card.getAttribute('data-horario');
                        
                        if (horarioFiltro === 'todos' || horarioFiltro === horarioPedido) {
                            card.classList.remove('hidden');
                        } else {
                            card.classList.add('hidden');
                        }
                    });
                    
                    // Mostrar mensaje si no hay pedidos para el filtro seleccionado
                    const pedidosVisibles = document.querySelectorAll('.pedido-card:not(.hidden)');
                    const noDataDiv = document.querySelector('.column-left .no-data');
                    const pedidosList = document.querySelector('.pedidos-list');
                    
                    if (pedidosVisibles.length === 0 && pedidosList) {
                        if (!noDataDiv) {
                            const noDataElement = document.createElement('div');
                            noDataElement.className = 'no-data filtro-temporal';
                            noDataElement.innerHTML = `<p>🎯 No hay pedidos para el horario seleccionado</p>`;
                            pedidosList.parentNode.insertBefore(noDataElement, pedidosList.nextSibling);
                        }
                        pedidosList.style.display = 'none';
                    } else {
                        const tempNoData = document.querySelector('.filtro-temporal');
                        if (tempNoData) {
                            tempNoData.remove();
                        }
                        if (pedidosList) {
                            pedidosList.style.display = 'block';
                        }
                    }
                });
            });
            
            // Funcionalidad de filtros de horario para MERCANCÍAS
            const horarioFilterBtnsMercancias = document.querySelectorAll('.horario-filter-btn-mercancias');
            const mercanciasLists = document.querySelectorAll('.mercancias-list');
            
            horarioFilterBtnsMercancias.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remover clase active de todos los botones de mercancías
                    horarioFilterBtnsMercancias.forEach(b => b.classList.remove('active'));
                    
                    // Agregar clase active al botón clickeado
                    this.classList.add('active');
                    
                    // Obtener el horario a filtrar
                    const horarioFiltro = this.getAttribute('data-horario');
                    
                    // Mostrar/ocultar listas de mercancías según el filtro
                    mercanciasLists.forEach(list => {
                        const horarioMercancias = list.getAttribute('data-horario-mercancias');
                        
                        if (horarioFiltro === 'todos') {
                            // Si es "todos", mostrar solo la lista completa
                            if (horarioMercancias === 'todos') {
                                list.style.display = 'block';
                            } else {
                                list.style.display = 'none';
                            }
                        } else {
                            // Si es un horario específico, mostrar solo esa lista
                            if (horarioFiltro === horarioMercancias) {
                                list.style.display = 'block';
                            } else {
                                list.style.display = 'none';
                            }
                        }
                    });
                    
                    // Verificar si hay mercancías visibles
                    const mercanciasVisibles = document.querySelectorAll('.mercancias-list[style*="display: block"], .mercancias-list:not([style*="display: none"])');
                    const columnRight = document.querySelector('.column-right');
                    let noDataMercancias = columnRight.querySelector('.no-data-mercancias');
                    
                    // Buscar si alguna lista visible tiene contenido
                    let hayContenido = false;
                    mercanciasVisibles.forEach(list => {
                        if (list.children.length > 0) {
                            hayContenido = true;
                        }
                    });
                    
                    if (!hayContenido) {
                        if (!noDataMercancias) {
                            noDataMercancias = document.createElement('div');
                            noDataMercancias.className = 'no-data no-data-mercancias filtro-temporal';
                            noDataMercancias.innerHTML = `<p>📦 No hay mercancías para el horario seleccionado</p>`;
                            columnRight.appendChild(noDataMercancias);
                        }
                        noDataMercancias.style.display = 'block';
                    } else {
                        if (noDataMercancias) {
                            noDataMercancias.style.display = 'none';
                        }
                    }
                });
            });
        });
    </script>

@endsection

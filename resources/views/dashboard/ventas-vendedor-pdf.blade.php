<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas por Vendedor - {{ $fechaCarbon->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        .header h1 {
            color: #007bff;
            margin: 0 0 5px 0;
            font-size: 20px;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        
        .resumen-total {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #007bff;
        }
        
        .resumen-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            text-align: center;
        }
        
        .stat-item {
            background: white;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
        
        .stat-number {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            display: block;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .vendedor-section {
            margin-bottom: 30px;
            break-inside: avoid;
        }
        
        .vendedor-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            display: grid;
            grid-template-columns: 2fr 1fr;
            align-items: center;
        }
        
        .vendedor-info h3 {
            margin: 0 0 5px 0;
            font-size: 16px;
        }
        
        .vendedor-username {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .vendedor-email {
            font-size: 11px;
            opacity: 0.8;
            margin-top: 2px;
        }
        
        .vendedor-stats {
            text-align: right;
        }
        
        .stat-inline {
            display: inline-block;
            margin-left: 15px;
            text-align: center;
        }
        
        .stat-inline .number {
            font-size: 14px;
            font-weight: bold;
            display: block;
        }
        
        .stat-inline .label {
            font-size: 9px;
            opacity: 0.9;
        }
        
        .vendedor-metrics {
            background: #f8f9fa;
            padding: 10px 15px;
            border-left: 4px solid #17a2b8;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            font-size: 11px;
        }
        
        .metric-item {
            color: #495057;
        }
        
        .pedidos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }
        
        .pedidos-table th {
            background: #f8f9fa;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #dee2e6;
            font-weight: bold;
            color: #495057;
        }
        
        .pedidos-table td {
            padding: 6px 6px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }
        
        .pedidos-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .precio-personalizado {
            background-color: #fff3cd !important;
        }
        
        .precio-personalizado-texto {
            color: #856404;
            font-weight: bold;
        }
        
        .precio-base {
            font-size: 9px;
            color: #666;
            display: block;
        }
        
        .costo-column {
            color: #dc3545;
            font-weight: bold;
        }
        
        .sin-iva-column {
            color: #007bff;
            font-size: 10px;
        }
        
        .total-pedido {
            font-weight: bold;
            color: #28a745;
            text-align: right;
        }
        
        .horario-manana {
            background: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .horario-tarde {
            background: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .cliente-info {
            font-weight: bold;
        }
        
        .cliente-rut {
            font-size: 9px;
            color: #666;
            display: block;
            font-weight: normal;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .vendedor-section {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Reporte de Ventas por Vendedor</h1>
        <p class="subtitle">
            Periodo: {{ $ventasPorVendedor['mes_actual'] ?? $fechaCarbon->locale('es')->isoFormat('MMMM YYYY') }}
            @if($vendedorInfo)
                - Vendedor: {{ $vendedorInfo->name }}
            @endif
            <br><small>Generado desde fecha de referencia: {{ $fechaCarbon->format('d/m/Y') }}</small>
        </p>
    </div>

    @if($ventasPorVendedor['es_admin'] && !$vendedorInfo && $ventasPorVendedor['resumen_total']['total_vendedores'] > 1)
        {{-- Resumen total solo para administradores viendo todos los vendedores --}}
        <div class="resumen-total">
            <h3 style="margin: 0 0 15px 0; color: #007bff;">📈 Resumen General</h3>
            <div class="resumen-stats">
                <div class="stat-item">
                    <span class="stat-number">{{ $ventasPorVendedor['resumen_total']['total_vendedores'] }}</span>
                    <span class="stat-label">Vendedores Activos</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $ventasPorVendedor['resumen_total']['total_pedidos'] }}</span>
                    <span class="stat-label">Total Pedidos</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">${{ number_format($ventasPorVendedor['resumen_total']['total_neto'], 0, ',', '.') }}</span>
                    <span class="stat-label">Total Neto (con IVA)</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">${{ number_format($ventasPorVendedor['resumen_total']['total_bruto'], 0, ',', '.') }}</span>
                    <span class="stat-label">Total Bruto (sin IVA)</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">${{ number_format($ventasPorVendedor['resumen_total']['total_costos'], 0, ',', '.') }}</span>
                    <span class="stat-label">Total Costos</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">${{ number_format($ventasPorVendedor['resumen_total']['ganancia_neta_total'], 0, ',', '.') }}</span>
                    <span class="stat-label">Ganancia Neta</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ number_format($ventasPorVendedor['resumen_total']['rentabilidad_general'], 1) }}%</span>
                    <span class="stat-label">Rentabilidad General</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">${{ number_format($ventasPorVendedor['resumen_total']['total_comisiones'], 0, ',', '.') }}</span>
                    <span class="stat-label">Comisiones Estimadas</span>
                </div>
            </div>
        </div>
    @endif

    @if(count($ventasPorVendedor['ventas']) > 0)
        @foreach($ventasPorVendedor['ventas'] as $index => $ventaVendedor)
            @if($index > 0 && count($ventasPorVendedor['ventas']) > 1)
                <div class="page-break"></div>
            @endif
            
            <div class="vendedor-section">
                {{-- Header del vendedor --}}
                <div class="vendedor-header">
                    <div class="vendedor-info">
                        <h3>{{ $ventaVendedor['vendedor_nombre'] }}</h3>
                        <div class="vendedor-username">{{ $ventaVendedor['vendedor_username'] }}</div>
                        @if($ventasPorVendedor['es_admin'])
                            <div class="vendedor-email">{{ $ventaVendedor['vendedor_email'] }}</div>
                        @endif
                    </div>
                    <div class="vendedor-stats">
                        <div class="stat-inline">
                            <span class="number">{{ $ventaVendedor['total_pedidos'] }}</span>
                            <span class="label">Pedidos</span>
                        </div>
                        <div class="stat-inline">
                            <span class="number">${{ number_format($ventaVendedor['total_ventas'], 0, ',', '.') }}</span>
                            <span class="label">Total Ventas</span>
                        </div>
                        @if($ventasPorVendedor['es_admin'])
                            <div class="stat-inline">
                                <span class="number">${{ number_format($ventaVendedor['comision_estimada'], 0, ',', '.') }}</span>
                                <span class="label">Comisión</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- Métricas del vendedor --}}
                <div class="vendedor-metrics">
                    <div class="metric-item">
                        📊 Promedio por pedido: ${{ number_format($ventaVendedor['promedio_por_pedido'], 0, ',', '.') }}
                    </div>
                    <div class="metric-item">
                        💰 Total bruto (sin IVA): ${{ number_format($ventaVendedor['total_bruto'], 0, ',', '.') }}
                    </div>
                    <div class="metric-item">
                        💸 Total costos: ${{ number_format($ventaVendedor['total_costos'], 0, ',', '.') }}
                    </div>
                    <div class="metric-item">
                        📈 Ganancia neta: ${{ number_format($ventaVendedor['ganancia_neta'], 0, ',', '.') }}
                    </div>
                    <div class="metric-item">
                        📊 Rentabilidad: {{ number_format($ventaVendedor['rentabilidad_porcentaje'], 1) }}%
                    </div>
                    <div class="metric-item">
                        👥 {{ $ventaVendedor['clientes_unicos'] }} clientes únicos
                    </div>
                    <div class="metric-item">
                        📦 {{ $ventaVendedor['productos_unicos'] }} productos diferentes
                    </div>
                    <div class="metric-item">
                        🕒 {{ $ventaVendedor['pedidos_manana'] }} mañana / {{ $ventaVendedor['pedidos_tarde'] }} tarde
                    </div>
                </div>
                
                {{-- Tabla de pedidos --}}
                <table class="pedidos-table">
                    <thead>
                        <tr>
                            <th style="width: 18%;">Cliente</th>
                            <th style="width: 15%;">Producto</th>
                            <th style="width: 6%;">Cant.</th>
                            <th style="width: 10%;">P. Unit.</th>
                            <th style="width: 10%;">Total</th>
                            <th style="width: 9%;">Sin IVA</th>
                            <th style="width: 9%;">Costo</th>
                            <th style="width: 8%;">Horario</th>
                            <th style="width: 15%;">Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventaVendedor['pedidos'] as $pedido)
                            <tr class="{{ $pedido['tiene_precio_personalizado'] ? 'precio-personalizado' : '' }}">
                                <td>
                                    <div class="cliente-info">
                                        {{ $pedido['cliente'] }}
                                        <span class="cliente-rut">{{ $pedido['cliente_rut'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    {{ $pedido['mercancia'] }}
                                    @if($pedido['tiene_precio_personalizado'])
                                        ⭐
                                    @endif
                                </td>
                                <td style="text-align: center;">{{ $pedido['cantidad'] }}</td>
                                <td style="text-align: right;">
                                    @if($pedido['tiene_precio_personalizado'])
                                        <span class="precio-personalizado-texto">${{ number_format($pedido['precio_unitario'], 0, ',', '.') }}</span>
                                        <span class="precio-base">Base: ${{ number_format($pedido['precio_base'], 0, ',', '.') }}</span>
                                    @else
                                        ${{ number_format($pedido['precio_unitario'], 0, ',', '.') }}
                                    @endif
                                </td>
                                <td class="total-pedido">${{ number_format($pedido['total'], 0, ',', '.') }}</td>
                                <td style="text-align: right;">${{ number_format($pedido['total_sin_iva'], 0, ',', '.') }}</td>
                                <td style="text-align: right; color: #dc3545;">${{ number_format($pedido['costo_total'], 0, ',', '.') }}</td>
                                <td>
                                    <span class="horario-{{ $pedido['horario_entrega'] == 'Mañana' ? 'manana' : 'tarde' }}">
                                        {{ $pedido['horario_entrega'] }}
                                    </span>
                                </td>
                                <td>{{ $pedido['condicion_pago'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @else
        <div class="no-data">
            <h3>📋 No hay ventas registradas</h3>
            <p>
                No se encontraron ventas 
                @if($vendedorInfo)
                    para {{ $vendedorInfo->name }}
                @endif
                en el período de {{ $ventasPorVendedor['mes_actual'] ?? $fechaCarbon->locale('es')->isoFormat('MMMM YYYY') }}
            </p>
        </div>
    @endif

    <div class="footer">
        <p>📄 Reporte generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>🏪 Sistema de Gestión de Ventas</p>
    </div>
</body>
</html>

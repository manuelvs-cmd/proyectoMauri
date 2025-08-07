<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Pedidos - {{ $fechaCarbon->format('d/m/Y') }}</title>
    <style>
        @page {
            margin: 20px;
            size: A4;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 15px;
        }
        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .date-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .statistics {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #007BFF;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }
        .table th {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
        }
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .horario-manana {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .horario-tarde {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE PEDIDOS</h1>
        <h2>{{ $fechaCarbon->format('d/m/Y') }}</h2>
    </div>

    <div class="company-info">
        <strong>TU EMPRESA</strong><br>
        RUT: XX.XXX.XXX-X<br>
        Dirección: Tu dirección<br>
        Teléfono: +56 9 XXXX XXXX
    </div>

    <div class="date-info">
        <strong>Fecha del Reporte:</strong> {{ ucfirst($fechaCarbon->translatedFormat('l, d \d\e F \d\e Y')) }}
    </div>

    <div class="statistics">
        <div class="stat-box">
            <div class="stat-number">{{ $estadisticas['total_pedidos'] }}</div>
            <div class="stat-label">Total Pedidos</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">${{ number_format($estadisticas['valor_total'], 0, ',', '.') }}</div>
            <div class="stat-label">Valor Total</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $estadisticas['pedidos_manana'] }}</div>
            <div class="stat-label">Pedidos Mañana</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $estadisticas['pedidos_tarde'] }}</div>
            <div class="stat-label">Pedidos Tarde</div>
        </div>
    </div>

    @if($pedidos->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="20%">Cliente</th>
                    <th width="20%">Mercancía</th>
                    <th width="8%">Cant.</th>
                    <th width="12%">Precio Unit.</th>
                    <th width="12%">Total</th>
                    <th width="10%">Horario</th>
                    <th width="13%">Forma Pago</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedidos as $index => $pedido)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $pedido->cliente->razon_social }}</td>
                    <td>{{ $pedido->mercancia->nombre }}</td>
                    <td class="text-center">{{ $pedido->cantidad_solicitada ?? 1 }}</td>
                    <td class="text-right">${{ number_format($pedido->mercancia->precio_venta, 0, ',', '.') }}</td>
                    <td class="text-right">${{ number_format($pedido->calcularTotal(), 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="{{ $pedido->horario_entrega == 'Mañana' ? 'horario-manana' : 'horario-tarde' }}">
                            {{ $pedido->horario_entrega }}
                        </span>
                    </td>
                    <td class="text-center">{{ $pedido->condicion_pago }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            <h3>Resumen por Horario:</h3>
            <table class="table" style="width: 50%;">
                <tr>
                    <th>Horario</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-right">Valor</th>
                </tr>
                <tr>
                    <td>Mañana</td>
                    <td class="text-center">{{ $estadisticas['pedidos_manana'] }}</td>
                    <td class="text-right">
                        ${{ number_format($pedidos->where('horario_entrega', 'Mañana')->sum(function($p) { return $p->calcularTotal(); }), 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td>Tarde</td>
                    <td class="text-center">{{ $estadisticas['pedidos_tarde'] }}</td>
                    <td class="text-right">
                        ${{ number_format($pedidos->where('horario_entrega', 'Tarde')->sum(function($p) { return $p->calcularTotal(); }), 0, ',', '.') }}
                    </td>
                </tr>
                <tr style="font-weight: bold; background-color: #f0f0f0;">
                    <td>TOTAL</td>
                    <td class="text-center">{{ $estadisticas['total_pedidos'] }}</td>
                    <td class="text-right">${{ number_format($estadisticas['valor_total'], 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    @else
        <div class="no-data">
            <h3>📅 No hay pedidos programados para esta fecha</h3>
            <p>No se encontraron pedidos con fecha de entrega {{ $fechaCarbon->format('d/m/Y') }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Reporte generado el {{ now()->setTimezone('America/Santiago')->format('d/m/Y H:i:s') }}</p>
        <p>Este documento contiene información confidencial de la empresa</p>
    </div>
</body>
</html>

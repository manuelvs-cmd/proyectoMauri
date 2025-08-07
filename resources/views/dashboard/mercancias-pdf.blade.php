<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Mercancías - {{ $fechaCarbon->format('d/m/Y') }}</title>
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
        .no-stock {
            color: #dc3545;
            font-weight: bold;
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
        <h1>REPORTE DE MERCANCÍAS</h1>
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
            <div class="stat-number">{{ $estadisticas['total_mercancias'] }}</div>
            <div class="stat-label">Total Mercancías</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $estadisticas['cantidad_total_pedida'] }}</div>
            <div class="stat-label">Cantidad Total Pedida</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">${{ number_format($estadisticas['valor_total'], 0, ',', '.') }}</div>
            <div class="stat-label">Valor Total</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $estadisticas['mercancias_sin_stock'] }}</div>
            <div class="stat-label">Sin Stock</div>
        </div>
    </div>

    @if($mercancias->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="40%">Mercancía</th>
                    <th width="10%">Cant. Pedida</th>
                    <th width="10%">Stock</th>
                    <th width="10%">Precio Unit.</th>
                    <th width="10%">Total</th>
                    <th width="15%">Pedidos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mercancias as $index => $mercancia)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $mercancia['nombre'] }}
                        @if($mercancia['tiene_precios_personalizados'])
                            <span title="Precio personalizado" style="color: #b79347;">★</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $mercancia['cantidad_pedida'] }}</td>
                    <td class="text-center {{ $mercancia['stock_actual'] <= 0 ? 'no-stock' : '' }}">{{ $mercancia['stock_actual'] }}</td>
                    <td class="text-right">${{ number_format($mercancia['precio_venta'], 0, ',', '.') }}</td>
                    <td class="text-right">${{ number_format($mercancia['valor_total'], 0, ',', '.') }}</td>
                    <td class="text-center">{{ $mercancia['total_pedidos'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top: 20px;">
            <h3>Resumen General:</h3>
            <table class="table" style="width: 50%;">
                <tr>
                    <th>Total Mercancías</th>
                    <td class="text-center">{{ $estadisticas['total_mercancias'] }}</td>
                </tr>
                <tr>
                    <th>Total Cantidad Pedida</th>
                    <td class="text-center">{{ $estadisticas['cantidad_total_pedida'] }}</td>
                </tr>
                <tr>
                    <th>Total Valor</th>
                    <td class="text-right">${{ number_format($estadisticas['valor_total'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Mercancías Sin Stock</th>
                    <td class="text-center">{{ $estadisticas['mercancias_sin_stock'] }}</td>
                </tr>
            </table>
        </div>
    @else
        <div class="no-data">
            <h3>📦 No hay mercancías solicitadas para esta fecha</h3>
            <p>No se encontraron mercancías asociadas a pedidos con fecha de entrega {{ $fechaCarbon->format('d/m/Y') }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Reporte generado el {{ now()->setTimezone('America/Santiago')->format('d/m/Y H:i:s') }}</p>
        <p>Este documento contiene información confidencial de la empresa</p>
    </div>
</body>
</html>

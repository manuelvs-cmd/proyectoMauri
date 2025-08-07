<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($factura->tipo_documento) }} {{ $factura->numero_documento }}</title>
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
        }
        .company-info {
            margin-bottom: 20px;
            width: 60%;
            float: left;
        }
        .document-info {
            float: right;
            width: 35%;
            border: 2px solid #000;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .client-info {
            margin-bottom: 20px;
            clear: both;
            border: 1px solid #ccc;
            padding: 10px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .totals {
            float: right;
            width: 250px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            clear: both;
            page-break-inside: avoid;
        }
        .text-right {
            text-align: right;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ucfirst($factura->tipo_documento) }}</h1>
    </div>

    <div class="clearfix">
        <div class="company-info">
            <strong>TU EMPRESA</strong><br>
            RUT: XX.XXX.XXX-X<br>
            Dirección: Tu dirección<br>
            Teléfono: +56 9 XXXX XXXX<br>
            Email: contacto@tuempresa.cl
        </div>

        <div class="document-info">
            <strong>{{ strtoupper($factura->tipo_documento) }}</strong><br>
            N° {{ $factura->numero_documento }}<br>
            Fecha: {{ $factura->fecha_emision->format('d/m/Y') }}<br>
            Estado: {{ ucfirst($factura->estado) }}
        </div>
    </div>

    <div class="client-info">
        <strong>Datos del Cliente:</strong><br>
        {{ $factura->pedido->cliente->razon_social }}<br>
        RUT: {{ $factura->pedido->cliente->rut }}<br>
        Dirección: {{ $factura->pedido->cliente->obtenerDireccionCompleta() }}<br>
        @if($factura->pedido->cliente->telefono)
            Teléfono: {{ $factura->pedido->cliente->telefono }}<br>
        @endif
        Email: {{ $factura->pedido->cliente->correo_electronico ?? '-' }}<br>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->pedido->mercancias as $mercancia)
                <tr>
                    <td>
                        {{ $mercancia->nombre }}
                        @if($mercancia->pivot->precio_unitario !== null)
                            <span style="color: #d9534f; font-size: 10px;"> (Precio personalizado)</span>
                        @endif
                    </td>
                    <td>{{ $mercancia->pivot->cantidad_solicitada ?? 1 }}</td>
                    <td class="text-right">
                        @php
                            $precioUnitario = $mercancia->pivot->precio_unitario ?? $mercancia->precio_venta;
                        @endphp
                        ${{ number_format($precioUnitario, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        @php
                            $cantidad = $mercancia->pivot->cantidad_solicitada ?? 1;
                            $subtotal = $precioUnitario * $cantidad;
                        @endphp
                        ${{ number_format($subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table class="table">
            <tr>
                <th>Venta neto:</th>
                <td class="text-right">${{ number_format($factura->subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>IVA (19%):</th>
                <td class="text-right">${{ number_format($factura->iva, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th><strong>Venta bruto:</strong></th>
                <td class="text-right"><strong>${{ number_format($factura->total, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
        <p style="font-size: 10px; font-style: italic; margin-top: 10px;">* Los precios mostrados incluyen IVA</p>
    </div>
    
    @if($factura->pedido->mercancias->count() > 1)
        <div style="clear: both; margin-top: 15px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;">
            <strong>Resumen del pedido:</strong><br>
            <span style="font-size: 11px;">
                {{ $factura->pedido->mercancias->count() }} productos diferentes • 
                {{ $factura->pedido->getCantidadTotal() }} unidades en total
                @if($factura->pedido->tienePrecioPersonalizado())
                    • <span style="color: #d9534f;">Contiene precios personalizados</span>
                @endif
            </span>
        </div>
    @endif

    @if($factura->observaciones)
        <div style="clear: both; margin-top: 20px;">
            <strong>Observaciones:</strong><br>
            {{ $factura->observaciones }}
        </div>
    @endif

    <div class="footer">
        <p>Pedido creado por: {{ $factura->pedido->user->name }} - {{ $factura->pedido->created_at->format('d/m/Y H:i') }}</p>
        <p>Documento generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @if(!request()->is('*/pdf'))
    <script>
        // Auto-imprimir cuando se carga la página (solo para vista previa)
        window.onload = function() {
            window.print();
        };
    </script>
    @endif
</body>
</html>

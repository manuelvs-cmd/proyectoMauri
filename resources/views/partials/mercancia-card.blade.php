@if(isset($mercancia))
    <div class="mercancia-card">
        <div class="mercancia-header">
            <span class="nombre">
                {{ $mercancia['nombre'] }}
                @if($mercancia['tiene_precios_personalizados'])
                    <i class="fas fa-star text-warning" title="Tiene precios personalizados"></i>
                @endif
            </span>
            <span class="pedidos-count">{{ $mercancia['total_pedidos'] }} pedido(s)</span>
        </div>
        <div class="mercancia-body">
            <div class="cantidad-info">
                <div class="cantidad-pedida">
                    <strong>Cantidad Solicitada:</strong>
                    <span class="numero">{{ $mercancia['cantidad_pedida'] }}</span>
                </div>
                <div class="stock-actual">
                    <strong>Stock Actual:</strong>
                    <span class="numero {{ $mercancia['stock_actual'] <= 0 ? 'sin-stock' : '' }}">
                        {{ $mercancia['stock_actual'] }}
                    </span>
                </div>
            </div>
            <div class="precio-info">
                <span class="precio-unitario">
                    Precio base: ${{ number_format($mercancia['precio_venta_original'], 0, ',', '.') }}
                    @if($mercancia['tiene_precios_personalizados'])
                        <br><small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Algunos pedidos con precio personalizado</small>
                    @endif
                </span>
                <span class="total-mercancia {{ $mercancia['tiene_precios_personalizados'] ? 'text-warning font-weight-bold' : '' }}">
                    Total: ${{ number_format($mercancia['valor_total'], 0, ',', '.') }}
                    @if($mercancia['tiene_precios_personalizados'])
                        <i class="fas fa-star" title="Incluye precios personalizados"></i>
                    @endif
                </span>
            </div>
        </div>
        @if($mercancia['stock_actual'] <= 0)
            <div class="alerta-stock">
                ⚠️ Sin stock disponible
            </div>
        @elseif($mercancia['stock_actual'] < $mercancia['cantidad_pedida'])
            <div class="alerta-stock">
                ⚠️ Stock insuficiente
            </div>
        @endif
    </div>
@endif


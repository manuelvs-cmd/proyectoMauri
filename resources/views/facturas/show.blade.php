@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalle de la Factura/Boleta</h4>
                    <div>
                        <a href="{{ route('facturas.edit', $factura->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('facturas.preview', $factura->id) }}" class="btn btn-info btn-sm" target="_blank">
                            <i class="fas fa-search"></i> Vista Previa
                        </a>
                        <a href="{{ route('facturas.pdf', $factura->id) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Descargar PDF
                        </a>
                        @if(!$factura->esEnviadaAlSii())
                            <button class="btn btn-primary btn-sm" onclick="enviarSii({{ $factura->id }})">
                                <i class="fas fa-paper-plane"></i> Enviar al SII
                            </button>
                        @else
                            <button class="btn btn-info btn-sm" onclick="verificarEstadoSii({{ $factura->id }})">
                                <i class="fas fa-sync"></i> Verificar Estado SII
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <table class="table table-bordered">
                        <tr>
                            <th>N° Documento</th>
                            <td>{{ $factura->numero_documento }}</td>
                        </tr>
                        <tr>
                            <th>Tipo de Documento</th>
                            <td>{{ ucfirst($factura->tipo_documento) }}</td>
                        </tr>
                        <tr>
                            <th>Cliente</th>
                            <td>{{ $factura->pedido->cliente->razon_social }}</td>
                        </tr>
                        <tr>
                            <th>Mercancía</th>
                            <td>{{ $factura->pedido->mercancia->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Emisión</th>
                            <td>{{ $factura->fecha_emision->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Venta neto</th>
                            <td>${{ number_format($factura->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>IVA (19%)</th>
                            <td>${{ number_format($factura->iva, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Venta bruto</th>
                            <td>${{ number_format($factura->total, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Estado</th>
                            <td>
                                <span class="badge badge-{{ $factura->estado === 'pagada' ? 'success' : ($factura->estado === 'anulada' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($factura->estado) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Observaciones</th>
                            <td>{{ $factura->observaciones ?? 'Sin observaciones' }}</td>
                        </tr>
                        <tr>
                            <th>Estado SII</th>
                            <td>
                                <span class="badge badge-{{ $factura->color_sii_estado }}">
                                    {{ $factura->sii_estado_amigable }}
                                </span>
                                @if($factura->sii_track_id)
                                    <br><small class="text-muted">Track ID: {{ $factura->sii_track_id }}</small>
                                @endif
                            </td>
                        </tr>
                        @if($factura->sii_fecha_envio)
                        <tr>
                            <th>Fecha Envío SII</th>
                            <td>{{ $factura->sii_fecha_envio->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        @endif
                    </table>

                    <div class="form-group">
                        <a href="{{ route('facturas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a Facturas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar resultados -->
<div class="modal fade" id="resultadoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Resultado</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function enviarSii(facturaId) {
    if (!confirm('¿Estás seguro de enviar esta factura al SII?')) {
        return;
    }
    
    // Mostrar loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    btn.disabled = true;
    
    fetch(`/facturas/${facturaId}/enviar-sii`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showModal('Envío Exitoso', 
                `<div class="alert alert-success">
                    <strong>Factura enviada correctamente al SII</strong><br>
                    ${data.track_id ? `Track ID: ${data.track_id}` : ''}
                </div>`);
            // Recargar página después de 2 segundos
            setTimeout(() => location.reload(), 2000);
        } else {
            showModal('Error en Envío', 
                `<div class="alert alert-danger">
                    <strong>Error:</strong> ${data.message}<br>
                    ${data.error ? `Detalle: ${data.error}` : ''}
                </div>`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 
            `<div class="alert alert-danger">
                <strong>Error de conexión:</strong> ${error.message}
            </div>`);
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function verificarEstadoSii(facturaId) {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';
    btn.disabled = true;
    
    fetch(`/facturas/${facturaId}/verificar-sii`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showModal('Estado en SII', 
                `<div class="alert alert-info">
                    <strong>Estado actual:</strong> ${data.estado}<br>
                    ${data.message}
                </div>`);
        } else {
            showModal('Error al Verificar', 
                `<div class="alert alert-warning">
                    <strong>Error:</strong> ${data.message}
                </div>`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 
            `<div class="alert alert-danger">
                <strong>Error de conexión:</strong> ${error.message}
            </div>`);
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function showModal(title, body) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalBody').innerHTML = body;
    $('#resultadoModal').modal('show');
}
</script>
@endsection

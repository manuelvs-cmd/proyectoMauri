<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'pedido_id',
        'tipo_documento',
        'numero_documento',
        'fecha_emision',
        'subtotal',
        'iva',
        'total',
        'estado',
        'observaciones',
        'sii_track_id',
        'sii_estado',
        'sii_fecha_envio',
        'sii_respuesta',
        'sii_folio_caf',
        'sii_enviado_automatico',
    ];
    
    protected $casts = [
        'fecha_emision' => 'date',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
        'sii_fecha_envio' => 'datetime',
        'sii_respuesta' => 'array',
        'sii_enviado_automatico' => 'boolean',
    ];
    
    /**
     * Relación con el modelo Pedido
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
    
    /**
     * Generar número de documento automáticamente
     */
    public static function generarNumeroDocumento($tipo)
    {
        $prefijo = $tipo === 'factura' ? 'F' : 'B';
        $ultimo = self::where('tipo_documento', $tipo)
                     ->orderBy('id', 'desc')
                     ->first();
        
        if ($ultimo) {
            $numero = intval(substr($ultimo->numero_documento, 1)) + 1;
        } else {
            $numero = 1;
        }
        
        return $prefijo . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Calcular IVA (19% en Chile) - IVA incluido en el precio
     * El IVA ya está calculado y almacenado en la BD
     */
    public function calcularIVA()
    {
        return $this->iva;
    }
    
    /**
     * Calcular total
     */
    public function calcularTotal()
    {
        return $this->subtotal + $this->iva;
    }
    
    /**
     * Obtener precio neto (sin IVA)
     */
    public function getPrecioNeto()
    {
        return $this->subtotal;
    }
    
    /**
     * Obtener monto del IVA
     */
    public function getMontoIVA()
    {
        return $this->iva;
    }
    
    /**
     * Obtener precio con IVA incluido (total)
     */
    public function getPrecioConIVA()
    {
        return $this->total;
    }
    
    /**
     * Obtener el estado con formato amigable
     */
    public function getEstadoAmigableAttribute()
    {
        $estados = [
            'emitida' => 'Emitida',
            'pagada' => 'Pagada',
            'anulada' => 'Anulada'
        ];
        
        return $estados[$this->estado] ?? 'Desconocido';
    }
    
    /**
     * Obtener el color del badge según el estado
     */
    public function getColorEstadoAttribute()
    {
        $colores = [
            'emitida' => 'warning',
            'pagada' => 'success',
            'anulada' => 'danger'
        ];
        
        return $colores[$this->estado] ?? 'secondary';
    }
    
    /**
     * Scope para filtrar por tipo de documento
     */
    public function scopeTipoDocumento($query, $tipo)
    {
        return $query->where('tipo_documento', $tipo);
    }
    
    /**
     * Scope para filtrar por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }
    
    /**
     * Scope para filtrar por fecha
     */
    public function scopeFechaEmision($query, $fecha)
    {
        return $query->whereDate('fecha_emision', $fecha);
    }
    
    /**
     * Verificar si la factura ha sido enviada al SII
     */
    public function esEnviadaAlSii()
    {
        return !is_null($this->sii_track_id) && $this->sii_estado !== 'pendiente';
    }
    
    /**
     * Verificar si la factura fue aceptada por el SII
     */
    public function esAceptadaPorSii()
    {
        return $this->sii_estado === 'aceptado';
    }
    
    /**
     * Verificar si la factura fue rechazada por el SII
     */
    public function esRechazadaPorSii()
    {
        return $this->sii_estado === 'rechazado';
    }
    
    /**
     * Obtener el estado del SII con formato amigable
     */
    public function getSiiEstadoAmigableAttribute()
    {
        $estados = [
            'pendiente' => 'Pendiente de envío',
            'enviado' => 'Enviado al SII',
            'aceptado' => 'Aceptado por SII',
            'rechazado' => 'Rechazado por SII',
            'reparo' => 'Con reparos'
        ];
        
        return $estados[$this->sii_estado] ?? 'Estado desconocido';
    }
    
    /**
     * Obtener el color del badge según el estado SII
     */
    public function getColorSiiEstadoAttribute()
    {
        $colores = [
            'pendiente' => 'secondary',
            'enviado' => 'info',
            'aceptado' => 'success',
            'rechazado' => 'danger',
            'reparo' => 'warning'
        ];
        
        return $colores[$this->sii_estado] ?? 'secondary';
    }
    
    /**
     * Scope para filtrar por estado SII
     */
    public function scopeSiiEstado($query, $estado)
    {
        return $query->where('sii_estado', $estado);
    }
    
    /**
     * Scope para facturas no enviadas al SII
     */
    public function scopePendientesEnvioSii($query)
    {
        return $query->where('sii_estado', 'pendiente');
    }
    
    /**
     * Scope para facturas enviadas al SII
     */
    public function scopeEnviadasSii($query)
    {
        return $query->whereNotNull('sii_track_id');
    }
}

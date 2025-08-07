<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Mercancia;
use App\Models\Factura;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cliente_id',
        'fecha_entrega',
        'direccion_entrega',
        'horario_entrega',
        'condicion_pago',
        'formas_pago',
        'observacion',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        // Evento que se ejecuta antes de crear un pedido
        static::creating(function ($pedido) {
            // Si no se ha proporcionado una dirección de entrega, construirla automáticamente
            if (empty($pedido->direccion_entrega) && $pedido->cliente_id) {
                $cliente = Cliente::find($pedido->cliente_id);
                if ($cliente) {
                    $pedido->direccion_entrega = $cliente->obtenerDireccionCompleta();
                }
            }
        });
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mercancias()
    {
        return $this->belongsToMany(Mercancia::class, 'pedido_mercancia')
                    ->withPivot('cantidad_solicitada', 'precio_unitario')
                    ->withTimestamps();
    }
    
    // Relación legacy para compatibilidad - devuelve la primera mercancía como relación
    public function mercancia()
    {
        return $this->belongsTo(Mercancia::class, 'primera_mercancia_id');
    }
    
    // Método helper para obtener la primera mercancía (no es una relación)
    public function getPrimeraMercancia()
    {
        // Verificar si las mercancías están cargadas
        if (!$this->relationLoaded('mercancias')) {
            $this->load('mercancias');
        }
        
        // Devolver la primera mercancía o null si no hay ninguna
        return $this->mercancias->first();
    }
    
    /**
     * Relación con facturas
     */
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
    
    /**
     * Verificar si el pedido tiene factura
     */
    public function tieneFactura()
    {
        return $this->facturas()->exists();
    }
    
    /**
     * Calcular el precio total del pedido basado en todas las mercancías
     */
    public function calcularTotal()
    {
        // Si las mercancías no están cargadas, cargarlas
        if (!$this->relationLoaded('mercancias')) {
            $this->load('mercancias');
        }
        
        // Si no hay mercancías asociadas, retornar 0
        if ($this->mercancias->isEmpty()) {
            return 0;
        }
        
        $total = 0;
        foreach ($this->mercancias as $mercancia) {
            $precio = $mercancia->pivot->precio_unitario ?? $mercancia->precio_venta ?? 0;
            $cantidad = $mercancia->pivot->cantidad_solicitada ?? 1;
            $total += $precio * $cantidad;
        }
        return $total;
    }
    
    /**
     * Obtener cantidad total de artículos en el pedido
     */
    public function getCantidadTotal()
    {
        if (!$this->relationLoaded('mercancias')) {
            $this->load('mercancias');
        }
        
        if ($this->mercancias->isEmpty()) {
            return 0;
        }
        
        return $this->mercancias->sum('pivot.cantidad_solicitada') ?: 0;
    }
    
    /**
     * Verificar si el pedido tiene precios personalizados
     */
    public function tienePreciosPersonalizados()
    {
        if (!$this->relationLoaded('mercancias')) {
            $this->load('mercancias');
        }
        
        if ($this->mercancias->isEmpty()) {
            return false;
        }
        
        return $this->mercancias->whereNotNull('pivot.precio_unitario')->count() > 0;
    }
    
    /**
     * Obtener resumen de mercancías del pedido
     */
    public function getResumenMercancias()
    {
        if (!$this->relationLoaded('mercancias')) {
            $this->load('mercancias');
        }
        return $this->mercancias->map(function($mercancia) {
            return [
                'nombre' => $mercancia->nombre,
                'cantidad' => $mercancia->pivot->cantidad_solicitada,
                'precio_unitario' => $mercancia->pivot->precio_unitario ?? $mercancia->precio_venta,
                'subtotal' => ($mercancia->pivot->precio_unitario ?? $mercancia->precio_venta) * $mercancia->pivot->cantidad_solicitada
            ];
        });
    }
    
    /**
     * Obtener ID de la primera mercancía (compatibilidad)
     */
    public function getMercanciaIdAttribute()
    {
        if (!$this->relationLoaded('mercancias')) {
            $this->load('mercancias');
        }
        
        $primeraMercancia = $this->mercancias->first();
        return $primeraMercancia ? $primeraMercancia->id : null;
    }
    
    /**
     * Obtener ID de la primera mercancía para la relación legacy
     */
    public function getPrimeraMercanciaIdAttribute()
    {
        return $this->mercancia_id;
    }
    
    /**
     * Obtener cantidad solicitada de la primera mercancía (compatibilidad)
     */
    public function getCantidadSolicitadaAttribute()
    {
        if (!$this->relationLoaded('mercancias')) {
            $this->load('mercancias');
        }
        
        $primeraMercancia = $this->mercancias->first();
        return $primeraMercancia ? $primeraMercancia->pivot->cantidad_solicitada : 1;
    }
    
    /**
     * Obtener precio unitario efectivo de la primera mercancía (compatibilidad)
     */
    public function getPrecioUnitarioEfectivo()
    {
        if (!$this->relationLoaded('mercancias')) {
            $this->load('mercancias');
        }
        
        $primeraMercancia = $this->mercancias->first();
        if (!$primeraMercancia) {
            return 0;
        }
        
        return $primeraMercancia->pivot->precio_unitario ?? $primeraMercancia->precio_venta ?? 0;
    }
    
    /**
     * Verificar si tiene precio personalizado (compatibilidad)
     */
    public function tienePrecioPersonalizado()
    {
        if (!$this->relationLoaded('mercancias')) {
            $this->load('mercancias');
        }
        
        return $this->mercancias->whereNotNull('pivot.precio_unitario')->count() > 0;
    }
    
    /**
     * Obtener precio unitario representativo para el pedido
     * Si tiene un solo producto: devuelve su precio
     * Si tiene múltiples productos: devuelve precio promedio ponderado
     */
    public function getPrecioUnitarioRepresentativo()
    {
        if (!$this->relationLoaded('mercancias')) {
            $this->load('mercancias');
        }
        
        if ($this->mercancias->isEmpty()) {
            return 0;
        }
        
        // Si solo hay un producto, devolver su precio
        if ($this->mercancias->count() === 1) {
            $mercancia = $this->mercancias->first();
            return $mercancia->pivot->precio_unitario ?? $mercancia->precio_venta ?? 0;
        }
        
        // Si hay múltiples productos, calcular precio promedio ponderado
        $totalValor = 0;
        $totalCantidad = 0;
        
        foreach ($this->mercancias as $mercancia) {
            $precio = $mercancia->pivot->precio_unitario ?? $mercancia->precio_venta ?? 0;
            $cantidad = $mercancia->pivot->cantidad_solicitada ?? 1;
            
            $totalValor += $precio * $cantidad;
            $totalCantidad += $cantidad;
        }
        
        return $totalCantidad > 0 ? $totalValor / $totalCantidad : 0;
    }
    
    /**
     * Verificar si el pedido tiene múltiples productos diferentes
     */
    public function tieneMultiplesProductos()
    {
        if (!$this->relationLoaded('mercancias')) {
            $this->load('mercancias');
        }
        
        return $this->mercancias->count() > 1;
    }
    
    /**
     * Obtener lista de todos los precios unitarios del pedido
     * Devuelve un string con todos los precios separados por comas
     */
    public function getPreciosUnitariosFormateados()
    {
        if (!$this->relationLoaded('mercancias')) {
            $this->load('mercancias');
        }
        
        if ($this->mercancias->isEmpty()) {
            return '$0';
        }
        
        $precios = [];
        foreach ($this->mercancias as $mercancia) {
            $precio = $mercancia->pivot->precio_unitario ?? $mercancia->precio_venta ?? 0;
            $precios[] = '$' . number_format($precio, 0, ',', '.');
        }
        
        return implode(', ', $precios);
    }
    
    /**
     * Obtener array de precios unitarios sin formatear
     */
    public function getPreciosUnitarios()
    {
        if (!$this->relationLoaded('mercancias')) {
            $this->load('mercancias');
        }
        
        if ($this->mercancias->isEmpty()) {
            return [0];
        }
        
        $precios = [];
        foreach ($this->mercancias as $mercancia) {
            $precio = $mercancia->pivot->precio_unitario ?? $mercancia->precio_venta ?? 0;
            $precios[] = $precio;
        }
        
        return $precios;
    }
    
}

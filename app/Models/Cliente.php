<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rut',
        'razon_social',
        'giro',
        'ciudad',
        'comuna',
        'direccion_exacta',
        'tipo_vivienda',
        'correo_electronico',
        'telefono',
        'orden_atencion',
        'tipo_atencion',
        'lista_precios',
        'formas_pago',
        'condicion_pago',
    ];

    protected $casts = [
        'formas_pago' => 'array',
        'condicion_pago' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /**
     * Relación con el usuario que creó el cliente
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Relación con pedidos
     */
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }
    
    /**
     * Obtener la dirección completa del cliente
     */
    public function obtenerDireccionCompleta(): string
    {
        return collect([
            $this->direccion_exacta,
            $this->comuna,
            $this->ciudad
        ])->filter()->implode(', ');
    }
    
    /**
     * Obtener el RUT formateado con puntos
     */
    public function getRutFormateadoAttribute(): string
    {
        if (!$this->rut) return '';
        
        $rut = str_replace(['.', '-'], '', $this->rut);
        $dv = substr($rut, -1);
        $numero = substr($rut, 0, -1);
        
        return number_format($numero, 0, '', '.') . '-' . $dv;
    }
    
    /**
     * Accessor para nombre completo (alias de razon_social)
     */
    public function getNombreCompletoAttribute(): string
    {
        return $this->razon_social;
    }
    
    /**
     * Scope para buscar por RUT o razón social
     */
    public function scopeBuscar(Builder $query, string $termino): Builder
    {
        return $query->where(function (Builder $q) use ($termino) {
            $q->where('rut', 'like', "%{$termino}%")
              ->orWhere('razon_social', 'like', "%{$termino}%")
              ->orWhere('correo_electronico', 'like', "%{$termino}%");
        });
    }
    
    /**
     * Scope para filtrar por ciudad
     */
    public function scopePorCiudad(Builder $query, string $ciudad): Builder
    {
        return $query->where('ciudad', $ciudad);
    }
    
    /**
     * Scope para filtrar clientes por usuario (solo vendedores ven sus clientes)
     */
    public function scopeParaUsuario(Builder $query, $user = null): Builder
    {
        if (!$user) {
            $user = auth()->user();
        }
        
        // Si es superadmin, mostrar todos los clientes
        if ($user && $user->hasRole('superadmin')) {
            return $query;
        }
        
        // Si es vendedor, solo mostrar sus clientes
        return $query->where('user_id', $user?->id);
    }
    
}

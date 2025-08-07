<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pedido;

class Mercancia extends Model
{
    protected $fillable = [
    'nombre',
    'cantidad',
    'costo_compra',
    'precio_venta',
    'rentabilidad',
    'kilos_litros',
    ];
    
    /**
     * Relación con pedidos
     */
    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'pedido_mercancia')
                    ->withPivot('cantidad_solicitada', 'precio_unitario')
                    ->withTimestamps();
    }
}

<?php

namespace App\Services;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Collection;

class ClienteService
{
    /**
     * Tipos de vivienda disponibles
     */
    public const TIPOS_VIVIENDA = [
        'Local' => 'Local',
        'Casa' => 'Casa',
        'Departamento' => 'Departamento',
    ];

    /**
     * Tipos de teléfono disponibles
     */
    public const TIPOS_TELEFONO = [
        'celular' => 'Celular',
        'fijo' => 'Fijo',
    ];

    /**
     * Formas de pago disponibles
     */
    public const FORMAS_PAGO = [
        'Efectivo' => 'Efectivo',
        'Transferencia' => 'Transferencia',
        'Tarjeta' => 'Tarjeta',
    ];

    /**
     * Condiciones de pago disponibles
     */
    public const CONDICIONES_PAGO = [
        'Por pagar' => 'Por pagar',
        'Pagado' => 'Pagado',
    ];

    /**
     * Obtener clientes con paginación y búsqueda
     */
    public function obtenerClientesPaginados(
        ?string $busqueda = null,
        ?string $ciudad = null,
        int $porPagina = 15
    ): \Illuminate\Contracts\Pagination\LengthAwarePaginator {
        $query = Cliente::select([
            'id', 'rut', 'razon_social', 'correo_electronico', 
            'telefono', 'ciudad', 'updated_at'
        ]);

        if ($busqueda) {
            $query->buscar($busqueda);
        }

        if ($ciudad) {
            $query->porCiudad($ciudad);
        }

        return $query->orderBy('razon_social')
                    ->paginate($porPagina);
    }

    /**
     * Obtener ciudades únicas para filtros
     */
    public function obtenerCiudadesUnicas(): Collection
    {
        return Cliente::whereNotNull('ciudad')
                     ->distinct()
                     ->orderBy('ciudad')
                     ->pluck('ciudad');
    }

    /**
     * Formatear teléfono según tipo
     */
    public function formatearTelefono(string $numero, string $tipo): string
    {
        $prefijo = $tipo === 'celular' ? '+569' : '+562';
        return $prefijo . $numero;
    }

    /**
     * Formatear RUT completo
     */
    public function formatearRut(string $numero, string $dv): string
    {
        return $numero . '-' . strtoupper($dv);
    }

    /**
     * Obtener estadísticas de clientes
     */
    public function obtenerEstadisticas(): array
    {
        $total = Cliente::count();
        $conPedidos = Cliente::has('pedidos')->count();
        $ciudades = Cliente::distinct('ciudad')->count('ciudad');

        return [
            'total' => $total,
            'con_pedidos' => $conPedidos,
            'sin_pedidos' => $total - $conPedidos,
            'ciudades' => $ciudades,
        ];
    }

    /**
     * Verificar si un cliente puede ser eliminado
     */
    public function puedeEliminar(Cliente $cliente): bool
    {
        return !$cliente->pedidos()->exists();
    }
}

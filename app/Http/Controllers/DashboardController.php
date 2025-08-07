<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Mercancia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Obtener la fecha del filtro o usar la fecha de hoy por defecto
        $fechaSeleccionada = $request->get('fecha', Carbon::today()->format('Y-m-d'));
        $fechaCarbon = Carbon::parse($fechaSeleccionada);
        
        // Obtener pedidos con fecha de entrega de la fecha seleccionada
        $pedidosHoy = Pedido::with(['cliente', 'mercancias', 'user'])
            ->whereDate('fecha_entrega', $fechaCarbon)
            ->orderBy('horario_entrega')
            ->get();
        
        // Obtener mercancías asociadas a pedidos de la fecha seleccionada con sus cantidades
        // Ahora considerando precios personalizados y TODAS las mercancías de cada pedido
        // Separadas por horario (Mañana/Tarde)
        $mercanciasData = [
            'Mañana' => [],
            'Tarde' => []
        ];
        
        foreach ($pedidosHoy as $pedido) {
            // Iterar sobre TODAS las mercancías del pedido, no solo la primera
            foreach ($pedido->mercancias as $mercancia) {
                $mercanciaId = $mercancia->id;
                $horario = $pedido->horario_entrega;
                
                if (!isset($mercanciasData[$horario][$mercanciaId])) {
                    $mercanciasData[$horario][$mercanciaId] = [
                        'id' => $mercancia->id,
                        'nombre' => $mercancia->nombre,
                        'precio_venta_original' => $mercancia->precio_venta,
                        'stock_actual' => $mercancia->cantidad,
                        'cantidad_pedida' => 0,
                        'total_pedidos' => 0,
                        'valor_total' => 0,
                        'tiene_precios_personalizados' => false
                    ];
                }
                
                // Obtener cantidad y precio de esta mercancía específica en este pedido
                $cantidadSolicitada = $mercancia->pivot->cantidad_solicitada ?? 1;
                $precioUnitario = $mercancia->pivot->precio_unitario ?? $mercancia->precio_venta;
                $subtotal = $cantidadSolicitada * $precioUnitario;
                
                $mercanciasData[$horario][$mercanciaId]['cantidad_pedida'] += $cantidadSolicitada;
                $mercanciasData[$horario][$mercanciaId]['total_pedidos'] += 1;
                $mercanciasData[$horario][$mercanciaId]['valor_total'] += $subtotal;
                
                // Marcar si tiene precios personalizados
                if ($mercancia->pivot->precio_unitario !== null) {
                    $mercanciasData[$horario][$mercanciaId]['tiene_precios_personalizados'] = true;
                }
            }
        }
        
        // Convertir a colecciones por horario
        $mercanciasManana = collect(array_values($mercanciasData['Mañana'] ?? []));
        $mercanciasTarde = collect(array_values($mercanciasData['Tarde'] ?? []));
        $mercanciasHoy = $mercanciasManana->merge($mercanciasTarde); // Para mantener compatibilidad
        
        // Calcular comisiones/ventas según el rol del usuario
        $user = auth()->user();
        $comisionesPorVendedor = collect();
        $misVentas = null;
        
        if ($user->hasRole('superadmin')) {
            // Administradores ven todas las comisiones
            $comisionesPorVendedor = $this->calcularComisionesPorVendedor($pedidosHoy);
        } else {
            // Vendedores solo ven sus propias ventas
            $pedidosDelVendedor = $pedidosHoy->where('user_id', $user->id);
            $misVentas = $this->calcularVentasDelVendedor($pedidosDelVendedor, $user);
        }
        
        // Calcular estadísticas del día
        $estadisticas = [
            'total_pedidos' => $pedidosHoy->count(),
            'total_mercancias' => $mercanciasHoy->count(),
            'valor_total_pedidos' => $pedidosHoy->sum(function($pedido) {
                return $pedido->calcularTotal();
            }),
            'pedidos_manana' => $pedidosHoy->where('horario_entrega', 'Mañana')->count(),
            'pedidos_tarde' => $pedidosHoy->where('horario_entrega', 'Tarde')->count()
        ];
        
        // Nueva funcionalidad: Obtener ventas detalladas por vendedor (del mes)
        $ventasPorVendedor = $this->obtenerVentasPorVendedor($request, $fechaCarbon);
        
        return view('dashboard', compact('pedidosHoy', 'mercanciasHoy', 'mercanciasManana', 'mercanciasTarde', 'comisionesPorVendedor', 'misVentas', 'estadisticas', 'fechaSeleccionada', 'fechaCarbon', 'ventasPorVendedor'));
    }
    
    /**
     * Generar PDF con los pedidos del día
     */
    public function pedidosDelDiaPdf(Request $request)
    {
        // Configurar Carbon en español
        Carbon::setLocale('es');
        
        $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));
        $horario = $request->get('horario', 'todos');
        $fechaCarbon = Carbon::parse($fecha);
        
        // Obtener pedidos de la fecha y horario especificados
        $query = Pedido::with(['cliente', 'mercancias', 'user'])
            ->whereDate('fecha_entrega', $fechaCarbon);

        if ($horario !== 'todos') {
            $query->where('horario_entrega', $horario);
        }

        $pedidos = $query->orderBy('horario_entrega')
            ->orderBy('created_at')
            ->get();
        
        // Calcular estadísticas
        $estadisticas = [
            'total_pedidos' => $pedidos->count(),
            'valor_total' => $pedidos->sum(function($pedido) {
                return $pedido->calcularTotal();
            }),
            'pedidos_manana' => $pedidos->where('horario_entrega', 'Mañana')->count(),
            'pedidos_tarde' => $pedidos->where('horario_entrega', 'Tarde')->count()
        ];
        
        // Generar PDF
        $pdf = Pdf::loadView('dashboard.pedidos-pdf', compact('pedidos', 'estadisticas', 'fechaCarbon', 'horario'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('pedidos_' . $fechaCarbon->format('Y-m-d') . '_' . $horario . '.pdf');
    }
    
    /**
     * Generar PDF con las mercancías del día
     */
    public function mercanciasDelDiaPdf(Request $request)
    {
        // Configurar Carbon en español
        Carbon::setLocale('es');
        
        $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));
        $horario = $request->get('horario', 'todos');
        $fechaCarbon = Carbon::parse($fecha);
        
        // Obtener pedidos del día y horario especificados para procesar mercancías
        $query = Pedido::with(['mercancias'])->whereDate('fecha_entrega', $fechaCarbon);
        if ($horario !== 'todos') {
            $query->where('horario_entrega', $horario);
        }
        $pedidos = $query->get();
            
        // Procesar mercancías considerando precios personalizados y TODAS las mercancías
        $mercanciasData = [];
        
        foreach ($pedidos as $pedido) {
            // Iterar sobre TODAS las mercancías del pedido, no solo la primera
            foreach ($pedido->mercancias as $mercancia) {
                $mercanciaId = $mercancia->id;
                
                if (!isset($mercanciasData[$mercanciaId])) {
                    $mercanciasData[$mercanciaId] = [
                        'id' => $mercancia->id,
                        'nombre' => $mercancia->nombre,
                        'precio_venta' => $mercancia->precio_venta,
                        'stock_actual' => $mercancia->cantidad,
                        'cantidad_pedida' => 0,
                        'total_pedidos' => 0,
                        'valor_total' => 0,
                        'tiene_precios_personalizados' => false
                    ];
                }
                
                // Obtener cantidad y precio de esta mercancía específica en este pedido
                $cantidadSolicitada = $mercancia->pivot->cantidad_solicitada ?? 1;
                $precioUnitario = $mercancia->pivot->precio_unitario ?? $mercancia->precio_venta;
                $subtotal = $cantidadSolicitada * $precioUnitario;
                
                $mercanciasData[$mercanciaId]['cantidad_pedida'] += $cantidadSolicitada;
                $mercanciasData[$mercanciaId]['total_pedidos'] += 1;
                $mercanciasData[$mercanciaId]['valor_total'] += $subtotal;
                
                // Marcar si tiene precios personalizados
                if ($mercancia->pivot->precio_unitario !== null) {
                    $mercanciasData[$mercanciaId]['tiene_precios_personalizados'] = true;
                }
            }
        }
        
        // Convertir a colección y ordenar por cantidad pedida
        $mercancias = collect(array_values($mercanciasData))
            ->sortByDesc('cantidad_pedida')
            ->values();
        
        // Calcular estadísticas
        $estadisticas = [
            'total_mercancias' => $mercancias->count(),
            'cantidad_total_pedida' => $mercancias->sum('cantidad_pedida'),
            'valor_total' => $mercancias->sum('valor_total'),
            'mercancias_sin_stock' => $mercancias->where('stock_actual', '<=', 0)->count()
        ];
        
        // Generar PDF
        $pdf = Pdf::loadView('dashboard.mercancias-pdf', compact('mercancias', 'estadisticas', 'fechaCarbon', 'horario'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('mercancias_' . $fechaCarbon->format('Y-m-d') . '_' . $horario . '.pdf');
    }
    
    /**
     * Generar PDF con las ventas por vendedor
     * Solo accesible para administradores
     */
    public function ventasVendedorPdf(Request $request)
    {
        // Configurar Carbon en español
        Carbon::setLocale('es');
        
        $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));
        $vendedorId = $request->get('vendedor_id');
        $fechaCarbon = Carbon::parse($fecha);
        
        // Obtener datos de ventas por vendedor del mes actual
        $ventasPorVendedor = $this->obtenerVentasPorVendedor($request, $fechaCarbon);
        
        // Información del vendedor específico si se filtró
        $vendedorInfo = null;
        if ($vendedorId) {
            $vendedorInfo = \App\Models\User::find($vendedorId);
        }
        
        // Generar PDF
        $pdf = Pdf::loadView('dashboard.ventas-vendedor-pdf', compact(
            'ventasPorVendedor', 
            'fechaCarbon', 
            'vendedorInfo'
        ));
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'ventas_vendedor_' . $fechaCarbon->format('Y-m-d');
        if ($vendedorInfo) {
            $filename .= '_' . str_replace(' ', '_', $vendedorInfo->name);
        }
        $filename .= '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Calcular comisiones por vendedor
     * 
     * @param \Illuminate\Database\Eloquent\Collection $pedidos
     * @return \Illuminate\Support\Collection
     */
    private function calcularComisionesPorVendedor($pedidos)
    {
        $comisionesData = [];
        
        foreach ($pedidos as $pedido) {
            $userId = $pedido->user_id;
            
            if (!isset($comisionesData[$userId])) {
                $comisionesData[$userId] = [
                    'vendedor_id' => $userId,
                    'vendedor_nombre' => $pedido->user ? $pedido->user->name : 'Usuario sin nombre',
                    'total_pedidos' => 0,
                    'total_ventas' => 0,
                    'comision_porcentaje' => 5, // 5% de comisión por defecto - se puede hacer configurable
                    'comision_monto' => 0,
                    'pedidos' => []
                ];
            }
            
            $totalPedido = $pedido->calcularTotal();
            
            $comisionesData[$userId]['total_pedidos'] += 1;
            $comisionesData[$userId]['total_ventas'] += $totalPedido;
            
            // Crear resumen de mercancías para este pedido
            $mercanciasList = [];
            foreach ($pedido->mercancias as $mercancia) {
                $mercanciasList[] = $mercancia->nombre . ' (x' . ($mercancia->pivot->cantidad_solicitada ?? 1) . ')';
            }
            $nombresMercancias = implode(', ', $mercanciasList);
            
            $comisionesData[$userId]['pedidos'][] = [
                'id' => $pedido->id,
                'cliente' => $pedido->cliente->razon_social,
                'mercancia' => $nombresMercancias ?: 'Sin mercancía',
                'cantidad' => $pedido->getCantidadTotal(),
                'total' => $totalPedido
            ];
        }
        
        // Calcular comisiones
        foreach ($comisionesData as &$vendedor) {
            $vendedor['comision_monto'] = $vendedor['total_ventas'] * ($vendedor['comision_porcentaje'] / 100);
        }
        
        // Convertir a colección y ordenar por total de ventas
        return collect(array_values($comisionesData))
            ->sortByDesc('total_ventas')
            ->values();
    }
    
    /**
     * Calcular ventas de un vendedor específico (sin mostrar comisión)
     * 
     * @param \Illuminate\Database\Eloquent\Collection $pedidos
     * @param \App\Models\User $user
     * @return array
     */
    private function calcularVentasDelVendedor($pedidos, $user)
    {
        $totalVentas = 0;
        $totalPedidos = $pedidos->count();
        $detallesPedidos = [];
        
        foreach ($pedidos as $pedido) {
            $totalPedido = $pedido->calcularTotal();
            $totalVentas += $totalPedido;
            
            // Crear resumen de mercancías para este pedido
            $mercanciasList = [];
            foreach ($pedido->mercancias as $mercancia) {
                $mercanciasList[] = $mercancia->nombre . ' (x' . ($mercancia->pivot->cantidad_solicitada ?? 1) . ')';
            }
            $nombresMercancias = implode(', ', $mercanciasList);
            
            $detallesPedidos[] = [
                'id' => $pedido->id,
                'cliente' => $pedido->cliente->razon_social,
                'mercancia' => $nombresMercancias ?: 'Sin mercancía',
                'cantidad' => $pedido->getCantidadTotal(),
                'precio_unitario' => $pedido->getPrecioUnitarioRepresentativo(), // Para cálculos
                'precios_unitarios_formateados' => $pedido->getPreciosUnitariosFormateados(), // Para mostrar
                'tiene_multiples_productos' => $pedido->tieneMultiplesProductos(),
                'total' => $totalPedido,
                'horario_entrega' => $pedido->horario_entrega,
                'condicion_pago' => $pedido->condicion_pago
            ];
        }
        
        return [
            'vendedor_nombre' => $user->name,
            'total_pedidos' => $totalPedidos,
            'total_ventas' => $totalVentas,
            'pedidos' => $detallesPedidos
        ];
    }
    
    /**
     * Mostrar comisiones acumuladas por vendedor en un rango de fechas
     */
    public function comisionesAcumuladas(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $fechaInicioCarbon = Carbon::parse($fechaInicio);
        $fechaFinCarbon = Carbon::parse($fechaFin);
        
        // Obtener todos los pedidos en el rango de fechas
        $pedidos = Pedido::with(['cliente', 'mercancias', 'user'])
            ->whereBetween('fecha_entrega', [$fechaInicioCarbon, $fechaFinCarbon])
            ->get();
            
        $comisionesAcumuladas = $this->calcularComisionesPorVendedor($pedidos);
        
        return view('dashboard.comisiones-acumuladas', compact(
            'comisionesAcumuladas', 
            'fechaInicio', 
            'fechaFin', 
            'fechaInicioCarbon', 
            'fechaFinCarbon'
        ));
    }
    
    /**
     * Obtener ventas detalladas por vendedor para la pestaña de ventas (del mes actual)
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Carbon\Carbon $fechaCarbon
     * @return array
     */
    private function obtenerVentasPorVendedor($request, $fechaCarbon)
    {
        $user = auth()->user();
        $vendedorSeleccionado = $request->get('vendedor_id');
        
        // Obtener todos los pedidos del mes actual
        $inicioMes = $fechaCarbon->copy()->startOfMonth();
        $finMes = $fechaCarbon->copy()->endOfMonth();
        
        $pedidosDelMes = Pedido::with(['cliente', 'mercancias', 'user'])
            ->whereBetween('fecha_entrega', [$inicioMes, $finMes])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Si es vendedor, solo puede ver sus propias ventas
        if (!$user->hasRole('superadmin')) {
            $pedidosFiltrados = $pedidosDelMes->where('user_id', $user->id);
            $vendedores = collect([$user]); // Solo el usuario actual
        } else {
            // Si es admin, puede ver todos o filtrar por vendedor específico
            if ($vendedorSeleccionado) {
                $pedidosFiltrados = $pedidosDelMes->where('user_id', $vendedorSeleccionado);
            } else {
                $pedidosFiltrados = $pedidosDelMes;
            }
            
            // Obtener lista de vendedores que tienen pedidos en el mes
            $vendedores = \App\Models\User::whereIn('id', $pedidosDelMes->pluck('user_id')->unique())
                ->with('roles')
                ->get();
        }
        
        // Calcular datos por vendedor
        $ventasData = [];
        
        foreach ($pedidosFiltrados->groupBy('user_id') as $userId => $pedidosVendedor) {
            $vendedor = $pedidosVendedor->first()->user;
            
            if (!$vendedor) continue;
            
            $totalNeto = 0; // Total neto (precio con IVA incluido)
            $totalBruto = 0; // Total bruto (precio sin IVA)
            $totalCostos = 0; // Total de costos
            $detallesPedidos = [];
            
            foreach ($pedidosVendedor as $pedido) {
                $totalPedido = $pedido->calcularTotal(); // Precio con IVA
                
                // Total neto = precio de venta (con IVA incluido)
                $totalNeto += $totalPedido;
                
                // Total bruto = precio de venta sin IVA (restar el 19% del IVA)
                $totalBruto += $totalPedido * 0.81; // Equivale a $totalPedido - ($totalPedido * 0.19)
                
                // Calcular costos totales del pedido
                $costoTotalPedido = 0;
                foreach ($pedido->mercancias as $mercancia) {
                    $cantidadMercancia = $mercancia->pivot->cantidad_solicitada ?? 1;
                    $costoUnitario = $mercancia->costo_compra ?? 0;
                    $costoTotalPedido += $costoUnitario * $cantidadMercancia;
                }
                
                // Total de costos
                $totalCostos += $costoTotalPedido;
                
                // Crear resumen de mercancías para este pedido
                $mercanciasList = [];
                foreach ($pedido->mercancias as $mercancia) {
                    $mercanciasList[] = $mercancia->nombre . ' (x' . ($mercancia->pivot->cantidad_solicitada ?? 1) . ')';
                }
                $nombresMercancias = implode(', ', $mercanciasList);
                
                $primeraMercancia = $pedido->getPrimeraMercancia();
                $precioBaseMercancia = $primeraMercancia ? $primeraMercancia->precio_venta : 0;
                
                $detallesPedidos[] = [
                    'id' => $pedido->id,
                    'cliente' => $pedido->cliente->razon_social,
                    'cliente_rut' => $pedido->cliente->rut,
                    'mercancia' => $nombresMercancias ?: 'Sin mercancía',
                    'cantidad' => $pedido->getCantidadTotal(),
                    'precio_unitario' => $pedido->getPrecioUnitarioRepresentativo(), // Para cálculos
                    'precios_unitarios_formateados' => $pedido->getPreciosUnitariosFormateados(), // Para mostrar
                    'precios_unitarios_array' => $pedido->getPreciosUnitarios(), // Array de precios
                    'precio_base' => $precioBaseMercancia,
                    'precio_sin_iva' => $pedido->getPrecioUnitarioRepresentativo() * 0.81,
                    'costo_unitario' => $costoTotalPedido > 0 ? $costoTotalPedido / ($pedido->getCantidadTotal() ?: 1) : 0,
                    'costo_total' => $costoTotalPedido,
                    'tiene_precio_personalizado' => $pedido->tienePrecioPersonalizado(),
                    'tiene_multiples_productos' => $pedido->tieneMultiplesProductos(),
                    'total' => $totalPedido,
                    'total_sin_iva' => $totalPedido * 0.81,
                    'horario_entrega' => $pedido->horario_entrega,
                    'condicion_pago' => $pedido->condicion_pago,
                    'direccion_entrega' => $pedido->direccion_entrega,
                    'fecha_creacion' => $pedido->created_at
                ];
            }
            
            // Cálculos financieros
            $gananciaNeta = $totalNeto - $totalCostos; // Ganancia real (precio de venta - costos)
            $rentabilidadPorcentaje = $totalNeto > 0 ? (($gananciaNeta / $totalNeto) * 100) : 0;
            
            // Ordenar pedidos por hora de creación (más recientes primero)
            usort($detallesPedidos, function($a, $b) {
                return $b['fecha_creacion'] <=> $a['fecha_creacion'];
            });
            
            $ventasData[] = [
                'vendedor_id' => $userId,
                'vendedor_nombre' => $vendedor->name,
                'vendedor_username' => $vendedor->username,
                'vendedor_email' => $vendedor->email,
                'total_pedidos' => $pedidosVendedor->count(),
                'total_ventas' => $totalNeto, // Para compatibilidad (precio con IVA)
                'total_neto' => $totalNeto, // Total neto (precio con IVA incluido)
                'total_bruto' => $totalBruto, // Total bruto (precio sin IVA)
                'total_costos' => $totalCostos,
                'ganancia_neta' => $gananciaNeta, // Ganancia real (bruto - costos)
                'rentabilidad_porcentaje' => $rentabilidadPorcentaje,
                'promedio_por_pedido' => $pedidosVendedor->count() > 0 ? $totalNeto / $pedidosVendedor->count() : 0,
                'pedidos_manana' => $pedidosVendedor->where('horario_entrega', 'Mañana')->count(),
                'pedidos_tarde' => $pedidosVendedor->where('horario_entrega', 'Tarde')->count(),
                'clientes_unicos' => $pedidosVendedor->pluck('cliente_id')->unique()->count(),
                'productos_unicos' => $pedidosVendedor->pluck('mercancia_id')->unique()->count(),
                'comision_estimada' => $totalNeto * 0.05, // 5% de comisión sobre total neto
                'pedidos' => $detallesPedidos
            ];
        }
        
        // Ordenar por total de ventas (descendente)
        usort($ventasData, function($a, $b) {
            return $b['total_ventas'] <=> $a['total_ventas'];
        });
        
        return [
            'ventas' => $ventasData,
            'vendedores_disponibles' => $vendedores,
            'vendedor_seleccionado' => $vendedorSeleccionado,
            'es_admin' => $user->hasRole('superadmin'),
            'mes_actual' => $fechaCarbon->locale('es')->isoFormat('MMMM YYYY'),
            'inicio_mes' => $inicioMes,
            'fin_mes' => $finMes,
            'resumen_total' => [
                'total_vendedores' => count($ventasData),
                'total_pedidos' => array_sum(array_column($ventasData, 'total_pedidos')),
                'total_ventas' => array_sum(array_column($ventasData, 'total_ventas')), // Total con IVA
                'total_neto' => array_sum(array_column($ventasData, 'total_neto')), // Total con IVA
                'total_bruto' => array_sum(array_column($ventasData, 'total_bruto')), // Total sin IVA
                'total_costos' => array_sum(array_column($ventasData, 'total_costos')),
                'ganancia_neta_total' => array_sum(array_column($ventasData, 'ganancia_neta')), // Ganancia real
                'rentabilidad_general' => array_sum(array_column($ventasData, 'total_neto')) > 0 ? 
                    ((array_sum(array_column($ventasData, 'ganancia_neta')) / array_sum(array_column($ventasData, 'total_neto'))) * 100) : 0,
                'total_comisiones' => array_sum(array_column($ventasData, 'comision_estimada'))
            ]
        ];
    }
}

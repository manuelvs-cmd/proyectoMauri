<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Pedido;
use App\Services\SiiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $facturas = Factura::with(['pedido.cliente', 'pedido.mercancias'])
                          ->orderBy('created_at', 'desc')
                          ->get();
        
        return view('facturas.index', compact('facturas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $pedido_id = $request->get('pedido_id');
        $pedido = null;
        
        if ($pedido_id) {
            $pedido = Pedido::with(['cliente', 'mercancias'])->find($pedido_id);
            if (!$pedido) {
                return redirect()->route('pedidos.index')->with('error', 'Pedido no encontrado.');
            }
        } else {
            $pedidos = Pedido::with(['cliente', 'mercancias'])
                           ->whereDoesntHave('facturas')
                           ->get();
            return view('facturas.create', compact('pedidos'));
        }
        
        return view('facturas.create', compact('pedido'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'tipo_documento' => 'required|in:factura,boleta',
            'fecha_emision' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $pedido = Pedido::with('mercancias')->find($request->pedido_id);
            
            // Verificar si el pedido ya tiene factura
            if ($pedido->tieneFactura()) {
                return redirect()->back()->with('error', 'Este pedido ya tiene una factura asociada.');
            }
            
            // El precio ya incluye IVA, necesitamos separarlo
            $total = $pedido->calcularTotal(); // Precio con IVA incluido
            $subtotal = round($total / 1.19, 2); // Precio sin IVA
            $iva = $total - $subtotal; // IVA = Total - Subtotal
            
            $factura = Factura::create([
                'pedido_id' => $request->pedido_id,
                'tipo_documento' => $request->tipo_documento,
                'numero_documento' => Factura::generarNumeroDocumento($request->tipo_documento),
                'fecha_emision' => $request->fecha_emision,
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $total,
                'observaciones' => $request->observaciones,
                'sii_estado' => 'pendiente', // Estado inicial
            ]);
            
            DB::commit();
            
            return redirect()->route('facturas.show', $factura->id)
                           ->with('success', 'Factura creada exitosamente.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al crear la factura: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Factura $factura)
    {
        $factura->load(['pedido.cliente', 'pedido.mercancias', 'pedido.user']);
        return view('facturas.show', compact('factura'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Factura $factura)
    {
        return view('facturas.edit', compact('factura'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Factura $factura)
    {
        $request->validate([
            'estado' => 'required|in:emitida,pagada,anulada',
            'observaciones' => 'nullable|string',
        ]);
        
        $factura->update([
            'estado' => $request->estado,
            'observaciones' => $request->observaciones,
        ]);
        
        return redirect()->route('facturas.show', $factura->id)
                       ->with('success', 'Factura actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Factura $factura)
    {
        $factura->delete();
        
        return redirect()->route('facturas.index')
                       ->with('success', 'Factura eliminada exitosamente.');
    }
    
    /**
     * Generar PDF de la factura
     */
    public function pdf(Factura $factura)
    {
        $factura->load(['pedido.cliente', 'pedido.mercancias', 'pedido.user']);
        
        // Generar PDF usando DomPDF
        $pdf = Pdf::loadView('facturas.pdf', compact('factura'));
        
        // Configurar papel A4 y orientación vertical
        $pdf->setPaper('A4', 'portrait');
        
        // Descargar el PDF
        return $pdf->download($factura->tipo_documento . '_' . $factura->numero_documento . '.pdf');
    }
    
    /**
     * Mostrar vista previa del PDF
     */
    public function preview(Factura $factura)
    {
        $factura->load(['pedido.cliente', 'pedido.mercancias', 'pedido.user']);
        
        // Mostrar vista HTML para previsualizar
        return view('facturas.pdf', compact('factura'));
    }
    
    /**
     * Enviar factura al SII
     */
    public function enviarSii(Factura $factura, SiiService $siiService)
    {
        try {
            // Verificar si la factura ya fue enviada
            if ($factura->esEnviadaAlSii()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta factura ya ha sido enviada al SII'
                ]);
            }
            
            // Enviar al SII
            $resultado = $siiService->enviarFactura($factura);
            
            return response()->json($resultado);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar factura al SII',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Verificar estado de factura en el SII
     */
    public function verificarEstadoSii(Factura $factura, SiiService $siiService)
    {
        try {
            if (!$factura->esEnviadaAlSii()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta factura no ha sido enviada al SII'
                ]);
            }
            
            $resultado = $siiService->consultarEstado($factura);
            
            return response()->json($resultado);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar estado en el SII',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Envío masivo de facturas al SII
     */
    public function envioMasivoSii(Request $request, SiiService $siiService)
    {
        try {
            $request->validate([
                'factura_ids' => 'required|array',
                'factura_ids.*' => 'exists:facturas,id'
            ]);
            
            $facturaIds = $request->factura_ids;
            
            // Validar que las facturas no hayan sido enviadas ya
            $facturasYaEnviadas = Factura::whereIn('id', $facturaIds)
                                        ->where('sii_estado', '!=', 'pendiente')
                                        ->whereNotNull('sii_track_id')
                                        ->count();
            
            if ($facturasYaEnviadas > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Se encontraron {$facturasYaEnviadas} facturas ya enviadas al SII"
                ]);
            }
            
            // Realizar envío masivo
            $resultado = $siiService->envioMasivo($facturaIds);
            
            return response()->json([
                'success' => true,
                'message' => "Envío masivo completado: {$resultado['exitosos']} exitosos, {$resultado['fallidos']} fallidos",
                'resultado' => $resultado
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en envío masivo al SII',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Validar configuración del SII
     */
    public function validarConfiguracionSii(SiiService $siiService)
    {
        try {
            $validacion = $siiService->validarConfiguracion();
            
            return response()->json([
                'success' => true,
                'validacion' => $validacion
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al validar configuración SII',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Ver facturas pendientes de envío al SII
     */
    public function pendientesSii()
    {
        $facturasPendientes = Factura::with(['pedido.cliente'])
                                    ->where(function($query) {
                                        $query->where('sii_estado', 'pendiente')
                                              ->orWhereNull('sii_estado')
                                              ->orWhere('sii_estado', 'error');
                                    })
                                    ->orderBy('fecha_emision', 'desc')
                                    ->get();
        
        return view('facturas.pendientes-sii', compact('facturasPendientes'));
    }
    
    /**
     * Ver historial de envíos SII
     */
    public function historialSii()
    {
        $facturasEnviadas = Factura::with(['pedido.cliente'])
                                  ->whereNotNull('sii_track_id')
                                  ->orderBy('sii_fecha_envio', 'desc')
                                  ->paginate(20);
        
        return view('facturas.historial-sii', compact('facturasEnviadas'));
    }
    
    /**
     * Dashboard de SII - resumen de estados
     */
    public function dashboardSii()
    {
        $estadisticas = [
            'pendientes' => Factura::where('sii_estado', 'pendiente')
                                  ->orWhereNull('sii_estado')
                                  ->count(),
            'enviadas' => Factura::where('sii_estado', 'enviado')->count(),
            'aceptadas' => Factura::where('sii_estado', 'aceptado')->count(),
            'rechazadas' => Factura::where('sii_estado', 'rechazado')->count(),
            'errores' => Factura::where('sii_estado', 'error')->count(),
        ];
        
        // Facturas recientes
        $facturasRecientes = Factura::with(['pedido.cliente'])
                                   ->whereNotNull('sii_fecha_envio')
                                   ->orderBy('sii_fecha_envio', 'desc')
                                   ->limit(10)
                                   ->get();
        
        return view('facturas.dashboard-sii', compact('estadisticas', 'facturasRecientes'));
    }
}

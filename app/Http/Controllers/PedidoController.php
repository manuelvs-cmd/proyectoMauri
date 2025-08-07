<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\User;
use App\models\Cliente;
use App\models\Mercancia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    
    // Ver todos los pedidos del usuario autenticado
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('superadmin')) {
            // Si es superadmin, mostrar todos los pedidos
            $pedidos = Pedido::with(['cliente', 'mercancias', 'user', 'facturas'])->get();
        } else {
            // Si es vendedor, solo mostrar pedidos de sus clientes
            $pedidos = Pedido::with(['cliente', 'mercancias', 'user', 'facturas'])
                            ->whereHas('cliente', function($query) use ($user) {
                                $query->where('user_id', $user->id);
                            })
                            ->get();
        }
        
        return view('pedidos.index', compact('pedidos'));
    }

    // Mostrar el formulario para crear un nuevo pedido
    public function create()
    {
        // Filtrar clientes según el rol del usuario
        $clientes = \App\Models\Cliente::paraUsuario()->get();
        $mercancias = \App\Models\Mercancia::all();

        return view('pedidos.create-multiple', compact('clientes', 'mercancias'));
    }

    // Guardar un nuevo pedido
    public function store(Request $request)
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para crear un pedido.');
        }

        $validationRules = [
            'cliente_id' => 'required|exists:clientes,id',
            'mercancias' => 'required|array|min:1',
            'mercancias.*.mercancia_id' => 'required|exists:mercancias,id',
            'mercancias.*.cantidad_solicitada' => 'required|integer|min:1',
            'fecha_entrega' => 'required|date',
            'direccion_entrega' => 'nullable|string',
            'horario_entrega' => 'required|string',
            'condicion_pago' => 'required|in:Pagado,Por pagar',
            'formas_pago' => 'required|in:Efectivo,Transferencia,Tarjeta',
            'observacion' => 'nullable|string',
        ];
        
        // Solo superadmin puede modificar precios
        if (Auth::user()->hasRole('superadmin')) {
            $validationRules['mercancias.*.precio_unitario'] = 'nullable|numeric|min:0';
        }
        
        $request->validate($validationRules);

        // Validar stock disponible para todas las mercancías
        $stockErrors = [];
        foreach ($request->mercancias as $index => $mercanciaData) {
            $mercancia = \App\Models\Mercancia::find($mercanciaData['mercancia_id']);
            if (!$mercancia) {
                $stockErrors["mercancias.{$index}.mercancia_id"] = 'Mercancía no encontrada.';
                continue;
            }
            
            if ($mercanciaData['cantidad_solicitada'] > $mercancia->cantidad) {
                $stockErrors["mercancias.{$index}.cantidad_solicitada"] = 
                    "La cantidad solicitada ({$mercanciaData['cantidad_solicitada']}) excede el stock disponible ({$mercancia->cantidad}) para {$mercancia->nombre}";
            }
            
            if ($mercancia->cantidad <= 0) {
                $stockErrors["mercancias.{$index}.mercancia_id"] = "La mercancía {$mercancia->nombre} no tiene stock disponible.";
            }
        }
        
        if (!empty($stockErrors)) {
            return redirect()->back()->withInput()->withErrors($stockErrors);
        }

        // Obtener el cliente para construir la dirección automáticamente
        $cliente = \App\Models\Cliente::find($request->cliente_id);
        
        // Construir la dirección de entrega
        $direccion_entrega = $request->direccion_entrega;
        if (empty($direccion_entrega)) {
            $direccion_entrega = $cliente->obtenerDireccionCompleta();
        }

        // Usar transacción para asegurar consistencia
        try {
            DB::beginTransaction();
            
            // Crear el pedido
            $pedido = \App\Models\Pedido::create([
                'user_id' => Auth::id(),
                'cliente_id' => $request->cliente_id,
                'fecha_entrega' => $request->fecha_entrega,
                'direccion_entrega' => $direccion_entrega,
                'horario_entrega' => $request->horario_entrega,
                'condicion_pago' => $request->condicion_pago,
                'formas_pago' => $request->formas_pago,
                'observacion' => $request->observacion,
            ]);
            
            // Agregar mercancías al pedido y actualizar stock
            foreach ($request->mercancias as $mercanciaData) {
                $mercancia = \App\Models\Mercancia::find($mercanciaData['mercancia_id']);
                
                // Datos para la tabla pivote
                $pivotData = [
                    'cantidad_solicitada' => $mercanciaData['cantidad_solicitada']
                ];
                
                // Solo agregar precio personalizado si es superadmin y lo proporciona
                if (Auth::user()->hasRole('superadmin') && isset($mercanciaData['precio_unitario']) && !empty($mercanciaData['precio_unitario'])) {
                    $pivotData['precio_unitario'] = $mercanciaData['precio_unitario'];
                }
                
                // Asociar mercancía con el pedido
                $pedido->mercancias()->attach($mercanciaData['mercancia_id'], $pivotData);
                
                // Descontar del stock
                $mercancia->cantidad -= $mercanciaData['cantidad_solicitada'];
                $mercancia->save();
            }
            
            DB::commit();
            
            return redirect()->route('pedidos.index')->with('success', 'Pedido creado correctamente con ' . count($request->mercancias) . ' mercancías y stock actualizado.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors([
                'error' => 'Error al crear el pedido: ' . $e->getMessage()
            ]);
        }
    }

    // Ver un pedido específico
    public function show(Pedido $pedido)
    {
        $pedido->load('mercancias', 'cliente', 'user');
        return view('pedidos.show', compact('pedido'));
    }

    // Mostrar formulario para editar
    public function edit(Pedido $pedido)
    {
        $pedido->load('mercancias', 'cliente');
        $clientes = \App\Models\Cliente::paraUsuario()->get();
        $mercancias = \App\Models\Mercancia::all();
        
        return view('pedidos.edit-multiple', compact('pedido', 'clientes', 'mercancias'));
    }

    // Actualizar pedido
    public function update(Request $request, Pedido $pedido)
    {
        $validationRules = [
            'cliente_id' => 'required|exists:clientes,id',
            'mercancias' => 'required|array|min:1',
            'mercancias.*.mercancia_id' => 'required|exists:mercancias,id',
            'mercancias.*.cantidad_solicitada' => 'required|integer|min:1',
            'fecha_entrega' => 'required|date',
            'direccion_entrega' => 'nullable|string',
            'horario_entrega' => 'required|string',
            'condicion_pago' => 'required|in:Pagado,Por pagar',
            'formas_pago' => 'required|in:Efectivo,Transferencia,Tarjeta',
            'observacion' => 'nullable|string',
        ];
        
        // Solo superadmin puede modificar precios
        if (Auth::user()->hasRole('superadmin')) {
            $validationRules['mercancias.*.precio_unitario'] = 'nullable|numeric|min:0';
        }
        
        $request->validate($validationRules);

        // Cargar mercancías actuales del pedido
        $pedido->load('mercancias');
        
        // Validar stock disponible para todas las mercancías
        $stockErrors = [];
        foreach ($request->mercancias as $index => $mercanciaData) {
            $mercancia = \App\Models\Mercancia::find($mercanciaData['mercancia_id']);
            if (!$mercancia) {
                $stockErrors["mercancias.{$index}.mercancia_id"] = 'Mercancía no encontrada.';
                continue;
            }
            
            // Obtener cantidad anterior si existe esta mercancía en el pedido
            $cantidadAnterior = 0;
            $mercanciaExistente = $pedido->mercancias->firstWhere('id', $mercanciaData['mercancia_id']);
            if ($mercanciaExistente) {
                $cantidadAnterior = $mercanciaExistente->pivot->cantidad_solicitada;
            }
            
            // Stock disponible = stock actual + cantidad que tenía antes en este pedido
            $stockDisponible = $mercancia->cantidad + $cantidadAnterior;
            
            if ($mercanciaData['cantidad_solicitada'] > $stockDisponible) {
                $stockErrors["mercancias.{$index}.cantidad_solicitada"] = 
                    "La cantidad solicitada ({$mercanciaData['cantidad_solicitada']}) excede el stock disponible ({$stockDisponible}) para {$mercancia->nombre}";
            }
        }
        
        if (!empty($stockErrors)) {
            return redirect()->back()->withInput()->withErrors($stockErrors);
        }

        // Obtener el cliente para construir la dirección automáticamente
        $cliente = \App\Models\Cliente::find($request->cliente_id);
        
        // Construir la dirección de entrega
        $direccion_entrega = $request->direccion_entrega;
        if (empty($direccion_entrega)) {
            $direccion_entrega = $cliente->obtenerDireccionCompleta();
        }

        // Usar transacción para asegurar consistencia
        try {
            DB::beginTransaction();
            
            // Restaurar stock de las mercancías anteriores
            foreach ($pedido->mercancias as $mercanciaAnterior) {
                $mercanciaModel = \App\Models\Mercancia::find($mercanciaAnterior->id);
                $mercanciaModel->cantidad += $mercanciaAnterior->pivot->cantidad_solicitada;
                $mercanciaModel->save();
            }
            
            // Desconectar todas las mercancías anteriores
            $pedido->mercancias()->detach();
            
            // Actualizar datos básicos del pedido
            $pedido->update([
                'cliente_id' => $request->cliente_id,
                'fecha_entrega' => $request->fecha_entrega,
                'direccion_entrega' => $direccion_entrega,
                'horario_entrega' => $request->horario_entrega,
                'condicion_pago' => $request->condicion_pago,
                'formas_pago' => $request->formas_pago,
                'observacion' => $request->observacion,
            ]);
            
            // Agregar nuevas mercancías al pedido y actualizar stock
            foreach ($request->mercancias as $mercanciaData) {
                $mercancia = \App\Models\Mercancia::find($mercanciaData['mercancia_id']);
                
                // Datos para la tabla pivote
                $pivotData = [
                    'cantidad_solicitada' => $mercanciaData['cantidad_solicitada']
                ];
                
                // Solo agregar precio personalizado si es superadmin y lo proporciona
                if (Auth::user()->hasRole('superadmin') && isset($mercanciaData['precio_unitario']) && !empty($mercanciaData['precio_unitario'])) {
                    $pivotData['precio_unitario'] = $mercanciaData['precio_unitario'];
                }
                
                // Asociar mercancía con el pedido
                $pedido->mercancias()->attach($mercanciaData['mercancia_id'], $pivotData);
                
                // Descontar del stock
                $mercancia->cantidad -= $mercanciaData['cantidad_solicitada'];
                $mercancia->save();
            }
            
            DB::commit();
            
            return redirect()->route('pedidos.index')->with('success', 'Pedido actualizado correctamente con ' . count($request->mercancias) . ' mercancías y stock ajustado.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors([
                'error' => 'Error al actualizar el pedido: ' . $e->getMessage()
            ]);
        }
    }

        // Eliminar pedido
    public function destroy(Pedido $pedido)
    {
        try {
            DB::beginTransaction();
            
            // Restaurar el stock antes de eliminar el pedido
            foreach ($pedido->mercancias as $mercancia) {
                $cantidad_a_restaurar = $mercancia->pivot->cantidad_solicitada;
                $mercancia->cantidad += $cantidad_a_restaurar;
                $mercancia->save();
            }
            
            $pedido->delete();
            
            DB::commit();
            
            return redirect()->route('pedidos.index')->with('success', 'Pedido eliminado y stock restaurado.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'error' => 'Error al eliminar el pedido: ' . $e->getMessage()
            ]);
        }
    }
    /**
     * Obtener la direccion del cliente para AJAX
     */
    public function obtenerDireccionCliente($cliente_id)
    {
        $cliente = \App\Models\Cliente::find($cliente_id);
        
        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }
        
        $direccion_completa = $cliente->obtenerDireccionCompleta();
        
        return response()->json([
            'direccion_completa' => $direccion_completa,
            'ciudad' => $cliente->ciudad,
            'comuna' => $cliente->comuna,
            'direccion_exacta' => $cliente->direccion_exacta
        ]);
    }
    
    /**
     * Obtener el stock de la mercancía para AJAX
     */
    public function obtenerStockMercancia($mercancia_id)
    {
        $mercancia = \App\Models\Mercancia::find($mercancia_id);
        
        if (!$mercancia) {
            return response()->json(['error' => 'Mercancía no encontrada'], 404);
        }
        
        return response()->json([
            'stock' => $mercancia->cantidad,
            'nombre' => $mercancia->nombre,
            'precio_venta' => $mercancia->precio_venta,
            'disponible' => $mercancia->cantidad > 0,
            'estado' => $mercancia->cantidad > 0 ? 'disponible' : 'agotado'
        ]);
    }
    
    /**
     * Buscar mercancías por nombre para AJAX
     */
    public function buscarMercancias(Request $request)
    {
        $termino = $request->get('q', '');
        $mostrarTodas = $request->get('all', false);
        
        // Si se solicita mostrar todas o el término es muy corto, mostrar todas
        if ($mostrarTodas || strlen($termino) < 1) {
            $mercancias = \App\Models\Mercancia::orderBy('nombre')
                ->get();
        } else {
            $mercancias = \App\Models\Mercancia::where('nombre', 'LIKE', '%' . $termino . '%')
                ->orderBy('nombre')
                ->get();
        }
        
        $result = $mercancias->map(function($mercancia) {
            return [
                'id' => $mercancia->id,
                'nombre' => $mercancia->nombre,
                'stock' => $mercancia->cantidad,
                'precio_venta' => $mercancia->precio_venta,
                'disponible' => $mercancia->cantidad > 0,
                'texto_completo' => $mercancia->nombre . ' - Stock: ' . $mercancia->cantidad . ' - $' . number_format($mercancia->precio_venta, 0, ',', '.')
            ];
        });
        
        return response()->json($result);
    }
    
    /**
     * Construir la dirección completa combinando ciudad, comuna y dirección exacta
     */
    private function construirDireccionCompleta($cliente)
    {
        $direccion_partes = [];
        
        // Agregar dirección exacta si existe
        if (!empty($cliente->direccion_exacta)) {
            $direccion_partes[] = $cliente->direccion_exacta;
        }
        
        // Agregar comuna si existe
        if (!empty($cliente->comuna)) {
            $direccion_partes[] = $cliente->comuna;
        }
        
        // Agregar ciudad si existe
        if (!empty($cliente->ciudad)) {
            $direccion_partes[] = $cliente->ciudad;
        }
        
        // Unir las partes con comas
        return implode(', ', $direccion_partes);
    }
    
}

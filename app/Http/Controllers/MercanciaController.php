<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mercancia;
use Illuminate\Support\Facades\DB;

class MercanciaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Middlewares de permisos deshabilitados temporalmente
        // $this->middleware('permission:ver mercancías')->only(['index', 'show']);
        // $this->middleware('permission:crear mercancías')->only(['create', 'store']);
        // $this->middleware('permission:editar mercancías')->only(['edit', 'update']);
        // $this->middleware('permission:eliminar mercancías')->only(['destroy']);
    }
    public function create()
    {
        return view('mercancias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'cantidad' => 'required|integer|min:1',
            'costo_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'kilos_litros' => 'required|numeric|min:0',
        ]);

        $costoCompra = $request->costo_compra;
        $precioVenta = $request->precio_venta;

        $rentabilidad = 0;

        if ($costoCompra > 0) {
            $rentabilidad = (($precioVenta - $costoCompra) / $precioVenta) * 100;
        }

        Mercancia::create([
            'nombre' => $request->nombre,
            'cantidad' => $request->cantidad,
            'costo_compra' => $costoCompra,
            'precio_venta' => $precioVenta,
            'rentabilidad' => round($rentabilidad, 2),
            'kilos_litros' => $request->kilos_litros,
        ]);

        return redirect()->route('mercancias.index')->with('success', 'Mercancía agregada correctamente.');
    }

    public function index()
    {
        // Verificación simplificada - solo superadmin por ahora
        if (!auth()->user()->hasRole('superadmin')) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a mercancías.');
        }
        
        $mercancias = \App\Models\Mercancia::all();
        return view('mercancias.index', compact('mercancias'));
    }

    public function edit($id)
    {
        $mercancia = \App\Models\Mercancia::findOrFail($id);
        return view('mercancias.edit', compact('mercancia'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required',
            'cantidad' => 'required|integer|min:1',
            'costo_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'kilos_litros' => 'required|numeric|min:0',
        ]);

        $costoCompra = $request->costo_compra;
        $precioVenta = $request->precio_venta;

        $rentabilidad = 0;

        if ($costoCompra > 0) {
            $rentabilidad = (($precioVenta - $costoCompra) / $precioVenta) * 100;
        }

        $mercancia = \App\Models\Mercancia::findOrFail($id);
        $mercancia->update([
            'nombre' => $request->nombre,
            'cantidad' => $request->cantidad,
            'costo_compra' => $costoCompra,
            'precio_venta' => $precioVenta,
            'rentabilidad' => round($rentabilidad, 2),
            'kilos_litros' => $request->kilos_litros,
        ]);

        return redirect()->route('mercancias.index')->with('success', 'Mercancía actualizada.');
    }

    public function destroy($id)
    {
        $mercancia = \App\Models\Mercancia::findOrFail($id);
        $mercancia->delete();

        return redirect()->route('mercancias.index')->with('success', 'Mercancía eliminada.');
    }

    public function show($id)
    {
        $mercancia = \App\Models\Mercancia::findOrFail($id);
        return view('mercancias.show', compact('mercancia'));
    }
}


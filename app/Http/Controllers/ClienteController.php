<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\ClienteRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(): View
    {
        $clientes = Cliente::with('user')
                          ->select(['id', 'user_id', 'rut', 'razon_social', 'correo_electronico', 'telefono'])
                          ->paraUsuario()
                          ->orderBy('razon_social')
                          ->get();
        
        return view('clientes.index', compact('clientes'));
    }

    public function create(): View
    {
        return view('clientes.create');
    }

    public function store(ClienteRequest $request): RedirectResponse
    {
        try {
            $data = $request->getProcessedData();
            $data['user_id'] = auth()->id();
            Cliente::create($data);
            
            return redirect()
                ->route('clientes.index')
                ->with('success', 'Cliente creado correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el cliente. Intente nuevamente.');
        }
    }

    public function show(Cliente $cliente): View
    {
        if (!auth()->user()->hasRole('superadmin') && $cliente->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este cliente.');
        }
        
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente): View
    {
        // verificar autorización: el cliente debe pertenecer al usuario o ser superadmin
        if (!auth()->user()->hasRole('superadmin') && $cliente->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para editar este cliente.');
        }
        
        return view('clientes.edit', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        // verificar autorización: el cliente debe pertenecer al usuario o ser superadmin
        if (!auth()->user()->hasRole('superadmin') && $cliente->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para actualizar este cliente.');
        }
        
        try {
            $data = $request->getProcessedData();
            $cliente->update($data);
            
            return redirect()
                ->route('clientes.index')
                ->with('success', 'Cliente actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el cliente. Intente nuevamente.');
        }
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        // verificar autorización: el cliente debe pertenecer al usuario o ser superadmin
        if (!auth()->user()->hasRole('superadmin') && $cliente->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para eliminar este cliente.');
        }
        
        try {
            // Verificar si el cliente tiene pedidos asociados
            if ($cliente->pedidos()->exists()) {
                return redirect()
                    ->route('clientes.index')
                    ->with('warning', 'No se puede eliminar el cliente porque tiene pedidos asociados.');
            }
            
            $cliente->delete();
            
            return redirect()
                ->route('clientes.index')
                ->with('success', 'Cliente eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->route('clientes.index')
                ->with('error', 'Error al eliminar el cliente. Intente nuevamente.');
        }
    }
}

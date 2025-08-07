<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MercanciaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('home');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// 🔐 Rutas protegidas por login
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Cierre de sesión
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');

    // 🔐 Rutas de mercancias - Temporalmente sin middleware de rol
    // Route::middleware(['role:superadmin'])->group(function () {
        Route::get('/mercancias/opciones', function () {
            return view('mercancias.menu');
        })->name('mercancias.menu');

        // Rutas de gestión de usuarios (solo superadmin)
        Route::resource('users', UserController::class)->middleware('superadmin');
        
        // Mantener ruta de registro legacy (será reemplazada por users.create)
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('superadmin');
        Route::post('/register', [AuthController::class, 'register'])->middleware('superadmin');

        Route::get('/mercancias/index', [MercanciaController::class, 'index'])->name('mercancias.index');
        Route::get('/mercancias/agregar', [MercanciaController::class, 'create'])->name('mercancias.create');
        Route::post('/mercancias/agregar', [MercanciaController::class, 'store'])->name('mercancias.store');
        Route::get('/mercancias/{id}', [MercanciaController::class, 'show'])->name('mercancias.show');
        Route::get('/mercancias/{id}/editar', [MercanciaController::class, 'edit'])->name('mercancias.edit');
        Route::put('/mercancias/{id}', [MercanciaController::class, 'update'])->name('mercancias.update');
        Route::delete('/mercancias/{id}', [MercanciaController::class, 'destroy'])->name('mercancias.destroy');
    // });

    Route::get('/pedidos/cliente/{cliente_id}/direccion', [PedidoController::class, 'obtenerDireccionCliente'])->name('pedidos.cliente.direccion');
    Route::resource('pedidos', PedidoController::class);

    Route::resource('clientes', ClienteController::class);

});



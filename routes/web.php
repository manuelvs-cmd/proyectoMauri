<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MercanciaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FacturaController;

// Ruta principal - redirige al login o dashboard según el estado de autenticación
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

// Ruta opcional para la página home anterior (si se necesita)
Route::get('/home', function () {
    return view('home');
})->name('home');

// Rutas de desarrollo (solo en ambiente local)
if (app()->environment('local')) {
    Route::get('/test-login', function() {
        return view('test-login');
    });
    
    Route::get('/auth-check', function() {
        return response()->json([
            'authenticated' => \Auth::check(),
            'user' => \Auth::user()?->username,
            'environment' => app()->environment()
        ]);
    });
}

// Rutas protegidas por login
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/pedidos-pdf', [DashboardController::class, 'pedidosDelDiaPdf'])->name('dashboard.pedidos-pdf');
    Route::get('/dashboard/mercancias-pdf', [DashboardController::class, 'mercanciasDelDiaPdf'])->name('dashboard.mercancias-pdf');
    Route::get('/dashboard/ventas-vendedor-pdf', [DashboardController::class, 'ventasVendedorPdf'])->name('dashboard.ventas-vendedor-pdf')->middleware('superadmin');

    // Cierre de sesion
    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('login')->with('ok', 'Sesión cerrada correctamente');
    })->name('logout');
    
    // Rutas para comisiones (solo superadmin)
    Route::get('/dashboard/comisiones-acumuladas', [DashboardController::class, 'comisionesAcumuladas'])->name('dashboard.comisiones-acumuladas')->middleware('superadmin');

    // Rutas de mercancias - Temporalmente sin middleware de rol
    // Route::middleware(['role:superadmin'])->group(function () {
        Route::get('/mercancias/opciones', function () {
            return view('mercancias.menu');
        })->name('mercancias.menu');

        // Rutas de gestion de usuarios (solo superadmin)
        Route::resource('users', UserController::class)->middleware('superadmin');
        
        // Mantener ruta de registro legacy (sera reemplazada por users.create)
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
    Route::get('/pedidos/mercancia/{mercancia_id}/stock', [PedidoController::class, 'obtenerStockMercancia'])->name('pedidos.mercancia.stock');
    Route::get('/pedidos/buscar-mercancias', [PedidoController::class, 'buscarMercancias'])->name('pedidos.buscar-mercancias');
    Route::get('/pedidos/create-multiple', function() {
        $clientes = \App\Models\Cliente::paraUsuario()->get();
        $mercancias = \App\Models\Mercancia::all();
        return view('pedidos.create-multiple', compact('clientes', 'mercancias'));
    })->name('pedidos.create-multiple');
    Route::resource('pedidos', PedidoController::class);

    Route::resource('clientes', ClienteController::class);

    // Rutas de facturas
    Route::resource('facturas', FacturaController::class);
    Route::get('/facturas/{factura}/pdf', [FacturaController::class, 'pdf'])->name('facturas.pdf');
    Route::get('/facturas/{factura}/preview', [FacturaController::class, 'preview'])->name('facturas.preview');
    
    // Rutas SII - Facturación Electrónica
    Route::post('/facturas/{factura}/enviar-sii', [FacturaController::class, 'enviarSii'])->name('facturas.enviar-sii');
    Route::get('/facturas/{factura}/verificar-sii', [FacturaController::class, 'verificarEstadoSii'])->name('facturas.verificar-sii');
    Route::post('/facturas/envio-masivo-sii', [FacturaController::class, 'envioMasivoSii'])->name('facturas.envio-masivo-sii');
    Route::get('/facturas/validar-config-sii', [FacturaController::class, 'validarConfiguracionSii'])->name('facturas.validar-config-sii');
    Route::get('/facturas/pendientes-sii', [FacturaController::class, 'pendientesSii'])->name('facturas.pendientes-sii');
    Route::get('/facturas/historial-sii', [FacturaController::class, 'historialSii'])->name('facturas.historial-sii');
    Route::get('/facturas/dashboard-sii', [FacturaController::class, 'dashboardSii'])->name('facturas.dashboard-sii');

});

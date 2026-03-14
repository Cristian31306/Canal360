<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CarteraController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\LandingPageSettingsController;
use App\Models\Setting;
use App\Models\Aseguradora;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    $settings = Setting::where('group', 'like', 'landing_%')->get()->pluck('value', 'key');
    $aseguradoras = Aseguradora::whereNotNull('logo')->get(['nombre', 'logo']);
    
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'settings' => $settings,
        'aseguradoras' => $aseguradoras,
    ]);
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('clientes', ClienteController::class);
    Route::resource('aseguradoras', App\Http\Controllers\AseguradoraController::class);
    Route::resource('ramos', App\Http\Controllers\RamoController::class)->except(['show']);
    Route::resource('riesgos', App\Http\Controllers\RiesgoController::class);
    Route::get('/polizas/export', [App\Http\Controllers\PolizaController::class, 'export'])->name('polizas.export');
    Route::resource('polizas', App\Http\Controllers\PolizaController::class);
    Route::get('/renovaciones', [App\Http\Controllers\PolizaController::class, 'renewals'])->name('polizas.renewals');
    Route::post('/polizas/{poliza}/liquidar', [App\Http\Controllers\PolizaController::class, 'liquidate'])->name('polizas.liquidate');
    Route::post('/polizas/{poliza}/enviar', [App\Http\Controllers\PolizaController::class, 'sendToInsurance'])->name('polizas.send-to-insurance');
    Route::post('/polizas/{poliza}/finalizar-renovacion', [App\Http\Controllers\PolizaController::class, 'finalizeRenewal'])->name('polizas.finalize-renewal');
    Route::delete('/polizas/{poliza}/cancelar', [App\Http\Controllers\PolizaController::class, 'cancelRenewal'])->name('polizas.cancel-renewal');
    
    // Cartera y Abonos
    Route::get('/cartera/export', [CarteraController::class, 'export'])->name('cartera.export');
    Route::get('/cartera', [CarteraController::class, 'index'])->name('cartera.index');
    Route::get('/cartera/{id}', [CarteraController::class, 'show'])->name('cartera.show');
    Route::post('/cartera/{id}/abonos', [CarteraController::class, 'storeAbono'])->name('cartera.abonos.store');

    // CMS Landing Page
    Route::get('/settings/landing', [LandingPageSettingsController::class, 'index'])->name('settings.landing.index');
    Route::post('/settings/landing', [LandingPageSettingsController::class, 'update'])->name('settings.landing.update');

    // Administración de Usuarios
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/users/{user}/password-reset', [UserController::class, 'sendPasswordResetLink'])->name('admin.users.password.reset');
});

require __DIR__.'/auth.php';

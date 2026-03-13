<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\LandingPageSettingsController;
use App\Models\Setting;
use App\Models\Aseguradora;

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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('clientes', ClienteController::class);
    Route::resource('aseguradoras', App\Http\Controllers\AseguradoraController::class);
    Route::resource('ramos', App\Http\Controllers\RamoController::class)->except(['show']);
    Route::resource('riesgos', App\Http\Controllers\RiesgoController::class);
    Route::resource('polizas', App\Http\Controllers\PolizaController::class);

    // CMS Landing Page
    Route::get('/settings/landing', [LandingPageSettingsController::class, 'index'])->name('settings.landing.index');
    Route::post('/settings/landing', [LandingPageSettingsController::class, 'update'])->name('settings.landing.update');
});

require __DIR__.'/auth.php';

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CotizadorController extends Controller
{
    /**
     * Display the cotizador view.
     */
    public function index()
    {
        return Inertia::render('Cotizador/Index');
    }
}

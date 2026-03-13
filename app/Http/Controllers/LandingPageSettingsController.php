<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class LandingPageSettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::where('group', 'like', 'landing_%')->get();
        
        return Inertia::render('Settings/LandingPage', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        // El request vendrá como un array de configuraciones
        $settingsData = $request->input('settings', []);
        $files = $request->file('settings', []);

        foreach ($settingsData as $index => $item) {
            $key = $item['key'];
            $value = $item['value'] ?? null;

            // Si hay un archivo para este índice, lo procesamos
            if (isset($files[$index]['value']) && $files[$index]['value']->isValid()) {
                $file = $files[$index]['value'];
                $path = $file->store('landing', 'public');
                $value = '/storage/' . $path;
            }

            // Evitar guardar "[object File]" o valores nulos si ya existe una ruta
            if (is_string($value) && strpos($value, '[object') === false) {
                Setting::where('key', $key)->update(['value' => $value]);
            }
        }

        return Redirect::route('settings.landing.index')->with('success', 'Configuraciones actualizadas correctamente.');
    }
}

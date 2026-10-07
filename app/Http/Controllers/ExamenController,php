<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\Mascota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExamenController extends Controller
{
    public function store(Request $request, Mascota $mascota)
    {
        $data = $request->validate([
            'categoria'     => 'required|in:examen,laboratorio',
            'nombre'        => 'required|string|max:150',
            'fecha'         => 'required|date',
            'resultado'     => 'nullable|string',
            'observaciones' => 'nullable|string',
            'archivo'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('archivo')) {
            $data['archivo_ruta']   = $request->file('archivo')->store('examenes', 'public');
            $data['archivo_nombre'] = $request->file('archivo')->getClientOriginalName();
        }
        unset($data['archivo']);

        $data['mascota_id']     = $mascota->id;
        $data['veterinario_id'] = auth()->id();

        Examen::create($data);

        return response()->json(['message' => 'Registro guardado.']);
    }

    public function destroy(Examen $examen)
    {
        if ($examen->archivo_ruta) {
            Storage::disk('public')->delete($examen->archivo_ruta);
        }
        $examen->delete();

        return response()->json(['message' => 'Registro eliminado.']);
    }
}
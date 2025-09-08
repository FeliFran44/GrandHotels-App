<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotelController extends Controller
{
    // Ya no necesitamos el constructor aquí.

    public function index()
    {
        $hoteles = Hotel::all();
        return view('hoteles.index', ['hoteles' => $hoteles]);
    }

    public function create()
    {
        return view('hoteles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:hoteles',
            'ubicacion' => 'nullable|string|max:255',
        ]);
        Hotel::create($request->all());
        return redirect()->route('hoteles.index')->with('success', '¡Hotel guardado exitosamente!');
    }

    public function edit(Hotel $hotel)
    {
        return view('hoteles.edit', ['hotel' => $hotel]);
    }

    public function update(Request $request, Hotel $hotel)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:hoteles,nombre,' . $hotel->id,
            'ubicacion' => 'nullable|string|max:255',
        ]);
        $hotel->update($request->all());
        return redirect()->route('hoteles.index')->with('success', '¡Hotel actualizado exitosamente!');
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return redirect()->route('hoteles.index')->with('success', '¡Hotel eliminado exitosamente!');
    }
}

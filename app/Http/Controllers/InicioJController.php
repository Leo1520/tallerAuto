<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InicioJController extends Controller
{
    public function index()
    {
        return view('inicioJ');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        return redirect()->back()->with([
            'message' => 'Controlador InicioJ: acción ejecutada correctamente',
            'data' => $data,
        ]);
    }
}

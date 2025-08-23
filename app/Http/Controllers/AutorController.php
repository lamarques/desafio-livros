<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use Illuminate\Http\Request;

class AutorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $autores = Autor::all();
        return view('autor.index', compact('autores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('autor.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Nome' => 'required|string|max:255',
        ]);

        Autor::create($request->all());

        return redirect()->route('autor.index')->with('success', 'Autor Salvo com  sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Autor $autor)
    {
        return redirect()->route('autor.edit', $autor->CodAu);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Autor $autor)
    {
        return view('autor.edit', compact('autor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Autor $autor)
    {
        $request->validate([
            'Nome' => 'required|string|max:255',
        ]);

        $autor->update($request->all());

        return redirect()->route('autor.index')->with('success', 'Autor Atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Autor $autor)
    {
        $autor->delete();

        return redirect()->route('autor.index')->with('success', 'Autor apagado com sucesso.');
    }
}

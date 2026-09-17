<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Pergunta;

class EventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::withCount('perguntas')->latest()->get();

        return view('eventos.index', compact('eventos'));
    }

    public function show(Evento $evento)
    {
        $perguntas = Pergunta::where('evento_id', $evento->id)
            ->latest()
            ->paginate(10);

        return view('eventos.show', compact('evento', 'perguntas'));
    }

    public function storePergunta(\App\Http\Requests\StorePerguntaRequest $request)
    {
        Pergunta::create($request->validated());

        return back()->with('sucesso', 'Pergunta enviada com sucesso!');
    }
}
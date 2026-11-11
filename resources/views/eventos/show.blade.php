@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <!-- Cabeçalho do Evento -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <a href="{{ route('eventos.index') }}" class="text-blue-500 hover:underline text-sm">← Voltar para a lista</a>
        <h1 class="text-3xl font-bold text-gray-800 mt-2">{{ $evento->titulo }}</h1>
        <p class="text-gray-600 mt-2">{{ $evento->descricao }}</p>
    </div>

    <!-- Formulário para Nova Pergunta -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Faça sua Pergunta</h2>

        @if(session('sucesso'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('sucesso') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('perguntas.store') }}" method="POST">
            @csrf
            <input type="hidden" name="evento_id" value="{{ $evento->id }}">

            <div class="mb-4">
                <textarea name="texto" rows="3" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Digite sua pergunta aqui (mínimo 10 caracteres)...">{{ old('texto') }}</textarea>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                Enviar Pergunta
            </button>
        </form>
    </div>

    <!-- Lista de Perguntas -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Perguntas do Evento</h2>

        @forelse($perguntas as $pergunta)
            <div class="border-b border-gray-200 py-3 last:border-b-0">
                <p class="text-gray-800">{{ $pergunta->texto }}</p>
                <span class="text-xs text-gray-500">{{ $pergunta->created_at->diffForHumans() }}</span>
            </div>
        @empty
            <p class="text-gray-500 text-center py-4">Nenhuma pergunta enviada ainda. Seja o primeiro!</p>
        @endforelse

        <!-- Botões de Navegação (Paginação) -->
        <div class="mt-4">
            {{ $perguntas->links() }}
        </div>
    </div>
</div>
@endsection
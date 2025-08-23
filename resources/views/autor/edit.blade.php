@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edição do Autor {{ $autor->Nome  }}</h1>
        <form action="{{ route('autor.update', $autor->CodAu) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <input type="hidden" name="CodAu" value="{{ $autor->CodAu }}">
                <label for="Nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="Nome" name="Nome" required value="{{ old('Nome', $autor->Nome) }}">
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('autor.index') }}" class="btn btn-secondary">Voltar</a>
        </form>
    </div
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Novo Autor</h1>
        <form action="{{ route('autor.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="Nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="Nome" name="Nome" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('autor.index') }}" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
@endsection

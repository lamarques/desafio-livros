@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Autores</h1>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('autor.create') }}" class="btn btn-primary mb-3">Novo Autor</a>

        @if($autores->isEmpty())
            <div class="alert alert-info">Não há autores no cadastro.</div>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($autores as $autor)
                        <tr>
                            <td>{{ $autor->CodAu }}</td>
                            <td>{{ $autor->Nome }}</td>
                            <td>
                                <a href="{{ route('autor.edit', $autor->CodAu) }}" class="btn btn-warning btn-sm">Editar</a>
                                <form action="{{ route('autor.destroy', $autor->CodAu) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm delete-autor">Apagar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-autor').forEach(function(botao) {
                botao.addEventListener('click', function(e) {
                    if (!confirm('Tem certeza que deseja apagar este autor?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
@endsection

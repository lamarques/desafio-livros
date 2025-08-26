<?php

namespace Tests\Unit\Livros\Dto;

use App\Livros\Dtos\LivrosSaveDto;
use PHPUnit\Framework\TestCase;

class LivrosSaveDtoTest extends TestCase
{
    public function testCriaDtoComValoresValidos(): void
    {
        $dto = new LivrosSaveDto(
            titulo: 'O Guia do Programador',
            editora: 'TechBooks',
            edicao: 2,
            anoPublicacao: '2020',
            valor: 59.90,
            autores: [],
            assuntos: []
        );

        $this->assertSame('O Guia do Programador', $dto->titulo);
        $this->assertSame('TechBooks', $dto->editora);
        $this->assertSame(2, $dto->edicao);
        $this->assertSame('2020', $dto->anoPublicacao);
        $this->assertSame(59.90, $dto->valor);

        $this->assertIsString($dto->titulo);
        $this->assertIsString($dto->editora);
        $this->assertIsInt($dto->edicao);
        $this->assertIsString($dto->anoPublicacao);
        $this->assertIsFloat($dto->valor);
    }

    public function testLancaTypeErrorQuandoTituloNaoEhString(): void
    {
        $this->expectException(\TypeError::class);
        new LivrosSaveDto([], 'Editora', 1, '2020',17.97, [], []);
    }

    public function testLancaTypeErrorQuandoEditoraNaoEhString(): void
    {
        $this->expectException(\TypeError::class);
        new LivrosSaveDto('Titulo', [], 1, '2020',17.97, [], []);
    }

    public function testLancaTypeErrorQuandoEdicaoNaoEhInteiro(): void
    {
        $this->expectException(\TypeError::class);
        new LivrosSaveDto('Titulo', 'Editora', [], '2020',17.97, [], []);
    }

    public function testLancaTypeErrorQuandoAnoPublicacaoNaoEhString(): void
    {
        $this->expectException(\TypeError::class);
        new LivrosSaveDto('Titulo', 'Editora', 1, [], [], [], []);
    }

    public function testPermiteAlterarCamposComTiposValidos(): void
    {
        $dto = new LivrosSaveDto('A', 'B', 1, '2000',17.97, [], []);

        $dto->titulo = 'Novo Título';
        $dto->editora = 'Nova Editora';
        $dto->edicao = 3;
        $dto->anoPublicacao = '2024';

        $this->assertSame('Novo Título', $dto->titulo);
        $this->assertSame('Nova Editora', $dto->editora);
        $this->assertSame(3, $dto->edicao);
        $this->assertSame('2024', $dto->anoPublicacao);
    }

    public function testLancaTypeErrorAoAtribuirTiposInvalidos(): void
    {
        $dto = new LivrosSaveDto('A', 'B', 1, '2000',17.97, [], []);

        $this->expectException(\TypeError::class);
        $dto->edicao = 'nao-inteiro';
    }
}

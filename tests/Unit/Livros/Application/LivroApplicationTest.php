<?php

namespace Livros\Application;

use App\Livros\Application\LivroApplication;
use App\Livros\Application\Services\LivroService;
use App\Livros\Dtos\LivroRequestDto;
use App\Livros\Dtos\LivroResponseDto;
use PHPUnit\Framework\TestCase;

class LivroApplicationTest extends TestCase
{
    public function testGetLivroRetornaResponseDtoComDadosDoRepository(): void
    {
        $service = $this->createMock(LivroService::class);
        $request = new LivroRequestDto(1);

        $dto = new LivroResponseDto(
            Codl: 1,
            Titulo: 'O Guia do Programador',
            Editora: 'TechBooks',
            Edicao: 2,
            AnoPublicacao: '2020',
            Autores: [],
            Assuntos: []
        );

        $service->expects($this->once())
            ->method('getLivro')
            ->with($request)
            ->willReturn($dto);

        $app = new LivroApplication($service);

        $response = $app->getLivro($request);

        $this->assertInstanceOf(LivroResponseDto::class, $response);
        $this->assertSame(1, $response->Codl);
        $this->assertSame('O Guia do Programador', $response->Titulo);
        $this->assertSame('TechBooks', $response->Editora);
        $this->assertSame(2, $response->Edicao);
        $this->assertSame('2020', $response->AnoPublicacao);
    }

    public function testGetLivroPropagaExcecaoDoRepository(): void
    {
        $service = $this->createMock(LivroService::class);
        $request = new LivroRequestDto(1);

        $service->expects($this->once())
            ->method('getLivro')
            ->with($request)
            ->willThrowException(new \RuntimeException('Não encontrado'));

        $app = new LivroApplication($service);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Não encontrado');

        $app->getLivro($request);
    }
}

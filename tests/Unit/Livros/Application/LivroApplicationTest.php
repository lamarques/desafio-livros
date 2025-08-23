<?php

namespace Livros\Application;

use App\Livros\Application\LivroApplication;
use App\Livros\Application\Domain\Entity\Livro;
use App\Livros\Application\Domain\Repository\LivroRepositoryInterface;
use App\Livros\Dtos\LivroRequestDto;
use App\Livros\Dtos\LivroResponseDto;
use PHPUnit\Framework\TestCase;

class LivroApplicationTest extends TestCase
{
    public function testGetLivroRetornaResponseDtoComDadosDoRepository(): void
    {
        // Arrange
        $repo = $this->createMock(LivroRepositoryInterface::class);
        $request = $this->createStub(LivroRequestDto::class);

        $entity = new Livro(
            Codl: 1,
            Titulo: 'O Guia do Programador',
            Editora: 'TechBooks',
            Edicao: 2,
            AnoPublicacao: '2020'
        );

        $repo->expects($this->once())
            ->method('getLivro')
            ->with($request) // garante que o mesmo DTO foi repassado
            ->willReturn($entity);

        $app = new LivroApplication($repo);

        // Act
        $response = $app->getLivro($request);

        // Assert
        $this->assertInstanceOf(LivroResponseDto::class, $response);
        // Assumindo que o ResponseDto expõe getters equivalentes:
        $this->assertSame(1, $response->Codl);
        $this->assertSame('O Guia do Programador', $response->Titulo);
        $this->assertSame('TechBooks', $response->Editora);
        $this->assertSame(2, $response->Edicao);
        $this->assertSame('2020', $response->AnoPublicacao);
    }

    public function testGetLivroPropagaExcecaoDoRepository(): void
    {
        // Arrange
        $repo = $this->createMock(LivroRepositoryInterface::class);
        $request = $this->createStub(LivroRequestDto::class);

        $repo->expects($this->once())
            ->method('getLivro')
            ->with($request)
            ->willThrowException(new \RuntimeException('Não encontrado'));

        $app = new LivroApplication($repo);

        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Não encontrado');

        // Act
        $app->getLivro($request);
    }
}

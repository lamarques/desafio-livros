<?php

namespace Livros\Application\Services;

use App\Livros\Application\Domain\Entity\Livro as LivroEntity;
use App\Livros\Application\Domain\Repository\LivroRepositoryInterface;
use App\Livros\Application\Services\LivroService;
use App\Livros\Dtos\LivroRequestDto;
use App\Livros\Dtos\LivroResponseDto;
use App\Livros\Dtos\LivrosSaveDto;
use PHPUnit\Framework\TestCase;

class LivroServiceTest extends TestCase
{
    public function testCreateLivroChamaRepositorioEDevolveResponseDto(): void
    {
        $repo   = $this->createMock(LivroRepositoryInterface::class);
        $service = new LivroService($repo);

        $saveDto = new LivrosSaveDto(
            titulo: 'DDD',
            editora: 'Addison-Wesley',
            edicao: 1,
            anoPublicacao: '2003',
            autores: [10, 11],
            assuntos: [5, 6]
        );

        // 1) Salva
        $repo->expects($this->once())
            ->method('saveLivro')
            ->with($this->callback(function ($arg) use ($saveDto) {
                // Confere que o DTO foi repassado corretamente
                $p = get_object_vars($arg);
                $q = get_object_vars($saveDto);
                return $p == $q;
            }))
            ->willReturn(true);

        // 2) Obtém o último ID
        $repo->expects($this->once())
            ->method('getLastInsertedId')
            ->willReturn(42);

        // 3) Busca o Livro recém-criado (a repo retorna ENTIDADE)
        $repo->expects($this->once())
            ->method('getLivro')
            ->with($this->callback(function ($dto) {
                return $dto instanceof LivroRequestDto && $dto->Codl === 42;
            }))
            ->willReturn(
                new LivroEntity(
                    Codl: 42,
                    Titulo: 'DDD',
                    Editora: 'Addison-Wesley',
                    Edicao: 1,
                    AnoPublicacao: '2003',
                    Autores: [10,11],
                    Assuntos: [5,6]
                )
            );

        $resp = $service->createLivro($saveDto);

        $this->assertInstanceOf(LivroResponseDto::class, $resp);
        $this->assertSame(42, $resp->Codl);
        $this->assertSame('DDD', $resp->Titulo);
        $this->assertSame('Addison-Wesley', $resp->Editora);
        $this->assertSame(1, $resp->Edicao);
        $this->assertSame('2003', $resp->AnoPublicacao);
        $this->assertSame([10,11], $resp->Autores);
        $this->assertSame([5,6], $resp->Assuntos);
    }

    public function testListLivrosRetornaArrayDoRepositorio(): void
    {
        $repo = $this->createMock(LivroRepositoryInterface::class);
        $service = new LivroService($repo);

        $lista = [
            new LivroResponseDto(1, 'A', 'X', 1, '2000', [], []),
            new LivroResponseDto(2, 'B', 'Y', 2, '2001', [9], [4]),
        ];

        $repo->expects($this->once())
            ->method('listLivros')
            ->willReturn($lista);

        $out = $service->listLivros();

        $this->assertSame($lista, $out);
        $this->assertContainsOnlyInstancesOf(LivroResponseDto::class, $out);
        $this->assertCount(2, $out);
    }

    public function testGetLivroRetornaNullQuandoRepositorioNaoEncontra(): void
    {
        $repo = $this->createMock(LivroRepositoryInterface::class);
        $service = new LivroService($repo);

        $dtoReq = new LivroRequestDto(999);

        $repo->expects($this->once())
            ->method('getLivro')
            ->with($dtoReq)
            ->willReturn(null);

        $this->assertNull($service->getLivro($dtoReq));
    }

    public function testGetLivroRetornaResponseDtoQuandoRepositorioRetornaEntidade(): void
    {
        $repo = $this->createMock(LivroRepositoryInterface::class);
        $service = new LivroService($repo);

        $dtoReq = new LivroRequestDto(7);

        $repo->expects($this->once())
            ->method('getLivro')
            ->with($dtoReq)
            ->willReturn(
                new LivroEntity(
                    Codl: 7,
                    Titulo: 'Clean Architecture',
                    Editora: 'Prentice Hall',
                    Edicao: 2,
                    AnoPublicacao: '2017',
                    Autores: [1,2],
                    Assuntos: [3]
                )
            );

        $resp = $service->getLivro($dtoReq);

        $this->assertInstanceOf(LivroResponseDto::class, $resp);
        $this->assertSame(7, $resp->Codl);
        $this->assertSame('Clean Architecture', $resp->Titulo);
        $this->assertSame('Prentice Hall', $resp->Editora);
        $this->assertSame(2, $resp->Edicao);
        $this->assertSame('2017', $resp->AnoPublicacao);
        $this->assertSame([1,2], $resp->Autores);
        $this->assertSame([3], $resp->Assuntos);
    }

    public function testUpdateLivroRetornaFalseQuandoLivroNaoExiste(): void
    {
        $repo = $this->createMock(LivroRepositoryInterface::class);
        $service = new LivroService($repo);

        $codl = new LivroRequestDto(123);
        $save = new LivrosSaveDto('T', 'E', 1, '2000', [], []);

        $repo->expects($this->once())
            ->method('getLivro')
            ->with($codl)
            ->willReturn(null);

        $repo->expects($this->never())
            ->method('updateLivro');

        $this->assertFalse($service->updateLivro($codl, $save));
    }

    public function testUpdateLivroAtualizaEPassaDTOsQuandoExiste(): void
    {
        $repo = $this->createMock(LivroRepositoryInterface::class);
        $service = new LivroService($repo);

        $codl = new LivroRequestDto(77);
        $save = new LivrosSaveDto('Novo', 'Editora B', 3, '2024', [1], [2]);

        $repo->expects($this->once())
            ->method('getLivro')
            ->with($codl)
            ->willReturn(
                new LivroEntity(77, 'Antigo', 'Editora A', 1, '2000', [], [])
            );

        $repo->expects($this->once())
            ->method('updateLivro')
            ->with(
                $this->callback(fn($arg) => $arg instanceof LivroRequestDto && $arg->Codl === 77),
                $this->callback(function ($arg) use ($save) {
                    // Confere que os dados foram repassados corretamente
                    return get_object_vars($arg) === get_object_vars($save);
                })
            )
            ->willReturn(true);

        $this->assertTrue($service->updateLivro($codl, $save));
    }

    public function testDeleteLivroDelegatesERetornaBoolTrue(): void
    {
        $repo = $this->createMock(LivroRepositoryInterface::class);
        $service = new LivroService($repo);

        $codl = new LivroRequestDto(9);

        $repo->expects($this->once())
            ->method('deleteLivro')
            ->with($codl)
            ->willReturn(true);

        $this->assertTrue($service->deleteLivro($codl));
    }

    public function testDeleteLivroDelegatesERetornaBoolFalse(): void
    {
        $repo = $this->createMock(LivroRepositoryInterface::class);
        $service = new LivroService($repo);

        $codl = new LivroRequestDto(10);

        $repo->expects($this->once())
            ->method('deleteLivro')
            ->with($codl)
            ->willReturn(false);

        $this->assertFalse($service->deleteLivro($codl));
    }
}

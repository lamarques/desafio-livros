<?php

namespace Livros\Application\Services;

use App\Livros\Application\Domain\Entity\Autor as AutorEntity;
use App\Livros\Application\Domain\Repository\AutorRepositoryInterface;
use App\Livros\Application\Services\AutorService;
use PHPUnit\Framework\TestCase;

class AutorServiceTest extends TestCase
{
    public function testListRetornaArrayMapeadoCorretamente(): void
    {
        $repo = $this->createMock(AutorRepositoryInterface::class);

        $autores = [
            new AutorEntity(CodAu: 1, Nome: 'Clarice Lispector'),
            new AutorEntity(CodAu: 2, Nome: 'Machado de Assis'),
        ];

        $repo->expects($this->once())
            ->method('getAllAutores')
            ->willReturn($autores);

        $service = new AutorService($repo);

        $resultado = $service->list();

        $esperado = [
            ['CodAu' => 1, 'Nome' => 'Clarice Lispector'],
            ['CodAu' => 2, 'Nome' => 'Machado de Assis'],
        ];
        $this->assertSame($esperado, $resultado);
    }

    public function testListRetornaArrayVazioQuandoRepositoryNaoTemAutores(): void
    {
        $repo = $this->createMock(AutorRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getAllAutores')
            ->willReturn([]);

        $service = new AutorService($repo);

        $resultado = $service->list();

        $this->assertSame([], $resultado);
    }
}

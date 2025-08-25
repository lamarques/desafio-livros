<?php

namespace Livros\Application;

use App\Livros\Application\AssuntoApplication;
use App\Livros\Application\Services\AssuntoService;
use App\Livros\Dtos\AssuntoRequestDto;
use App\Livros\Dtos\AssuntoResponseDto;
use App\Livros\Exceptions\AssuntoException;
use PHPUnit\Framework\TestCase;

class AssuntoApplicationTest extends TestCase
{
    public function testCreateDelegatesToServiceAndReturnsResponseDto(): void
    {
        $service = $this->createMock(AssuntoService::class);
        $app     = new AssuntoApplication($service);

        $request  = new AssuntoRequestDto(Descricao: 'Matemática');
        $expected = new AssuntoResponseDto(CodAs: 10, Descricao: 'Matemática');

        $service->expects($this->once())
            ->method('create')
            ->with($request)
            ->willReturn($expected);

        $response = $app->create($request);

        $this->assertInstanceOf(AssuntoResponseDto::class, $response);
        $this->assertSame(10, $response->CodAs);
        $this->assertSame('Matemática', $response->Descricao);
    }

    public function testCreatePropagaExcecaoDoService(): void
    {
        $service = $this->createMock(AssuntoService::class);
        $app     = new AssuntoApplication($service);

        $request = new AssuntoRequestDto(Descricao: 'Duplicado');

        $service->expects($this->once())
            ->method('create')
            ->with($request)
            ->willThrowException(AssuntoException::alreadyExists('Duplicado'));

        $this->expectException(AssuntoException::class);
        $this->expectExceptionMessage("Assunto 'Duplicado' já existe.");

        $app->create($request);
    }

    public function testListRetornaArrayVazioQuandoServiceNaoTemAssuntos(): void
    {
        $service = $this->createMock(AssuntoService::class);
        $service->expects($this->once())
            ->method('list')
            ->willReturn([]);

        $app = new AssuntoApplication($service);

        $resultado = $app->list();

        $this->assertIsArray($resultado);
        $this->assertCount(0, $resultado);
    }

    public function testListRetornaArrayDeResponseDtoComDados(): void
    {
        $service = $this->createMock(AssuntoService::class);

        $dto1 = new AssuntoResponseDto(CodAs: 1, Descricao: 'Redes');
        $dto2 = new AssuntoResponseDto(CodAs: 2, Descricao: 'Banco de Dados');

        $service->expects($this->once())
            ->method('list')
            ->willReturn([$dto1, $dto2]);

        $app = new AssuntoApplication($service);

        $resultado = $app->list();

        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
        $this->assertContainsOnlyInstancesOf(AssuntoResponseDto::class, $resultado);

        $this->assertSame(1, $resultado[0]->CodAs);
        $this->assertSame('Redes', $resultado[0]->Descricao);

        $this->assertSame(2, $resultado[1]->CodAs);
        $this->assertSame('Banco de Dados', $resultado[1]->Descricao);
    }

    public function testListPropagaExcecaoDoService(): void
    {
        $service = $this->createMock(AssuntoService::class);
        $service->expects($this->once())
            ->method('list')
            ->willThrowException(new \RuntimeException('Falha ao listar'));

        $app = new AssuntoApplication($service);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Falha ao listar');

        $app->list();
    }

    public function testShowRetornaDtoQuandoEncontrado(): void
    {
        $service = $this->createMock(AssuntoService::class);
        $dto = new AssuntoResponseDto(CodAs: 1, Descricao: 'Redes');

        $service->expects($this->once())
            ->method('show')
            ->with(1)
            ->willReturn($dto);

        $app = new AssuntoApplication($service);

        $result = $app->show(1);

        $this->assertInstanceOf(AssuntoResponseDto::class, $result);
        $this->assertSame(1, $result->CodAs);
        $this->assertSame('Redes', $result->Descricao);
    }

    public function testShowRetornaNullQuandoNaoEncontrado(): void
    {
        $service = $this->createMock(AssuntoService::class);
        $service->expects($this->once())
            ->method('show')
            ->with(999)
            ->willReturn(null);

        $app = new AssuntoApplication($service);

        $result = $app->show(999);

        $this->assertNull($result);
    }
}

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
}

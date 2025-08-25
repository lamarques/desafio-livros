<?php

namespace Livros\Application;

use App\Livros\Application\AutorApplication;
use App\Livros\Application\Services\AutorService;
use PHPUnit\Framework\TestCase;

class AutorApplicationTest extends TestCase
{
    public function testListRetornaArrayDoService(): void
    {
        $payload = [
            ['CodAu' => 1, 'Nome' => 'Clarice Lispector'],
            ['CodAu' => 2, 'Nome' => 'Machado de Assis'],
        ];

        $service = $this->createMock(AutorService::class);
        $service->expects($this->once())
            ->method('list')
            ->willReturn($payload);

        $app = new AutorApplication($service);

        $resultado = $app->list();

        $this->assertSame($payload, $resultado);
    }

    public function testListRetornaVazioQuandoServiceRetornaVazio(): void
    {
        $service = $this->createMock(AutorService::class);
        $service->expects($this->once())
            ->method('list')
            ->willReturn([]);

        $app = new AutorApplication($service);

        $resultado = $app->list();

        $this->assertSame([], $resultado);
    }
}

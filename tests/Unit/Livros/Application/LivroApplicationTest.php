<?php

namespace Livros\Application;

use App\Livros\Application\LivroApplication;
use App\Livros\Application\Services\LivroService;
use App\Livros\Dtos\LivroRequestDto;
use App\Livros\Dtos\LivroResponseDto;
use App\Livros\Dtos\LivrosSaveDto;
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

    public function testGetLivroRetornaNullQuandoServiceRetornaNull(): void
    {
        $service = $this->createMock(LivroService::class);
        $request = new LivroRequestDto(999);

        $service->expects($this->once())
            ->method('getLivro')
            ->with($request)
            ->willReturn(null);

        $app = new LivroApplication($service);

        $this->assertNull($app->getLivro($request));
    }

    public function testCreateLivroMontaSaveDtoEDelegaeRetornaResponse(): void
    {
        $service = $this->createMock(LivroService::class);
        $app     = new LivroApplication($service);

        $input = [
            'Titulo'        => 'DDD',
            'Editora'       => 'Addison-Wesley',
            'Edicao'        => 1,
            'AnoPublicacao' => '2003',
            'AutorID'       => [10, 11],
            'AssuntoID'     => [5, 6],
        ];

        $expected = new LivroResponseDto(7, 'DDD', 'Addison-Wesley', 1, '2003', [10, 11], [5, 6]);

        $service->expects($this->once())
            ->method('createLivro')
            ->with($this->callback(function ($arg) use ($input) {
                if (!$arg instanceof LivrosSaveDto) return false;
                $p = get_object_vars($arg);
                $titulo        = $p['titulo']        ?? $p['Titulo']        ?? null;
                $editora       = $p['editora']       ?? $p['Editora']       ?? null;
                $edicao        = $p['edicao']        ?? $p['Edicao']        ?? null;
                $anoPublicacao = $p['anoPublicacao'] ?? $p['AnoPublicacao'] ?? null;
                $autores       = $p['autores']       ?? null;
                $assuntos      = $p['assuntos']      ?? null;

                return $titulo        === $input['Titulo']
                    && $editora       === $input['Editora']
                    && $edicao        === $input['Edicao']
                    && $anoPublicacao === $input['AnoPublicacao']
                    && $autores       === $input['AutorID']
                    && $assuntos      === $input['AssuntoID'];
            }))
            ->willReturn($expected);

        $resp = $app->createLivro($input);

        $this->assertInstanceOf(LivroResponseDto::class, $resp);
        $this->assertSame(7, $resp->Codl);
        $this->assertSame('DDD', $resp->Titulo);
    }

    public function testListLivrosDelegaeRetornaListaDeDtos(): void
    {
        $service = $this->createMock(LivroService::class);
        $app     = new LivroApplication($service);

        $list = [
            new LivroResponseDto(1, 'A', 'Ed1', 1, '2010', [], []),
            new LivroResponseDto(2, 'B', 'Ed2', 2, '2015', [], []),
        ];

        $service->expects($this->once())
            ->method('listLivros')
            ->willReturn($list);

        $resp = $app->listLivros();

        $this->assertIsArray($resp);
        $this->assertCount(2, $resp);
        $this->assertContainsOnlyInstancesOf(LivroResponseDto::class, $resp);
        $this->assertSame('A', $resp[0]->Titulo);
        $this->assertSame('B', $resp[1]->Titulo);
    }

    public function testUpdateLivroMontaDtosEDelegaeRetornandoBool(): void
    {
        $service = $this->createMock(LivroService::class);
        $app     = new LivroApplication($service);

        $input = [
            'Titulo'        => 'Novo',
            'Editora'       => 'Editora X',
            'Edicao'        => 3,
            'AnoPublicacao' => '2024',
            'AutorID'       => [10],
            'AssuntoID'     => [5],
        ];

        $service->expects($this->once())
            ->method('updateLivro')
            ->with(
                $this->callback(function ($req) {
                    return $req instanceof LivroRequestDto
                        && (get_object_vars($req)['Codl'] ?? null) === 9;
                }),
                $this->isInstanceOf(LivrosSaveDto::class)
            )
            ->willReturn(true);

        $ok = $app->updateLivro(9, $input);

        $this->assertTrue($ok);
    }

    public function testDeleteLivroDelegaeRetornaBool(): void
    {
        $service = $this->createMock(LivroService::class);
        $app     = new LivroApplication($service);

        $req = new LivroRequestDto(5);

        $service->expects($this->once())
            ->method('deleteLivro')
            ->with($req)
            ->willReturn(true);

        $this->assertTrue($app->deleteLivro($req));
    }
}

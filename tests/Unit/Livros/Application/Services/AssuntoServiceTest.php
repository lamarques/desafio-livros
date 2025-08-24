<?php

namespace Livros\Application\Services;

use App\Livros\Application\Services\AssuntoService;
use App\Livros\Application\Domain\Repository\AssuntoRepositoryInterface;
use App\Livros\Dtos\AssuntoRequestDto;
use App\Livros\Dtos\AssuntoResponseDto;
use App\Livros\Exceptions\AssuntoException;
use Illuminate\Database\QueryException;
use PDOException;
use PHPUnit\Framework\TestCase;

class AssuntoServiceTest extends TestCase
{
    private function makeQueryException(string $sqlState, int|string $driverCode, string $message = 'DB error'): QueryException
    {
        $pdo = new PDOException($message);
        $pdo->errorInfo = [$sqlState, (string)$driverCode, $message];

        return new QueryException('mysql', 'INSERT ...', [], $pdo);
    }

    public function testCreateRetornaResponseDtoQuandoSucesso(): void
    {
        $repo = $this->createMock(AssuntoRepositoryInterface::class);

        $repo->expects($this->once())
            ->method('saveAssunto')
            ->with('Descricao')
            ->willReturn(true);

        $repo->expects($this->once())
            ->method('getLastInsertedId')
            ->willReturn(123);

        $service = new AssuntoService($repo);

        $request = new AssuntoRequestDto(Descricao: '  Descricao  ');
        $response = $service->create($request);

        $this->assertInstanceOf(AssuntoResponseDto::class, $response);
        $this->assertSame(123, $response->CodAs);
        $this->assertSame('Descricao', $response->Descricao);
    }

    public function testCreateLancaInvalidQuandoDescricaoVaziaOuSomenteEspacos(): void
    {
        $repo = $this->createMock(AssuntoRepositoryInterface::class);
        $service = new AssuntoService($repo);

        $this->expectException(AssuntoException::class);
        $this->expectExceptionMessage('Descrição é obrigatória.');

        $service->create(new AssuntoRequestDto(Descricao: '     '));
    }

    public function testCreateLancaInvalidQuandoDescricaoMaiorQue20(): void
    {
        $repo = $this->createMock(AssuntoRepositoryInterface::class);
        $service = new AssuntoService($repo);

        $this->expectException(AssuntoException::class);
        $this->expectExceptionMessage('Descrição excede o limite de 20 caracteres.');

        $service->create(new AssuntoRequestDto(Descricao: str_repeat('A', 21)));
    }

    public function testCreateMapeiaDuplicidadeParaAlreadyExists(): void
    {
        $repo = $this->createMock(AssuntoRepositoryInterface::class);

        $repo->expects($this->once())
            ->method('saveAssunto')
            ->with('Descricao')
            ->willThrowException($this->makeQueryException('23000', 1062, 'Duplicate entry'));

        $service = new AssuntoService($repo);

        $this->expectException(AssuntoException::class);
        $this->expectExceptionMessage("Assunto 'Descricao' já existe.");

        $service->create(new AssuntoRequestDto(Descricao: 'Descricao'));
    }

    public function testCreateMapeiaErroDeTamanhoParaInvalidMesmoCom20CharsValidos(): void
    {
        $repo = $this->createMock(AssuntoRepositoryInterface::class);

        $descricao20 = 'ABCDEFGHIJKLMNOPQRST';

        $repo->expects($this->once())
            ->method('saveAssunto')
            ->with($descricao20)
            ->willThrowException($this->makeQueryException('22001', 1406, 'Data too long'));

        $service = new AssuntoService($repo);

        $this->expectException(AssuntoException::class);
        $this->expectExceptionMessage('Descrição excede o limite de 20 caracteres.');

        $service->create(new AssuntoRequestDto(Descricao: $descricao20));
    }

    public function testCreateMapeiaQueryExceptionGenericaParaErroInterno(): void
    {
        $repo = $this->createMock(AssuntoRepositoryInterface::class);

        $repo->expects($this->once())
            ->method('saveAssunto')
            ->with('X')
            ->willThrowException($this->makeQueryException('08000', 9999, 'Generic driver error'));

        $service = new AssuntoService($repo);

        try {
            $service->create(new AssuntoRequestDto(Descricao: 'X'));
            $this->fail('Esperava AssuntoException para erro interno.');
        } catch (AssuntoException $e) {
            $this->assertSame('Erro ao salvar o assunto.', $e->getMessage());
            $this->assertSame(500, $e->status());
        }
    }

    public function testCreateLancaErroQuandoNaoConsegueObterIdAposSalvar(): void
    {
        $repo = $this->createMock(AssuntoRepositoryInterface::class);

        $repo->expects($this->once())
            ->method('saveAssunto')
            ->with('Ok')
            ->willReturn(true);

        $repo->expects($this->once())
            ->method('getLastInsertedId')
            ->willReturn(null);

        $service = new AssuntoService($repo);

        $this->expectException(AssuntoException::class);
        $this->expectExceptionMessage('Não foi possível obter o identificador do assunto recém-criado.');

        $service->create(new AssuntoRequestDto(Descricao: 'Ok'));
    }

    public function testCreateLancaErroQuandoRepositorioRetornaFalseSemException(): void
    {
        $repo = $this->createMock(AssuntoRepositoryInterface::class);

        $repo->expects($this->once())
            ->method('saveAssunto')
            ->with('Falha')
            ->willReturn(false);

        $repo->expects($this->never())->method('getLastInsertedId');

        $service = new AssuntoService($repo);

        $this->expectException(AssuntoException::class);
        $this->expectExceptionMessage('Erro ao salvar o assunto.');

        $service->create(new AssuntoRequestDto(Descricao: 'Falha'));
    }
}

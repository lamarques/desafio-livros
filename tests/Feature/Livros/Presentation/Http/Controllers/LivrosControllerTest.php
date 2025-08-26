<?php

namespace Livros\Presentation\Http\Controllers;

use App\Livros\Application\LivroApplication;
use App\Livros\Application\Services\LivroService;
use App\Livros\Dtos\LivroRequestDto;
use App\Livros\Dtos\LivroResponseDto;
use App\Livros\Dtos\LivrosSaveDto;
use App\Models\Assunto;
use App\Models\Autor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivrosControllerTest extends TestCase
{

    use RefreshDatabase;

    private array $autores = [];
    private array $assuntos = [];
    private function payloadValido(): array
    {
        Autor::factory()->count(2)->create();
        Assunto::factory()->count(2)->create();
        $this->autores = Autor::all()->pluck('CodAu')->toArray();
        $this->assuntos = Assunto::all()->pluck('CodAs')->toArray();

        return [
            'Titulo'        => 'DDD',
            'Editora'       => 'Addison-Wesley',
            'Edicao'        => 1,
            'AnoPublicacao' => '2003',
            'AutorID'       => $this->autores,
            'AssuntoID'     => $this->assuntos,
        ];
    }

    private function bindAppWithServiceMock(\Closure $configure): void
    {
        $service = $this->createMock(LivroService::class);
        $configure($service);
        $this->app->instance(LivroApplication::class, new LivroApplication($service));
    }

    public function testCreateRetorna201ComPayload(): void
    {
        $input = $this->payloadValido();

        $dto = new LivroResponseDto(
            Codl: 55,
            Titulo: $input['Titulo'],
            Editora: $input['Editora'],
            Edicao: $input['Edicao'],
            AnoPublicacao: $input['AnoPublicacao'],
            Autores: $input['AutorID'],
            Assuntos: $input['AssuntoID']
        );

        $this->bindAppWithServiceMock(function (LivroService $service) use ($input, $dto) {
            $service->expects($this->once())
                ->method('createLivro')
                ->with($this->callback(function ($arg) use ($input) {
                    return $arg instanceof LivrosSaveDto
                        && $arg->titulo === $input['Titulo']
                        && $arg->editora === $input['Editora']
                        && $arg->edicao === $input['Edicao']
                        && $arg->anoPublicacao === $input['AnoPublicacao']
                        && $arg->autores === $input['AutorID']
                        && $arg->assuntos === $input['AssuntoID'];
                }))
                ->willReturn($dto);
        });

        $resp = $this->postJson('/api/livro', $input);

        $resp->assertStatus(201)
            ->assertJsonFragment(['message' => 'Livro criado com sucesso.'])
            ->assertJsonPath('data.Codl', 55)
            ->assertJsonPath('data.Titulo', 'DDD')
            ->assertJsonPath('data.Editora', 'Addison-Wesley')
            ->assertJsonPath('data.Edicao', 1)
            ->assertJsonPath('data.AnoPublicacao', '2003')
            ->assertJsonPath('data.Autores', $this->autores)
            ->assertJsonPath('data.Assuntos', $this->assuntos);
    }

    public function testCreateRetorna422QuandoPayloadInvalido(): void
    {
        $resp = $this->postJson('/api/livro', []);
        $resp->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['Titulo','Editora','Edicao','AnoPublicacao','AutorID','AssuntoID'],
            ]);
    }

    public function testListRetorna200ComArray(): void
    {
        $lista = [
            new LivroResponseDto(1, 'A', 'X', 1, '2001', [10], [5]),
            new LivroResponseDto(2, 'B', 'Y', 2, '2002', [11], [6]),
        ];

        $this->bindAppWithServiceMock(function (LivroService $service) use ($lista) {
            $service->expects($this->once())
                ->method('listLivros')
                ->with()
                ->willReturn($lista);
        });

        $resp = $this->getJson('/api/livro');

        $resp->assertOk()
            ->assertJsonFragment(['Codl' => 1, 'Titulo' => 'A'])
            ->assertJsonFragment(['Codl' => 2, 'Titulo' => 'B']);
    }

    public function testShowRetorna200ComPayload(): void
    {
        $dto = new LivroResponseDto(7, 'Clean Code', 'PH', 1, '2008', [10], [5]);

        $this->bindAppWithServiceMock(function (LivroService $service) use ($dto) {
            $service->expects($this->once())
                ->method('getLivro')
                ->with($this->callback(fn($r) => $r instanceof LivroRequestDto && $r->Codl === 7))
                ->willReturn($dto);
        });

        $resp = $this->getJson('/api/livro/7');

        $resp->assertOk()
            ->assertJsonPath('data.Codl', 7)
            ->assertJsonPath('data.Titulo', 'Clean Code');
    }

    public function testShowRetorna200ComDataNullQuandoNaoEncontrado(): void
    {
        $this->bindAppWithServiceMock(function (LivroService $service) {
            $service->expects($this->once())
                ->method('getLivro')
                ->with($this->isInstanceOf(LivroRequestDto::class))
                ->willReturn(null);
        });

        $resp = $this->getJson('/api/livro/999');

        $resp->assertOk()->assertJson(['data' => null]);
    }

    public function testUpdateRetorna200QuandoApplicationRetornaTrue(): void
    {
        $input = $this->payloadValido();

        $this->bindAppWithServiceMock(function (LivroService $service) use ($input) {
            $service->expects($this->once())
                ->method('updateLivro')
                ->with(
                    $this->callback(fn($r) => $r instanceof LivroRequestDto && $r->Codl === 9),
                    $this->callback(function ($arg) use ($input) {
                        return $arg instanceof LivrosSaveDto
                            && $arg->titulo === $input['Titulo']
                            && $arg->editora === $input['Editora']
                            && $arg->edicao === $input['Edicao']
                            && $arg->anoPublicacao === $input['AnoPublicacao']
                            && $arg->autores === $input['AutorID']
                            && $arg->assuntos === $input['AssuntoID'];
                    })
                )
                ->willReturn(true);
        });

        $resp = $this->putJson('/api/livro/9', $input);

        $resp->assertOk()->assertJson(['message' => 'Livro atualizado com sucesso.']);
    }

    public function testUpdateRetorna404QuandoApplicationRetornaFalse(): void
    {
        $input = $this->payloadValido();

        $this->bindAppWithServiceMock(function (LivroService $service) {
            $service->expects($this->once())
                ->method('updateLivro')
                ->with($this->isInstanceOf(LivroRequestDto::class), $this->isInstanceOf(LivrosSaveDto::class))
                ->willReturn(false);
        });

        $resp = $this->putJson('/api/livro/404', $input);

        $resp->assertStatus(404)->assertJson(['message' => 'Livro não encontrado.']);
    }

    public function testDeleteRetorna200QuandoApplicationRetornaTrue(): void
    {
        $this->bindAppWithServiceMock(function (LivroService $service) {
            $service->expects($this->once())
                ->method('deleteLivro')
                ->with($this->callback(fn($r) => $r instanceof LivroRequestDto && $r->Codl === 3))
                ->willReturn(true);
        });

        $resp = $this->deleteJson('/api/livro/3');

        $resp->assertOk()->assertJson(['message' => 'Livro deletado com sucesso.']);
    }

    public function testDeleteRetorna404QuandoApplicationRetornaFalse(): void
    {
        $this->bindAppWithServiceMock(function (LivroService $service) {
            $service->expects($this->once())
                ->method('deleteLivro')
                ->with($this->isInstanceOf(LivroRequestDto::class))
                ->willReturn(false);
        });

        $resp = $this->deleteJson('/api/livro/9999');

        $resp->assertStatus(404)->assertJson(['message' => 'Livro não encontrado.']);
    }
}

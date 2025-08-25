<?php

namespace Livros\Presentation\Http\Controllers;

use App\Livros\Application\AssuntoApplication;
use App\Livros\Dtos\AssuntoRequestDto;
use App\Livros\Dtos\AssuntoResponseDto;
use App\Livros\Exceptions\AssuntoException;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AssuntoControlerTest extends TestCase
{
    public function testCreateRetorna201ComPayload(): void
    {
        $this->assertTrue(
            Route::has('api.assunto.create'),
            'Rota "api.assuntos.create" não está registrada. Verifique routes/api.php e bootstrap/app.php.'
        );

        $mock = $this->createMock(AssuntoApplication::class);
        $this->instance(AssuntoApplication::class, $mock);

        $input = ['Descricao' => 'Redes'];
        $retorno = new AssuntoResponseDto(
            CodAs: 10,
            Descricao: 'Redes'
        );

        $mock->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($dto) use ($input) {
                return $dto instanceof AssuntoRequestDto
                    && $dto->Descricao === $input['Descricao'];
            }))
            ->willReturn($retorno);

        $resp = $this->postJson('/api/assunto', $input);

        $resp->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Assunto criado com sucesso.',
            ])
            ->assertJsonPath('data.CodAs', 10)
            ->assertJsonPath('data.Descricao', 'Redes');
    }

    public function testCreateValidaDescricaoObrigatoria(): void
    {
        $mock = $this->createMock(AssuntoApplication::class);
        $mock->expects($this->never())->method('create');
        $this->instance(AssuntoApplication::class, $mock);

        $resp = $this->postJson('/api/assunto', ['Descricao' => '']);

        $resp->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['Descricao'],
            ]);
    }

    public function testCreateValidaDescricaoTamanhoMaximo20(): void
    {
        $mock = $this->createMock(AssuntoApplication::class);
        $mock->expects($this->never())->method('create');
        $this->instance(AssuntoApplication::class, $mock);

        $muitoLongo = str_repeat('A', 21);

        $resp = $this->postJson('/api/assunto', ['Descricao' => $muitoLongo]);

        $resp->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['Descricao'],
            ]);
    }

    public function testListRetorna200ComArrayDeAssuntos(): void
    {
        $mock = $this->createMock(AssuntoApplication::class);
        $this->instance(AssuntoApplication::class, $mock);

        $mock->expects($this->once())
            ->method('list')
            ->willReturn([
                new AssuntoResponseDto(CodAs: 1, Descricao: 'Redes'),
                new AssuntoResponseDto(CodAs: 2, Descricao: 'Banco de Dados'),
            ]);

        $resp = $this->getJson('/api/assunto');

        $resp->assertOk()
            ->assertJsonPath('data.0.CodAs', 1)
            ->assertJsonPath('data.0.Descricao', 'Redes')
            ->assertJsonPath('data.1.CodAs', 2)
            ->assertJsonPath('data.1.Descricao', 'Banco de Dados')
            ->assertJsonCount(2, 'data');
    }

    public function testListRetorna200ComArrayVazioQuandoNaoHaAssuntos(): void
    {
        $mock = $this->createMock(AssuntoApplication::class);
        $this->instance(AssuntoApplication::class, $mock);

        $mock->expects($this->once())
            ->method('list')
            ->willReturn([]);

        $resp = $this->getJson('/api/assunto');

        $resp->assertOk()
            ->assertExactJson(['data' => []]);
    }

    public function testShowRetorna200ComPayload(): void
    {
        $dto = new AssuntoResponseDto(CodAs: 1, Descricao: 'Redes');

        $this->mock(AssuntoApplication::class, function ($mock) use ($dto) {
            $mock->shouldReceive('show')
            ->once()
                ->with(1)
                ->andReturn($dto);
        });

        $resp = $this->getJson('/api/assunto/1');

        $resp->assertOk()
            ->assertJsonPath('data.CodAs', 1)
            ->assertJsonPath('data.Descricao', 'Redes');
    }

    public function testShowRetorna404QuandoNaoEncontrado(): void
    {
        $this->mock(AssuntoApplication::class, function ($mock) {
            $mock->shouldReceive('show')
            ->once()
                ->with(999)
                ->andReturn(null);
        });

        $resp = $this->getJson('/api/assunto/999');

        $resp->assertStatus(404)
            ->assertJsonFragment(['message' => 'Assunto não encontrado.']);
    }
}

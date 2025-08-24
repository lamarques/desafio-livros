<?php

namespace Livros\Presentation\Http\Controllers;

use App\Livros\Application\AssuntoApplication;
use App\Livros\Dtos\AssuntoRequestDto;
use App\Livros\Dtos\AssuntoResponseDto;
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

        $muitoLongo = str_repeat('A', 21); // > 20

        $resp = $this->postJson('/api/assunto', ['Descricao' => $muitoLongo]);

        $resp->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['Descricao'],
            ]);
    }
}

<?php

namespace Livros\Presentation\Http\Controllers;

use App\Livros\Application\AutorApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testListRetorna200ComPayload(): void
    {
        $payload = [
            ['CodAu' => 1, 'Nome' => 'Clarice Lispector'],
            ['CodAu' => 2, 'Nome' => 'Machado de Assis'],
        ];

        $this->mock(AutorApplication::class, function ($mock) use ($payload) {
            $mock->shouldReceive('list')->once()->andReturn($payload);
        });

        $resp = $this->getJson('/api/autor'); // depende do routes/api.php

        $resp->assertOk()
            ->assertJson(['data' => $payload])
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.CodAu', 1)
            ->assertJsonPath('data.0.Nome', 'Clarice Lispector');
    }

    public function testListRetornaArrayVazioQuandoNaoHaAutores(): void
    {
        $this->mock(AutorApplication::class, function ($mock) {
            $mock->shouldReceive('list')->once()->andReturn([]);
        });

        $resp = $this->getJson('/api/autor');

        $resp->assertOk()
            ->assertExactJson(['data' => []]);
    }
}

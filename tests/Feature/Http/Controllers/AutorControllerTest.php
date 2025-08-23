<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;
use App\Models\Autor;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AutorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    /** Helper para resolver o nome da tabela do modelo */
    protected function autorTable(): string
    {
        return (new Autor)->getTable();
    }

    public function testIndexExibeListaDeAutores(): void
    {
        Autor::factory()->count(2)->create();

        $resp = $this->get(route('autor.index'));

        $resp->assertStatus(200)
            ->assertViewIs('autor.index')
            ->assertViewHas('autores', function ($autores) {
                return $autores->count() === 2;
            });
    }

    public function testCreateExibeFormulario(): void
    {
        $this->get(route('autor.create'))
            ->assertStatus(200)
            ->assertViewIs('autor.create');
    }

    public function testStoreValidaNomeObrigatorio(): void
    {
        $token = 'test-token';


        $this->withSession(['_token' => $token])
            ->from(route('autor.create'))
            ->post(route('autor.store'), ['_token' => $token])
            ->assertRedirect(route('autor.create'))
            ->assertSessionHasErrors(['Nome']);
    }

    public function testStoreCriaAutorERedirecionaComFlash(): void
    {
        $token = 'test-token';

        $this->withSession(['_token' => $token])
            ->post(route('autor.store'), [
                '_token' => $token,
                'Nome'   => 'Clarice Lispector',
            ])
            ->assertRedirect(route('autor.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas($this->autorTable(), ['Nome' => 'Clarice Lispector']);
    }

    public function testShowRedirecionaParaEditUsandoCodau(): void
    {
        $autor = Autor::factory()->create();

        $this->get(route('autor.show', $autor))
            ->assertRedirect(route('autor.edit', $autor->CodAu));
    }

    public function EditExibeFormularioDeEdicao(): void
    {
        $autor = Autor::factory()->create();

        $this->get(route('autor.edit', $autor))
            ->assertStatus(200)
            ->assertViewIs('autor.edit')
            ->assertViewHas('autor', fn ($a) => $a->CodAu === $autor->CodAu);
    }

    public function testUpdateValidaNomeObrigatorio(): void
    {
        $autor = Autor::factory()->create();
        $token = 'test-token';

        $this->withSession(['_token' => $token])
            ->from(route('autor.edit', $autor))
            ->put(route('autor.update', $autor), ['_token'=>$token])
            ->assertRedirect(route('autor.edit', $autor))
            ->assertSessionHasErrors(['Nome']);
    }

    public function UpdateAtualizaAutorERedirecionaComFlash(): void
    {
        $autor = Autor::factory()->create(['Nome' => 'Nome Antigo']);
        $token = 'test-token';

        $this->withSession(['_token' => $token])
            ->put(route('autor.update', $autor), [
                '_token' => $token,
                'Nome'   => 'Nome Novo',
            ])
            ->assertRedirect(route('autor.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas((new Autor)->getTable(), [
            $autor->getKeyName() => $autor->getKey(),
            'Nome' => 'Nome Novo',
        ]);
    }

    public function testDestroyRemoveAutorERedirecionaComFlash(): void
    {
        $autor = Autor::factory()->create();
        $token = 'test-token';

        $this->withSession(['_token' => $token])
            ->delete(route('autor.destroy', $autor), ['_token' => $token])
            ->assertRedirect(route('autor.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing((new Autor)->getTable(), [
            $autor->getKeyName() => $autor->getKey(),
        ]);
    }

    public function testEditExibeViewDeEdicaoComAutor(): void
    {
        $autor = Autor::factory()->create();

        $this->get(route('autor.edit', $autor))
            ->assertStatus(200)
            ->assertViewIs('autor.edit')
            ->assertViewHas('autor', function ($viewAutor) use ($autor) {
                return $viewAutor->CodAu === $autor->CodAu;
            });
    }

    public function testUpdateValidaNomeObrigatorioRedirecionaParaEdit(): void
    {
        $autor = Autor::factory()->create(['Nome' => 'Antigo']);
        $token = 'test-token';

        $this->withSession(['_token' => $token])
            ->from(route('autor.edit', $autor))
            ->put(route('autor.update', $autor), ['_token' => $token])
            ->assertRedirect(route('autor.edit', $autor))
            ->assertSessionHasErrors(['Nome']);

        $this->assertDatabaseHas((new Autor)->getTable(), [
            $autor->getKeyName() => $autor->getKey(),
            'Nome' => 'Antigo',
        ]);
    }

    public function testUpdateAtualizaAutorERedirecionaComFlash(): void
    {
        $autor = Autor::factory()->create(['Nome' => 'Nome Antigo']);
        $token = 'test-token';

        $this->withSession(['_token' => $token])
            ->put(route('autor.update', $autor), [
                '_token' => $token,
                'Nome'   => 'Nome Novo',
            ])
            ->assertRedirect(route('autor.index'))
            ->assertSessionHas('success', 'Autor Atualizado com sucesso.')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas((new Autor)->getTable(), [
            $autor->getKeyName() => $autor->getKey(),
            'Nome' => 'Nome Novo',
        ]);
    }
}

<?php

namespace Livros\Infrastructure\Repository;

use App\Livros\Application\Domain\Entity\Autor as AutorEntity;
use App\Livros\Infrastructure\Repository\AutorRepository;
use App\Models\Autor as AutorModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutorRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private AutorRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new AutorRepository(new AutorModel());
    }

    /** Helper: nome da tabela do Model */
    private function autorTable(): string
    {
        return (new AutorModel())->getTable();
    }

    /** Helper: cria e persiste um AutorModel */
    private function criarAutor(string $nome = 'Machado de Assis'): AutorModel
    {
        $autor = new AutorModel();
        $autor->Nome = $nome;
        $autor->save();

        return $autor;
    }

    public function testGetAutorRetornaEntidadeQuandoEncontrado(): void
    {
        $salvo = $this->criarAutor('Clarice Lispector');

        $entity = $this->repo->getAutor($salvo->CodAu);

        $this->assertInstanceOf(AutorEntity::class, $entity);
        $this->assertSame($salvo->CodAu, $entity->getCodAu());
        $this->assertSame('Clarice Lispector', $entity->getNome());
    }

    public function testGetAutorRetornaNullQuandoNaoEncontrado(): void
    {
        $this->assertNull($this->repo->getAutor(999999));
    }

    public function testSaveAutorPersisteERetornaTrue(): void
    {
        $ok = $this->repo->saveAutor('Guimarães Rosa');
        $this->assertTrue($ok);

        $this->assertDatabaseHas($this->autorTable(), [
            'Nome' => 'Guimarães Rosa',
        ]);
    }

    public function testUpdateAutorRetornaFalseSeNaoEncontrado(): void
    {
        $this->assertFalse($this->repo->updateAutor(123456, 'Nome Novo'));
    }

    public function testUpdateAutorAtualizaERetornaTrue(): void
    {
        $salvo = $this->criarAutor('Nome Antigo');

        $ok = $this->repo->updateAutor($salvo->CodAu, 'Nome Novo');
        $this->assertTrue($ok);

        $this->assertDatabaseHas($this->autorTable(), [
            'CodAu' => $salvo->CodAu,
            'Nome'  => 'Nome Novo',
        ]);
    }

    public function testDeleteAutorRetornaFalseSeNaoEncontrado(): void
    {
        $this->assertFalse($this->repo->deleteAutor(777777));
    }

    public function testDeleteAutorRemoveERetornaTrue(): void
    {
        $salvo = $this->criarAutor();

        $ok = $this->repo->deleteAutor($salvo->CodAu);
        $this->assertTrue($ok);

        $this->assertDatabaseMissing($this->autorTable(), [
            'CodAu' => $salvo->CodAu,
        ]);
    }

    /** NOVO: getAllAutores retorna array vazio quando não há registros */
    public function testGetAllAutoresRetornaArrayVazioQuandoNaoHaRegistros(): void
    {
        $lista = $this->repo->getAllAutores();

        $this->assertIsArray($lista);
        $this->assertCount(0, $lista);
    }

    /** NOVO: getAllAutores retorna lista de entidades corretamente mapeadas */
    public function testGetAllAutoresRetornaListaDeEntidades(): void
    {
        $a = $this->criarAutor('Autor A');
        $b = $this->criarAutor('Autor B');

        $lista = $this->repo->getAllAutores();

        $this->assertIsArray($lista);
        $this->assertCount(2, $lista);
        $this->assertContainsOnlyInstancesOf(AutorEntity::class, $lista);

        // Facilita conferir pelo ID
        $map = [];
        foreach ($lista as $ent) {
            $map[$ent->getCodAu()] = $ent;
        }

        $this->assertArrayHasKey($a->CodAu, $map);
        $this->assertSame('Autor A', $map[$a->CodAu]->getNome());

        $this->assertArrayHasKey($b->CodAu, $map);
        $this->assertSame('Autor B', $map[$b->CodAu]->getNome());
    }
}

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
}

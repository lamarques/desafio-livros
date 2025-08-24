<?php

namespace Livros\Infrastructure\Repository;

use App\Livros\Application\Domain\Entity\Assunto as AssuntoEntity;
use App\Livros\Infrastructure\Repository\AssuntoRepository;
use App\Models\Assunto as AssuntoModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssuntoRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private AssuntoRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new AssuntoRepository(new AssuntoModel());
    }

    /** Helper: nome da tabela */
    private function assuntoTable(): string
    {
        return (new AssuntoModel())->getTable();
    }

    /** Helper: cria e persiste um AssuntoModel */
    private function criarAssunto(string $descricao = 'Tecnologia'): AssuntoModel
    {
        $assunto = new AssuntoModel();
        $assunto->Descricao = $descricao;
        $assunto->save();

        return $assunto;
    }

    public function testGetAssuntoRetornaEntidadeQuandoEncontrado(): void
    {
        $salvo = $this->criarAssunto('Ciência');

        $entity = $this->repo->getAssunto($salvo->CodAs);

        $this->assertInstanceOf(AssuntoEntity::class, $entity);
        $this->assertSame($salvo->CodAs, $entity->getCodAs());
        $this->assertSame('Ciência', $entity->getDescricao());
    }

    public function testGetAssuntoRetornaNullQuandoNaoEncontrado(): void
    {
        $this->assertNull($this->repo->getAssunto(999999));
    }

    public function testSaveAssuntoPersisteERetornaTrue(): void
    {
        $ok = $this->repo->saveAssunto('Matemática');
        $this->assertTrue($ok);

        $this->assertDatabaseHas($this->assuntoTable(), [
            'Descricao' => 'Matemática',
        ]);
    }

    public function testUpdateAssuntoRetornaFalseSeNaoEncontrado(): void
    {
        $this->assertFalse($this->repo->updateAssunto(123456, 'Nova Desc'));
    }

    public function testUpdateAssuntoAtualizaERetornaTrue(): void
    {
        $salvo = $this->criarAssunto('Antiga Desc');

        $ok = $this->repo->updateAssunto($salvo->CodAs, 'Nova Desc');
        $this->assertTrue($ok);

        $this->assertDatabaseHas($this->assuntoTable(), [
            'CodAs' => $salvo->CodAs,
            'Descricao' => 'Nova Desc',
        ]);
    }

    public function testDeleteAssuntoRetornaFalseSeNaoEncontrado(): void
    {
        $this->assertFalse($this->repo->deleteAssunto(987654));
    }

    public function testDeleteAssuntoRemoveERetornaTrue(): void
    {
        $salvo = $this->criarAssunto('Para Remover');

        $ok = $this->repo->deleteAssunto($salvo->CodAs);
        $this->assertTrue($ok);

        $this->assertDatabaseMissing($this->assuntoTable(), [
            'CodAs' => $salvo->CodAs,
        ]);
    }
}

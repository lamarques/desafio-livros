<?php

namespace Livros\Infrastructure\Repository;

use App\Livros\Application\Domain\Entity\Assunto as AssuntoEntity;
use App\Livros\Infrastructure\Repository\AssuntoRepository;
use App\Models\Assunto as AssuntoModel;
use Illuminate\Database\QueryException;
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

    private function assuntoTable(): string
    {
        return (new AssuntoModel())->getTable();
    }

    private function criarAssunto(string $descricao = 'Algoritmos'): AssuntoModel
    {
        // descrição ≤ 20 chars
        $m = new AssuntoModel();
        $m->Descricao = $descricao;
        $m->save();
        return $m;
    }

    public function testGetAssuntoRetornaEntidadeQuandoEncontrado(): void
    {
        $salvo = $this->criarAssunto('Ciencia da Comp.'); // 17 chars
        $entidade = $this->repo->getAssunto($salvo->CodAs);

        $this->assertInstanceOf(AssuntoEntity::class, $entidade);
        $this->assertSame($salvo->CodAs, $entidade->getCodAs());
        $this->assertSame('Ciencia da Comp.', $entidade->getDescricao());
    }

    public function testGetAssuntoRetornaNullQuandoNaoEncontrado(): void
    {
        $this->assertNull($this->repo->getAssunto(999999));
    }

    public function testSaveAssuntoPersisteRetornaTrueEAtualizaLastInsertedId(): void
    {
        $ok = $this->repo->saveAssunto('Estruturas de Dados'); // 20 chars exatos
        $this->assertTrue($ok);

        $this->assertDatabaseHas($this->assuntoTable(), [
            'Descricao' => 'Estruturas de Dados',
        ]);

        $registro = AssuntoModel::where('Descricao', 'Estruturas de Dados')->firstOrFail();
        $this->assertSame($registro->CodAs, $this->repo->getLastInsertedId());
    }

    public function testGetLastInsertedIdAntesDeSalvarRetornaNull(): void
    {
        $this->assertNull($this->repo->getLastInsertedId());
    }

    public function testSaveAssuntoExcedeLimiteDeCaracteresRespeitaDriver(): void
    {
        $repo = new AssuntoRepository(new AssuntoModel());
        $tooLong = str_repeat('A', 21);

        $driver = $this->app['db']->connection()->getDriverName();

        if ($driver === 'sqlite') {
            $ok = $repo->saveAssunto($tooLong);
            $this->assertTrue($ok);
            $this->assertDatabaseHas((new AssuntoModel())->getTable(), [
                'Descricao' => $tooLong,
            ]);
            return;
        }

        $this->expectException(QueryException::class);
        $repo->saveAssunto($tooLong);
    }

    public function testUpdateAssuntoAtualizaERetornaTrue(): void
    {
        $salvo = $this->criarAssunto('Antigo');
        $ok = $this->repo->updateAssunto($salvo->CodAs, 'Novo');
        $this->assertTrue($ok);

        $this->assertDatabaseHas($this->assuntoTable(), [
            'CodAs'     => $salvo->CodAs,
            'Descricao' => 'Novo',
        ]);
        $this->assertDatabaseMissing($this->assuntoTable(), [
            'CodAs'     => $salvo->CodAs,
            'Descricao' => 'Antigo',
        ]);
    }

    public function testUpdateAssuntoRetornaFalseQuandoNaoEncontrado(): void
    {
        $this->assertFalse($this->repo->updateAssunto(123456, 'Qualquer'));
    }

    public function testDeleteAssuntoRemoveERetornaTrue(): void
    {
        $salvo = $this->criarAssunto('Para Deletar');
        $ok = $this->repo->deleteAssunto($salvo->CodAs);
        $this->assertTrue($ok);

        $this->assertDatabaseMissing($this->assuntoTable(), [
            'CodAs' => $salvo->CodAs,
        ]);
    }

    public function testDeleteAssuntoRetornaFalseQuandoNaoEncontrado(): void
    {
        $this->assertFalse($this->repo->deleteAssunto(999999));
    }

    public function testGetAllAssuntosRetornaArrayVazioQuandoNaoHaRegistros(): void
    {
        $resultado = $this->repo->getAllAssuntos();

        $this->assertIsArray($resultado);
        $this->assertCount(0, $resultado);
    }

    public function testGetAllAssuntosRetornaArrayDeEntidadesComDadosMapeados(): void
    {
        $a = $this->criarAssunto('Redes');           // <= 5 chars
        $b = $this->criarAssunto('Banco de Dados');  // <= 14 chars

        $resultado = $this->repo->getAllAssuntos();

        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
        $this->assertContainsOnlyInstancesOf(AssuntoEntity::class, $resultado);

        $map = [];
        foreach ($resultado as $ent) {
            $map[$ent->getCodAs()] = $ent;
        }

        $this->assertArrayHasKey($a->CodAs, $map);
        $this->assertSame($a->CodAs, $map[$a->CodAs]->getCodAs());
        $this->assertSame('Redes', $map[$a->CodAs]->getDescricao());

        $this->assertArrayHasKey($b->CodAs, $map);
        $this->assertSame($b->CodAs, $map[$b->CodAs]->getCodAs());
        $this->assertSame('Banco de Dados', $map[$b->CodAs]->getDescricao());
    }
}

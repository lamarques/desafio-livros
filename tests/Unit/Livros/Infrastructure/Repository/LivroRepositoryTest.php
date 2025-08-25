<?php

namespace Tests\Unit\Livros\Infrastructure\Repository;

use App\Livros\Application\Domain\Entity\Livro as LivroEntity;
use App\Livros\Dtos\LivroRequestDto;
use App\Livros\Dtos\LivroResponseDto;
use App\Livros\Dtos\LivrosSaveDto;
use App\Livros\Infrastructure\Repository\LivroRepository;
use App\Models\Livro as LivroModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivroRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private LivroRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new LivroRepository(new LivroModel());
    }

    /** Helper para obter o nome da tabela do Model */
    private function livroTable(): string
    {
        return (new LivroModel())->getTable();
    }

    /** Helper para criar um LivroModel persistido */
    private function criarLivro(array $attrs = []): LivroModel
    {
        $livro = new LivroModel();
        $livro->Titulo        = $attrs['Titulo']        ?? 'O Guia do Programador';
        $livro->Editora       = $attrs['Editora']       ?? 'TechBooks';
        $livro->Edicao        = $attrs['Edicao']        ?? 1;
        $livro->AnoPublicacao = $attrs['AnoPublicacao'] ?? '2020';
        $livro->save();

        return $livro;
    }

    public function testGetLivroRetornaEntidadeQuandoEncontrado(): void
    {
        $salvo = $this->criarLivro([
            'Titulo' => 'Clean Architecture',
            'Editora' => 'Prentice Hall',
            'Edicao' => 2,
            'AnoPublicacao' => '2017',
        ]);

        $dtoReq = new LivroRequestDto($salvo->Codl);
        $entity = $this->repo->getLivro($dtoReq);

        $this->assertInstanceOf(LivroEntity::class, $entity);
        $this->assertSame($salvo->Codl, $entity->getCodl());
        $this->assertSame('Clean Architecture', $entity->getTitulo());
        $this->assertSame('Prentice Hall', $entity->getEditora());
        $this->assertSame(2, $entity->getEdicao());
        $this->assertSame('2017', $entity->getAnoPublicacao());
    }

    public function testGetLivroRetornaNullQuandoNaoEncontrado(): void
    {
        $dtoReq = new LivroRequestDto(999999);
        $this->assertNull($this->repo->getLivro($dtoReq));
    }

    public function testSaveLivroPersisteERetornaTrue(): void
    {
        $saveDto = new LivrosSaveDto(
            titulo: 'Domain-Driven Design',
            editora: 'Addison-Wesley',
            edicao: 1,
            anoPublicacao: '2003',
            autores: [],
            assuntos: []
        );

        $ok = $this->repo->saveLivro($saveDto);
        $this->assertTrue($ok);

        $this->assertDatabaseHas($this->livroTable(), [
            'Titulo' => 'Domain-Driven Design',
            'Editora' => 'Addison-Wesley',
            'Edicao' => 1,
            'AnoPublicacao' => '2003',
        ]);
    }

    public function testUpdateLivroRetornaFalseSeNaoEncontrado(): void
    {
        $req = new LivroRequestDto(123456);
        $save = new LivrosSaveDto('Novo Título', 'Nova Editora', 2, '2024', [], []);

        $this->assertFalse($this->repo->updateLivro($req, $save));
    }

    public function testUpdateLivroAtualizaERetornaTrue(): void
    {
        $salvo = $this->criarLivro([
            'Titulo' => 'Antigo',
            'Editora' => 'Editora A',
            'Edicao' => 1,
            'AnoPublicacao' => '2000',
        ]);

        $req = new LivroRequestDto($salvo->Codl);
        $save = new LivrosSaveDto('Novo', 'Editora B', 3, '2024', [], []);

        $ok = $this->repo->updateLivro($req, $save);
        $this->assertTrue($ok);

        $this->assertDatabaseHas($this->livroTable(), [
            'Codl' => $salvo->Codl,
            'Titulo' => 'Novo',
            'Editora' => 'Editora B',
            'Edicao' => 3,
            'AnoPublicacao' => '2024',
        ]);
    }

    public function testDeleteLivroRetornaFalseSeNaoEncontrado(): void
    {
        $req = new LivroRequestDto(999);
        $this->assertFalse($this->repo->deleteLivro($req));
    }

    public function testDeleteLivroRemoveERetornaTrue(): void
    {
        $salvo = $this->criarLivro();
        $req = new LivroRequestDto($salvo->Codl);

        $ok = $this->repo->deleteLivro($req);
        $this->assertTrue($ok);

        $this->assertDatabaseMissing($this->livroTable(), ['Codl' => $salvo->Codl]);
    }

    public function testListLivrosRetornaListaDeResponseDtos(): void
    {
        $a = $this->criarLivro([
            'Titulo' => 'Livro A',
            'Editora' => 'Editora A',
            'Edicao' => 1,
            'AnoPublicacao' => '2001',
        ]);
        $b = $this->criarLivro([
            'Titulo' => 'Livro B',
            'Editora' => 'Editora B',
            'Edicao' => 2,
            'AnoPublicacao' => '2002',
        ]);

        $lista = $this->repo->listLivros();

        $this->assertIsArray($lista);
        $this->assertCount(2, $lista);
        $this->assertContainsOnlyInstancesOf(LivroResponseDto::class, $lista);

        // Ordenação não é garantida — validamos por presença
        $map = [];
        foreach ($lista as $dto) {
            $map[$dto->Codl] = $dto;
        }

        $this->assertArrayHasKey($a->Codl, $map);
        $this->assertSame('Livro A', $map[$a->Codl]->Titulo);
        $this->assertSame('Editora A', $map[$a->Codl]->Editora);
        $this->assertSame(1, $map[$a->Codl]->Edicao);
        $this->assertSame('2001', $map[$a->Codl]->AnoPublicacao);

        $this->assertArrayHasKey($b->Codl, $map);
        $this->assertSame('Livro B', $map[$b->Codl]->Titulo);
        $this->assertSame('Editora B', $map[$b->Codl]->Editora);
        $this->assertSame(2, $map[$b->Codl]->Edicao);
        $this->assertSame('2002', $map[$b->Codl]->AnoPublicacao);
    }
}

<?php

namespace Livros\Application\Domain\Entity;

use App\Livros\Application\Domain\Entity\Livro;
use Faker\Factory as Faker;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class LivroTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create('pt_BR');
    }

    public function testCriaLivroComValoresValidos(): void
    {
        $codl   = $this->faker->numberBetween(1, 9999);
        $titulo = $this->faker->sentence(3);
        $editora = $this->faker->company();
        $edicao = $this->faker->numberBetween(1, 50);
        $ano    = (string) $this->faker->numberBetween(1900, (int) date('Y'));

        $livro = new Livro($codl, $titulo, $editora, $edicao, $ano, [], []);

        $this->assertSame($codl,   $livro->getCodl());
        $this->assertSame($titulo, $livro->getTitulo());
        $this->assertSame($editora,$livro->getEditora());
        $this->assertSame($edicao, $livro->getEdicao());
        $this->assertSame($ano,    $livro->getAnoPublicacao());
        $this->assertIsArray($livro->getAutores());
        $this->assertIsArray($livro->getAssuntos());
    }

    public function testLancaTypeErrorQuandoCodlNaoEhInteiro(): void
    {
        $this->expectException(\TypeError::class);
        new Livro([], 'Titulo', 'Editora', 1, '2020');
    }

    public function testLancaTypeErrorQuandoTituloNaoEhString(): void
    {
        $this->expectException(\TypeError::class);
        new Livro(1, [], 'Editora', 1, '2020');
    }

    public function testLancaTypeErrorQuandoEditoraNaoEhString(): void
    {
        $this->expectException(\TypeError::class);
        new Livro(1, 'Titulo', [], 1, '2020');
    }

    public function testLancaTypeErrorQuandoEdicaoNaoEhInteiro(): void
    {
        $this->expectException(\TypeError::class);
        new Livro(1, 'Titulo', 'Editora', [], '2020');
    }

    public function testLancaTypeErrorQuandoAnoPublicacaoNaoEhString(): void
    {
        $this->expectException(\TypeError::class);
        new Livro(1, 'Titulo', 'Editora', 1, []);
    }
}

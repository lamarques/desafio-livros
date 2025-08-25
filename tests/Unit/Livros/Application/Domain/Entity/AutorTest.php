<?php

namespace Livros\Application\Domain\Entity;


use App\Livros\Application\Domain\Entity\Autor;
use PHPUnit\Framework\TestCase;

class AutorTest extends TestCase
{
    public function testCriaAutorComValoresValidos(): void
    {
        $autor = new Autor(
            CodAu: 7,
            Nome: 'Clarice Lispector'
        );

        $this->assertInstanceOf(Autor::class, $autor);
        $this->assertSame(7, $autor->getCodAu());
        $this->assertSame('Clarice Lispector', $autor->getNome());
    }

    public function testLancaTypeErrorQuandoCodAuNaoEhInt(): void
    {
        $this->expectException(\TypeError::class);

        new Autor(
            CodAu: ['7'],
            Nome: 'Qualquer'
        );
    }

    public function testLancaTypeErrorQuandoNomeNaoEhString(): void
    {
        $this->expectException(\TypeError::class);

        new Autor(
            CodAu: 1,
            Nome: ['Fulano']
        );
    }

    public function testCoercaoFracaDeTiposQuandoValoresSaoCoerciveis(): void
    {
        $autor = new Autor(
            CodAu: '7',
            Nome: 123
        );

        $this->assertSame(7, $autor->getCodAu());
        $this->assertSame('123', $autor->getNome());
    }
}

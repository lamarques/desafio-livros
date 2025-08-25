<?php

namespace Livros\Application\Domain\Entity;

use App\Livros\Application\Domain\Entity\Assunto;
use PHPUnit\Framework\TestCase;

class AssuntoTest extends TestCase
{
    public function testCriaAssuntoComValoresValidos(): void
    {
        $assunto = new Assunto(
            CodAs: 10,
            Descricao: 'Ciência da Computação'
        );

        $this->assertInstanceOf(Assunto::class, $assunto);
        $this->assertSame(10, $assunto->getCodAs());
        $this->assertSame('Ciência da Computação', $assunto->getDescricao());
    }

    public function testLancaTypeErrorQuandoCodAsNaoEhInt(): void
    {
        $this->expectException(\TypeError::class);

        new Assunto(
            CodAs: ['10'],
            Descricao: 'Qualquer'
        );
    }

    public function testLancaTypeErrorQuandoDescricaoNaoEhString(): void
    {
        $this->expectException(\TypeError::class);

        new Assunto(
            CodAs: 1,
            Descricao: ['abc']
        );
    }

    public function testCoercaoFracaDeTiposQuandoValoresSaoCoerciveis(): void
    {
        $assunto = new Assunto(
            CodAs: '10',
            Descricao: 123
        );

        $this->assertSame(10, $assunto->getCodAs());
        $this->assertSame('123', $assunto->getDescricao());
    }
}

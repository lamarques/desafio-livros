<?php

namespace Tests\Unit\Livros\Dto;

use App\Livros\Dtos\LivroResponseDto;
use PHPUnit\Framework\TestCase;

class LivroResponseDtoTest extends TestCase
{
    /** Helper: tenta getter, depois propriedade pública (qualquer caixa), senão Reflection */
    private function readValue(object $dto, string $expectedName)
    {
        // 1) getter padrão (case-insensitive no PHP)
        $getter = 'get' . $expectedName;
        if (method_exists($dto, $getter)) {
            return $dto->{$getter}();
        }

        // 2) tenta propriedades com variações de caixa
        $candidates = [
            $expectedName,
            lcfirst($expectedName),
            strtoupper($expectedName),
            strtolower($expectedName),
        ];

        foreach ($candidates as $cand) {
            // isset cobre públicas definidas; property_exists cobre mesmo se null
            if (property_exists($dto, $cand)) {
                return $dto->{$cand};
            }
        }

        // 3) Reflection: encontra a propriedade ignorando caixa e lê o valor
        $ref = new \ReflectionClass($dto);
        foreach ($ref->getProperties() as $prop) {
            if (strtolower($prop->getName()) === strtolower($expectedName)) {
                $prop->setAccessible(true);
                return $prop->getValue($dto);
            }
        }

        // se não achou nada, retorna null para o assert falhar com mensagem clara
        return null;
    }

    public function testCriaDtoComValoresValidos(): void
    {
        $dto = new LivroResponseDto(
            1,
            'O Guia do Programador',
            'TechBooks',
            2,
            '2020'
        );

        $this->assertSame(1, $this->readValue($dto, 'Codl'));
        $this->assertSame('O Guia do Programador', $this->readValue($dto, 'Titulo'));
        $this->assertSame('TechBooks', $this->readValue($dto, 'Editora'));
        $this->assertSame(2, $this->readValue($dto, 'Edicao'));
        $this->assertSame('2020', $this->readValue($dto, 'AnoPublicacao'));

        // checagens de tipo
        $this->assertIsInt($this->readValue($dto, 'Codl'));
        $this->assertIsString($this->readValue($dto, 'Titulo'));
        $this->assertIsString($this->readValue($dto, 'Editora'));
        $this->assertIsInt($this->readValue($dto, 'Edicao'));
        $this->assertIsString($this->readValue($dto, 'AnoPublicacao'));
    }

    public function testLancaTypeErrorQuandoCodlNaoEhInteiro(): void
    {
        $this->expectException(\TypeError::class);
        new LivroResponseDto([], 'Titulo', 'Editora', 1, '2020');
    }

    public function testLancaTypeErrorQuandoTituloNaoEhString(): void
    {
        $this->expectException(\TypeError::class);
        new LivroResponseDto(1, [], 'Editora', 1, '2020');
    }

    public function testLancaTypeErrorQuandoEditoraNaoEhString(): void
    {
        $this->expectException(\TypeError::class);
        new LivroResponseDto(1, 'Titulo', [], 1, '2020');
    }

    public function testLancaTypeErrorQuandoEdicaoNaoEhInteiro(): void
    {
        $this->expectException(\TypeError::class);
        new LivroResponseDto(1, 'Titulo', 'Editora', [], '2020');
    }

    public function testLancaTypeErrorQuandoAnoPublicacaoNaoEhString(): void
    {
        $this->expectException(\TypeError::class);
        new LivroResponseDto(1, 'Titulo', 'Editora', 1, []);
    }
}

<?php

namespace Tests\Unit\Livros\Dto;

use App\Livros\Dtos\LivroResponseDto;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class LivroResponseDtoTest extends TestCase
{
    private function readValue(object $dto, string $expectedName)
    {
        $getter = 'get' . $expectedName;
        if (method_exists($dto, $getter)) {
            return $dto->{$getter}();
        }

        $candidates = [
            $expectedName,
            lcfirst($expectedName),
            strtoupper($expectedName),
            strtolower($expectedName),
        ];

        foreach ($candidates as $cand) {
            if (property_exists($dto, $cand)) {
                return $dto->{$cand};
            }
        }

        $ref = new ReflectionClass($dto);
        foreach ($ref->getProperties() as $prop) {
            if (strtolower($prop->getName()) === strtolower($expectedName)) {
                $prop->setAccessible(true);
                return $prop->getValue($dto);
            }
        }

        return null;
    }

    public function testCriaDtoComValoresValidos(): void
    {
        $dto = new LivroResponseDto(
            1,
            'O Guia do Programador',
            'TechBooks',
            2,
            '2020',
            [],
            []
        );

        $this->assertSame(1, $this->readValue($dto, 'Codl'));
        $this->assertSame('O Guia do Programador', $this->readValue($dto, 'Titulo'));
        $this->assertSame('TechBooks', $this->readValue($dto, 'Editora'));
        $this->assertSame(2, $this->readValue($dto, 'Edicao'));
        $this->assertSame('2020', $this->readValue($dto, 'AnoPublicacao'));

        $this->assertIsInt($this->readValue($dto, 'Codl'));
        $this->assertIsString($this->readValue($dto, 'Titulo'));
        $this->assertIsString($this->readValue($dto, 'Editora'));
        $this->assertIsInt($this->readValue($dto, 'Edicao'));
        $this->assertIsString($this->readValue($dto, 'AnoPublicacao'));
    }

    public function testLancaTypeErrorQuandoCodlNaoEhInteiro(): void
    {
        $this->expectException(\TypeError::class);
        new LivroResponseDto([], 'Titulo', 'Editora', 1, '2020', [], []);
    }

    public function testLancaTypeErrorQuandoTituloNaoEhString(): void
    {
        $this->expectException(\TypeError::class);
        new LivroResponseDto(1, [], 'Editora', 1, '2020', [], []);
    }

    public function testLancaTypeErrorQuandoEditoraNaoEhString(): void
    {
        $this->expectException(\TypeError::class);
        new LivroResponseDto(1, 'Titulo', [], 1, '2020', [], []);
    }

    public function testLancaTypeErrorQuandoEdicaoNaoEhInteiro(): void
    {
        $this->expectException(\TypeError::class);
        new LivroResponseDto(1, 'Titulo', 'Editora', [], '2020', [], []);
    }

    public function testLancaTypeErrorQuandoAnoPublicacaoNaoEhString(): void
    {
        $this->expectException(\TypeError::class);
        new LivroResponseDto(1, 'Titulo', 'Editora', 1, [], [], []);
    }
}

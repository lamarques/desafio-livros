<?php

namespace Tests\Unit\Livros\Dto;

use App\Livros\Dtos\LivroRequestDto;
use PHPUnit\Framework\TestCase;

class LivroRequestDtoTest extends TestCase
{
    public function testCriaDtoComValorValido(): void
    {
        $dto = new LivroRequestDto(123);

        $this->assertSame(123, $dto->Codl);
        $this->assertIsInt($dto->Codl);
    }

    public function testLancaTypeErrorQuandoCodlNaoEhInteiro(): void
    {
        $this->expectException(\TypeError::class);

        new LivroRequestDto([]);
    }

    public function testPermiteAlterarCodlComInteiro(): void
    {
        $dto = new LivroRequestDto(1);
        $dto->Codl = 2;

        $this->assertSame(2, $dto->Codl);
    }

    public function testLancaTypeErrorAoAtribuirTipoInvalido(): void
    {
        $dto = new LivroRequestDto(1);

        $this->expectException(\TypeError::class);
        $dto->Codl = 'abc';
    }
}

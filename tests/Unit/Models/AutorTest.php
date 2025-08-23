<?php

namespace Tests\Unit\Models;

use App\Models\Autor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutorTest extends TestCase
{
    use RefreshDatabase;

    public function testTabelaDoModeloEstaCorreta(): void
    {
        $this->assertSame('Autor', (new Autor)->getTable(), 'Nome da tabela deve ser "Autor".');
    }

    public function testChavePrimariaConfiguradaCorretamente(): void
    {
        $model = new Autor;

        $this->assertSame('CodAu', $model->getKeyName(), 'Primary key deve ser "CodAu".');
        // incrementing padrão é true quando PK é integer
        $this->assertTrue($model->getIncrementing(), 'Primary key deve ser auto-increment.');
        $this->assertSame('int', $model->getKeyType(), 'Primary key deve ser do tipo int.');
    }

    public function testTimestampsDesabilitados(): void
    {
        $this->assertFalse((new Autor)->timestamps, 'timestamps deve ser false na model.');
    }

    public function testCamposFillable(): void
    {
        $this->assertEqualsCanonicalizing(['Nome'], (new Autor)->getFillable(), 'Fillable deve conter apenas Nome.');
    }

    public function testCastsConfigurados(): void
    {
        $casts = (new Autor)->getCasts();
        $this->assertArrayHasKey('CodAu', $casts);
        // pode vir como 'int' ou 'integer' dependendo da versão; normalize:
        $this->assertTrue(in_array($casts['CodAu'], ['int', 'integer'], true), 'CodAu deve ser cast para integer.');
    }

    public function testFactoryCriaAutorNoBanco(): void
    {
        $autor = Autor::factory()->create(['Nome' => 'Machado de Assis']);

        $this->assertDatabaseHas((new Autor)->getTable(), [
            $autor->getKeyName() => $autor->getKey(),
            'Nome' => 'Machado de Assis',
        ]);
        $this->assertIsInt($autor->CodAu);
    }

    public function testMassAssignmentNaoPermiteCamposNaoFillable(): void
    {
        // Tentar atribuir um campo inexistente/não fillable
        $autor = Autor::create([
            'Nome' => 'Lima Barreto',
            'campo_invalido' => 'ignorado',
        ]);

        $this->assertDatabaseHas((new Autor)->getTable(), [
            $autor->getKeyName() => $autor->getKey(),
            'Nome' => 'Lima Barreto',
        ]);

        $this->assertFalse(array_key_exists('campo_invalido', $autor->getAttributes()));
    }
}

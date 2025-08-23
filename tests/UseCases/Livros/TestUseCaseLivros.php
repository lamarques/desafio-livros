<?php

namespace Tests\UseCases\Livros;

use App\Livros\Application\LivroApplication;
use App\Livros\Dtos\LivroRequestDto;
use App\Livros\Dtos\LivroResponseDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Livro as LivroModel;

class TestUseCaseLivros extends TestCase
{

    use RefreshDatabase, WithFaker;

    public function testDeveRetornarUmLivro(): void
    {

        $livroModel = new LivroModel();
        $livroModel->Titulo = $this->faker->sentence(3);
        $livroModel->Editora = $this->faker->company();
        $livroModel->Edicao = $this->faker->numberBetween(1, 10);
        $livroModel->AnoPublicacao = $this->faker->year();
        $livroModel->save();

        $application = app(LivroApplication::class);
        $applicationData = $application->getLivro(new LivroRequestDto($livroModel->Codl));

        $this->assertNotEmpty($applicationData);
        $this->assertEquals($applicationData->codl, $livroModel->Codl);
        $this->assertEquals($applicationData->Titulo, $livroModel->Titulo);
        $this->assertEquals($applicationData->Editora, $livroModel->Editora);
        $this->assertEquals($applicationData->Edicao, $livroModel->Edicao);
        $this->assertEquals($applicationData->AnoPublicacao, $livroModel->AnoPublicacao);
        $this->assertIsObject($applicationData);
        $this->assertInstanceOf(LivroResponseDto::class, $applicationData);

    }

}

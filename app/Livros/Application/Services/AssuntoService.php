<?php

namespace App\Livros\Application\Services;

use App\Livros\Application\Domain\Repository\AssuntoRepositoryInterface;
use App\Livros\Dtos\AssuntoRequestDto;
use App\Livros\Dtos\AssuntoResponseDto;
use App\Livros\Exceptions\AssuntoException;
use Illuminate\Database\QueryException;

class AssuntoService
{

    public function __construct(
        private readonly AssuntoRepositoryInterface $assuntoRepository
    )
    {
    }

    public function create(AssuntoRequestDto $assuntoDto): AssuntoResponseDto
    {
        $descricao = trim((string) $assuntoDto->Descricao);

        if (empty($descricao)) {
            throw AssuntoException::invalid('Descrição é obrigatória.', [
                'Descricao' => ['Campo obrigatório.'],
            ]);
        }

        if (mb_strlen($descricao) > 20) {
            throw AssuntoException::invalid('Descrição excede o limite de 20 caracteres.', [
                'Descricao' => ['Máximo de 20 caracteres.'],
            ]);
        }

        try {
            if ($this->assuntoRepository->saveAssunto($descricao)) {
                $codAs = $this->assuntoRepository->getLastInsertedId();

                if ($codAs === null) {
                    throw new AssuntoException(
                        'Não foi possível obter o identificador do assunto recém-criado.',
                        500
                    );
                }

                return new AssuntoResponseDto(
                    CodAs: $codAs,
                    Descricao: $descricao
                );
            }
        } catch (QueryException $e) {
            $sqlState   = $e->errorInfo[0] ?? null;
            $driverCode = $e->errorInfo[1] ?? null;

            // 23000 + 1062 (MySQL): duplicidade/unique
            if ($sqlState === '23000' && (string) $driverCode === '1062') {
                throw AssuntoException::alreadyExists($descricao);
            }

            // 22001: string data, right truncated (ultrapassou tamanho)
            if ($sqlState === '22001') {
                throw AssuntoException::invalid('Descrição excede o limite de 20 caracteres.', [
                    'Descricao' => ['Máximo de 20 caracteres.'],
                ]);
            }

            // Fallback: erro interno
            throw new AssuntoException(
                'Erro ao salvar o assunto.',
                500,
                ['pdo' => $e->errorInfo],
                0,
                $e
            );
        }
        throw new AssuntoException(
            'Erro ao salvar o assunto.',
            500,
            ['descricao' => $descricao]
        );
    }

    public function list(): array
    {
        $assuntos = $this->assuntoRepository->getAllAssuntos();

        if (empty($assuntos)) {
            return [];
        }

        return array_map(function ($assunto) {
            return new AssuntoResponseDto(
                CodAs: $assunto->getCodAs(),
                Descricao: $assunto->getDescricao()
            );
        }, $assuntos);
    }

    public function show(int $id): ?AssuntoResponseDto
    {
        $assunto = $this->assuntoRepository->getAssunto($id);

        if (!$assunto) {
            return null;
        }

        return new AssuntoResponseDto(
            CodAs: $assunto->getCodAs(),
            Descricao: $assunto->getDescricao()
        );
    }
}

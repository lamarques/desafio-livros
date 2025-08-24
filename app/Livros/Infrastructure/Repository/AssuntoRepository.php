<?php

namespace App\Livros\Infrastructure\Repository;

use App\Livros\Application\Domain\Entity\Assunto;
use App\Livros\Application\Domain\Repository\AssuntoRepositoryInterface;
use App\Models\Assunto as AssuntoModel;

class AssuntoRepository implements AssuntoRepositoryInterface
{

    private int $lastInsertedId;

    public function __construct(private readonly AssuntoModel $assuntoModel)
    {
    }

    #[\Override] public function getAssunto(int $codAs): ?Assunto
    {
        $assuntoData = $this->assuntoModel->find($codAs);

        if (!$assuntoData) {
            return null;
        }

        return new Assunto(
            CodAs: $assuntoData->CodAs,
            Descricao: $assuntoData->Descricao
        );
    }

    #[\Override] public function saveAssunto(string $descricao): bool
    {
        $assunto = new AssuntoModel();
        $assunto->Descricao = $descricao;
        if ($assunto->save()) {
            $this->lastInsertedId = $assunto->CodAs;
            return true;
        }
        return false;
    }

    #[\Override] public function updateAssunto(int $codAs, string $descricao): bool
    {
        $assuntoData = $this->assuntoModel->find($codAs);

        if (!$assuntoData) {
            return false;
        }

        $assuntoData->Descricao = $descricao;
        return $assuntoData->save();
    }

    #[\Override] public function deleteAssunto(int $codAs): bool
    {
        $assuntoData = $this->assuntoModel->find($codAs);

        if (!$assuntoData) {
            return false;
        }

        return $assuntoData->delete();
    }

    #[\Override] public function getLastInsertedId()
    {
        // TODO: Implement getLastInsertedId() method.
    }
}

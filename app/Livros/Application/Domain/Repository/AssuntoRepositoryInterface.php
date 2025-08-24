<?php

namespace App\Livros\Application\Domain\Repository;

use App\Livros\Application\Domain\Entity\Assunto;

interface AssuntoRepositoryInterface
{

    public function getAssunto(int $codAs): ?Assunto;

    public function saveAssunto(string $descricao): bool;

    public function updateAssunto(int $codAs, string $descricao): bool;

    public function deleteAssunto(int $codAs): bool;

    public function getLastInsertedId();

}

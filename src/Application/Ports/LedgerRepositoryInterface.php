<?php

namespace App\Application\Ports;

use App\Domain\Ledger;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

interface LedgerRepositoryInterface
{
    public function getById(Uuid $id): Ledger;

    public function save(Ledger $ledger): void;

    /**
     * @return Collection<Ledger>
     */
    public function getAll(): Collection;
}

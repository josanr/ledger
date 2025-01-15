<?php

namespace App\Application\Ports;

use App\Application\Exceptions\NotFoundException;
use App\Application\Exceptions\StoreException;
use App\Domain\Ledger;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

interface LedgerRepositoryInterface
{
    /**
     * @throws NotFoundException
     */
    public function getById(Uuid $id): Ledger;

    /**
     * @throws StoreException
     */
    public function save(Ledger $ledger): void;

    /**
     * @return Collection<Ledger>
     */
    public function getAll(): Collection;
}
<?php

namespace App\Infrastructure\Ledgers\Repositories;

use App\Application\Exceptions\NotFoundException;
use App\Application\Exceptions\StoreException;
use App\Application\Ports\LedgerRepositoryInterface;
use App\Domain\Ledger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class LedgerRepository extends ServiceEntityRepository implements LedgerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ledger::class);
    }

    public function getById(Uuid $id): Ledger
    {
        /** @var Ledger $ledger */
        $ledger = $this->find($id);
        if ($ledger === null) {
            throw new NotFoundException(sprintf('Ledger not found by id: %s', $id));
        }

        return $ledger;
    }

    public function save(Ledger $ledger): void
    {
        $em = $this->getEntityManager();
        try {
            $em->persist($ledger);
            $em->flush();
        } catch (\Exception $e) {
            throw new StoreException(sprintf('Could not persist ledger code: %s', $ledger->getCode()), 1, $e);
        }
    }

    public function getAll(): Collection
    {
        return new ArrayCollection($this->findAll());
    }
}

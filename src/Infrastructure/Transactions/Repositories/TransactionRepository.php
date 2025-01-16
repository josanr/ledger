<?php

namespace App\Infrastructure\Transactions\Repositories;

use App\Application\Exceptions\StoreException;
use App\Application\Ports\TransactionsRepositoryInterface;
use App\Domain\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository implements TransactionsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function save(Transaction $transaction): void
    {
        $em = $this->getEntityManager();
        try {
            $em->persist($transaction);
            $em->flush();
        }catch (UniqueConstraintViolationException $e) {
            throw new StoreException(sprintf('Transaction already exists with code: %s', $transaction->getExternalId()), 1, $e);
        }catch (\Exception $e) {
            throw new StoreException(sprintf('Could not persist transaction code: %s', $transaction->getExternalId()), 1, $e);
        }
    }
}

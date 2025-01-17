<?php

namespace App\Infrastructure\Balances\Repositories;

use App\Application\Ports\BalanceRepositoryInterface;
use App\Domain\BalanceSource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class BalanceRepository implements BalanceRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
    }

    /**
     * @return Collection<BalanceSource>
     */
    public function getBalanceSources(Uuid $ledgerId): Collection
    {
        $sql = '
            SELECT SUM(amount) as amount, currency_id, direction
            FROM transaction
            WHERE ledger_id_id = :ledgerId
            GROUP BY currency_id, direction
        ';
        $stmt = $this->em->getConnection()
            ->prepare($sql);
        $stmt->bindValue('ledgerId', $ledgerId);
        $rows = $stmt->executeQuery();
        $result = new ArrayCollection();
        foreach ($rows->iterateAssociative() as $row) {
            $balance = new BalanceSource((int) $row['amount'], $row['currency_id'], $row['direction']);
            $result->add($balance);
        }

        return $result;
    }
}

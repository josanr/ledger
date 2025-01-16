<?php

namespace App\Application\UseCases\Balances;

use App\Application\Exceptions\NotFoundException;
use App\Application\Ports\BalanceRepositoryInterface;
use App\Application\Ports\LedgerRepositoryInterface;
use App\Domain\Balance;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class GetBalanceUseCase
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly LedgerRepositoryInterface $ledgerRepository,
        private readonly BalanceRepositoryInterface $balanceRepository
    ) {
    }

    /**
     * @param Uuid $ledgerId
     * @return Collection<Balance>
     * @throws NotFoundException
     */
    public function execute(Uuid $ledgerId): Collection
    {
        try {
            $ledger = $this->ledgerRepository->getById($ledgerId);
            $sources = $this->balanceRepository->getBalanceSources($ledger->getId());
            $filter = [];
            foreach ($sources as $source) {
                if (!isset($filter[$source->getCurrencyId()])) {
                    $filter[$source->getCurrencyId()] = [
                        'total' => 0,
                        'debit' => 0,
                        'credit' => 0,
                    ];
                }
                if ($source->isDebit()) {
                    $filter[$source->getCurrencyId()]['total'] += $source->getAmount();
                    $filter[$source->getCurrencyId()]['debit'] += $source->getAmount();
                } else {
                    $filter[$source->getCurrencyId()]['total'] -= $source->getAmount();
                    $filter[$source->getCurrencyId()]['credit'] += $source->getAmount();
                }
            }

            $result = new ArrayCollection();
            foreach ($filter as $currencyId => $data) {
                $result[] = new Balance(
                    ledger: $ledger,
                        balance: $data['total'],
                        debit: $data['debit'],
                        credit: $data['credit'],
                        currency: $currencyId,
                );
            }

            return $result;
        } catch (NotFoundException $e) {
            $this->logger->info(sprintf('Not found ledger for balance with id: %s', $ledgerId), [
                'message' => $e->getMessage(),
                'exception' => $e
            ]);
            throw $e;
        }
    }
}
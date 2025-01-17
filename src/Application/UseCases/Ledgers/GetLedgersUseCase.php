<?php

namespace App\Application\UseCases\Ledgers;

use App\Application\Ports\LedgerRepositoryInterface;
use App\Domain\Ledger;
use Doctrine\Common\Collections\Collection;
use Psr\Log\LoggerInterface;

class GetLedgersUseCase
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly LedgerRepositoryInterface $ledgerRepository
    ) {
    }

    /**
     * @return Collection<Ledger>
     */
    public function execute(): Collection
    {
        $this->logger->info('Requested list of ledgers');
        try {
            return $this->ledgerRepository->getAll();
        } catch (\Exception $e) {
            $this->logger->error('Error returning ledgers', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
            throw $e;
        }
    }
}

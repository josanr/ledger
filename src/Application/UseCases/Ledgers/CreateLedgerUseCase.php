<?php

namespace App\Application\UseCases\Ledgers;

use App\Application\Exceptions\StoreException;
use App\Application\Ports\LedgerRepositoryInterface;
use App\Domain\Ledger;
use App\Infrastructure\Ledgers\Api\Requests\CreateLedgerRequest;
use Psr\Log\LoggerInterface;

class CreateLedgerUseCase
{

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly LedgerRepositoryInterface $ledgerRepository)
    {
    }

    public function execute(CreateLedgerRequest $request): Ledger
    {
        $this->logger->info(sprintf('Requested creation of ledger %s', $request->name));
        try {
            $ledger = new Ledger();
            $ledger->setName($request->name);
            $ledger->setDescription($request->description);
            $ledger->setCode($request->code);
            $ledger->setLedgerType($request->ledgerType);
            $ledger->setCurrencyId($request->currency);
            $ledger->setCreatedAt(new \DateTime());
            $ledger->setUpdatedAt(new \DateTime());
            $this->ledgerRepository->save($ledger);
            return $ledger;
        }catch (StoreException $e) {
            $this->logger->error(sprintf('Error creating ledger %s', $request->name), [
                'message' => $e->getMessage(),
                'exception' => $e
            ]);
            throw $e;
        }
    }
}
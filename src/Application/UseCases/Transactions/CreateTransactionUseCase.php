<?php

namespace App\Application\UseCases\Transactions;

use App\Application\Exceptions\NotFoundException;
use App\Application\Exceptions\StoreException;
use App\Application\Ports\LedgerRepositoryInterface;
use App\Application\Ports\TransactionsRepositoryInterface;
use App\Domain\Transaction;
use App\Infrastructure\Transactions\Requests\CreateTransactionRequest;
use Psr\Log\LoggerInterface;

class CreateTransactionUseCase
{

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TransactionsRepositoryInterface $transactionsRepository,
        private readonly LedgerRepositoryInterface $ledgerRepository
    )
    {
    }

    public function execute(CreateTransactionRequest $request): Transaction
    {
        try {
            $ledger = $this->ledgerRepository->getById($request->ledgerId);
            $transaction = new Transaction();
            $transaction->setExternalId($request->externalId);
            $transaction->setLedgerId($ledger);
            $transaction->setDescription($request->description);
            $transaction->setTransactionDate($request->transactionDate);
            $transaction->setDirection($request->direction);
            $transaction->setAmount($request->amount);
            $transaction->setCurrencyId($request->currency);
            $transaction->setCreatedAt(new \DateTime());
            $transaction->setUpdatedAt(new \DateTime());
            $this->transactionsRepository->save($transaction);
            return $transaction;
        } catch (NotFoundException $e) {
            $this->logger->info(sprintf('Not found ledger for transaction with id: %s', $request->ledgerId), [
                'message' => $e->getMessage(),
                'exception' => $e
            ]);
            throw new StoreException('Not found ledger for transaction', 2, $e);
        } catch (StoreException $e) {
            $this->logger->info(sprintf('Unexpected exception: %s', $request->ledgerId), [
                'message' => $e->getMessage(),
                'exception' => $e
            ]);
            throw $e;
        }
    }
}
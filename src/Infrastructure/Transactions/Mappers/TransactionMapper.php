<?php

namespace App\Infrastructure\Transactions\Mappers;

use App\Domain\Transaction;
use App\Infrastructure\Transactions\Requests\TransactionItemResponse;

class TransactionMapper
{
    public function mapToResponse(Transaction $transaction): TransactionItemResponse
    {
        $dto = new TransactionItemResponse();
        $dto->id = $transaction->getId();
        $dto->ledgerId = $transaction->getLedgerId()?->getId();
        $dto->amount = $transaction->getAmount();
        $dto->currency = $transaction->getCurrencyId();
        $dto->description = $transaction->getDescription();
        $dto->direction = $transaction->getDirection();
        $dto->externalId = $transaction->getExternalId();
        $dto->transactionDate = $transaction->getTransactionDate();
        return $dto;
    }
}
<?php

namespace App\Infrastructure\Transactions\Requests;

use Symfony\Component\Uid\Uuid;

class TransactionItemResponse
{
    public Uuid $id;

    public Uuid $externalId;

    public Uuid $ledgerId;

    public int $amount;

    public string $currency;

    public string $direction;

    public string $description = '';

    public \DateTimeInterface $transactionDate;
}

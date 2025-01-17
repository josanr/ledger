<?php

namespace App\Application\Ports;

use App\Domain\Transaction;

interface TransactionsRepositoryInterface
{
    public function save(Transaction $transaction): void;
}

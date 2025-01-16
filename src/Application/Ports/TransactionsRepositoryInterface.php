<?php

namespace App\Application\Ports;

use App\Application\Exceptions\StoreException;
use App\Domain\Transaction;

interface TransactionsRepositoryInterface
{
    /**
     * @throws StoreException
     */
    public function save(Transaction $transaction): void;

}
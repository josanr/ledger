<?php

namespace App\Domain;

class Balance
{
    public function __construct(
        private readonly Ledger $ledger,
        private readonly int $balance,
        private readonly int $debit,
        private readonly int $credit,
        private readonly string $currency
    ) {
    }

    public function getLedger(): Ledger
    {
        return $this->ledger;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function getDebit(): int
    {
        return $this->debit;
    }

    public function getCredit(): int
    {
        return $this->credit;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}

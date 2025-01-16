<?php

namespace App\Domain;

class BalanceSource
{
    public const DIRRECTION_DEBIT = 'DEBIT';

    public function __construct(
        private readonly int $amount,
        private readonly string $currencyId,
        private readonly string $direction
    ) {
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrencyId(): string
    {
        return $this->currencyId;
    }

    public function isDebit(): bool
    {
        return $this->direction === self::DIRRECTION_DEBIT;
    }
}
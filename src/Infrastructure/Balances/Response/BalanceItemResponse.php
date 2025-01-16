<?php

namespace App\Infrastructure\Balances\Response;

use Symfony\Component\Uid\Uuid;

class BalanceItemResponse
{
    public Uuid $ledgerId;
    public int $debit;
    public int $credit;
    public int $balance;
    public string $currency;
}
<?php

namespace App\Infrastructure\Balances\Mappers;

use App\Domain\Balance;
use App\Infrastructure\Balances\Response\BalanceItemResponse;

class BalanceMapper
{
    public function mapToResponse(Balance $balance): BalanceItemResponse
    {
        $dto = new BalanceItemResponse();
        $dto->ledgerId = $balance->getLedger()->getId();
        $dto->debit = $balance->getDebit();
        $dto->credit = $balance->getCredit();
        $dto->balance = $balance->getBalance();
        $dto->currency = $balance->getCurrency();
        return $dto;
    }
}
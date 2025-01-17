<?php

namespace App\Infrastructure\Ledgers\Api\Mappers;

use App\Domain\Ledger;
use App\Infrastructure\Ledgers\Api\Response\LedgerItemResponse;

class LedgerMapper
{
    public function mapToResponse(Ledger $ledger): LedgerItemResponse
    {

        $response = new LedgerItemResponse();

        $response->id = $ledger->getId();
        $response->name = $ledger->getName();
        $response->code = $ledger->getCode();
        $response->currencyId = $ledger->getCurrencyId();
        $response->ledgerType = $ledger->getLedgerType();
        $response->createdAt = $ledger->getCreatedAt();
        $response->updatedAt = $ledger->getUpdatedAt();

        return $response;
    }
}

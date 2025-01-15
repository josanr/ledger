<?php

namespace App\Infrastructure\Ledgers\Requests;

final class CreateLedgerRequest
{
    public ?string $code;
    public ?string $name;
    public ?string $description;
    public ?string $ledgerType;
}
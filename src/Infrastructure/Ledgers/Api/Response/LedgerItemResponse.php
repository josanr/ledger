<?php

namespace App\Infrastructure\Ledgers\Api\Response;

use Symfony\Component\Uid\Uuid;

class LedgerItemResponse
{
    public Uuid $id;
    public string $code;
    public string $name;
    public string $description;
    public string $ledgerType;
    public string $currencyId;
    public ?\DateTimeInterface $createdAt;
    public ?\DateTimeInterface $updatedAt;
}
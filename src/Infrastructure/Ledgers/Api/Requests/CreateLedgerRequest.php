<?php

namespace App\Infrastructure\Ledgers\Api\Requests;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateLedgerRequest
{
    #[Assert\NotBlank]
    public string $code;

    #[Assert\NotBlank]
    public string $name;

    #[Assert\NotBlank]
    public string $description;

    #[Assert\NotBlank]
    public string $ledgerType;

    #[Assert\NotBlank]
    #[Assert\Currency]
    public string $currency;
}

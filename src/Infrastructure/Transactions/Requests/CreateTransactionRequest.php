<?php

namespace App\Infrastructure\Transactions\Requests;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTransactionRequest
{
    #[Assert\NotBlank]
    public Uuid $externalId;

    #[Assert\NotBlank]
    public Uuid $ledgerId;

    #[Assert\NotBlank]
    public int $amount;

    #[Assert\NotBlank]
    #[Assert\Currency]
    public string $currency;

    #[Assert\NotBlank]
    #[Assert\Choice(['CREDIT', 'DEBIT'])]
    public string $direction;

    public string $description = '';

    #[Assert\NotBlank]
    public \DateTimeInterface $transactionDate;
}

<?php

namespace App\Infrastructure\Ledgers\Requests;

use Symfony\Component\Uid\Uuid;

class CreateLedgerResponse
{
    public Uuid $id;
}
<?php

namespace App\Application\Ports;

use App\Domain\BalanceSource;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

interface BalanceRepositoryInterface
{
    /**
     * @param Uuid $ledgerId
     * @return Collection<BalanceSource>
     */
    public function getBalanceSources(Uuid $ledgerId): Collection;
}
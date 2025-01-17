<?php

namespace App\Tests\Unit\Application\UseCases\Balances;

use App\Application\Exceptions\NotFoundException;
use App\Application\Ports\BalanceRepositoryInterface;
use App\Application\Ports\LedgerRepositoryInterface;
use App\Application\UseCases\Balances\GetBalanceUseCase;
use App\Domain\Balance;
use App\Domain\BalanceSource;
use App\Domain\Ledger;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class GetBalanceUseCaseTest extends TestCase
{
    private BalanceRepositoryInterface $balanceRepository;

    private LedgerRepositoryInterface $ledgerRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->balanceRepository = $this->createMock(BalanceRepositoryInterface::class);
        $this->ledgerRepository = $this->createMock(LedgerRepositoryInterface::class);
    }

    public function testGetBalanceThrowsExceptionOnMissingLedger(): void
    {
        $this->expectException(NotFoundException::class);
        $id = Uuid::v7();
        $this->ledgerRepository
            ->method('getById')
            ->willThrowException(new NotFoundException(sprintf('Ledger not found by id: %s', $id)));

        $useCase = new GetBalanceUseCase(
            $this->createMock(LoggerInterface::class),
            $this->ledgerRepository,
            $this->balanceRepository
        );

        $useCase->execute($id);
    }

    public function testGetBalanceReturnsCorrectBalance(): void
    {
        $ledger = new Ledger();
        $ledger->setId(Uuid::v7());
        $this->ledgerRepository
            ->method('getById')
            ->willReturn($ledger);

        $sources = new ArrayCollection();
        $sources[] = new BalanceSource(100, 'USD', BalanceSource::DIRRECTION_CREDIT);
        $sources[] = new BalanceSource(1000, 'USD', BalanceSource::DIRRECTION_DEBIT);

        $this->balanceRepository
            ->method('getBalanceSources')
            ->with($ledger->getId())
            ->willReturn($sources);

        $useCase = new GetBalanceUseCase(
            $this->createMock(LoggerInterface::class),
            $this->ledgerRepository,
            $this->balanceRepository
        );

        /** @var Balance[] $result */
        $result = $useCase->execute($ledger->getId());

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals(900, $result[0]->getBalance());
        $this->assertEquals(100, $result[0]->getCredit());
        $this->assertEquals(1000, $result[0]->getDebit());
    }

    public function testGetBalanceReturnsCorrectNumberOfBalances(): void
    {
        $ledger = new Ledger();
        $ledger->setId(Uuid::v7());
        $this->ledgerRepository
            ->method('getById')
            ->willReturn($ledger);

        $sources = new ArrayCollection();
        $sources[] = new BalanceSource(1000, 'EUR', BalanceSource::DIRRECTION_CREDIT);
        $sources[] = new BalanceSource(100, 'USD', BalanceSource::DIRRECTION_CREDIT);
        $sources[] = new BalanceSource(1000, 'USD', BalanceSource::DIRRECTION_DEBIT);
        $sources[] = new BalanceSource(200, 'EUR', BalanceSource::DIRRECTION_DEBIT);
        $sources[] = new BalanceSource(600, 'MDL', BalanceSource::DIRRECTION_CREDIT);
        $sources[] = new BalanceSource(1000, 'MDL', BalanceSource::DIRRECTION_DEBIT);

        $this->balanceRepository
            ->method('getBalanceSources')
            ->with($ledger->getId())
            ->willReturn($sources);

        $useCase = new GetBalanceUseCase(
            $this->createMock(LoggerInterface::class),
            $this->ledgerRepository,
            $this->balanceRepository
        );

        /** @var Collection<Balance> $result */
        $result = $useCase->execute($ledger->getId());

        // Assert
        $this->assertCount(3, $result);

        $usd = $result->filter(fn (Balance $balance) => $balance->getCurrency() === 'USD')
            ->first();
        $eur = $result->filter(fn (Balance $balance) => $balance->getCurrency() === 'EUR')
            ->first();
        $mdl = $result->filter(fn (Balance $balance) => $balance->getCurrency() === 'MDL')
            ->first();

        $this->assertEquals(900, $usd->getBalance());
        $this->assertEquals(-800, $eur->getBalance());
        $this->assertEquals(400, $mdl->getBalance());
    }
}

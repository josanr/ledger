<?php

namespace App\Tests\Unit\Application\UseCases\Ledgers;

use App\Application\Exceptions\StoreException;
use App\Application\Ports\LedgerRepositoryInterface;
use App\Application\UseCases\Ledgers\CreateLedgerUseCase;
use App\Domain\Ledger;
use App\Infrastructure\Ledgers\Api\Requests\CreateLedgerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CreateLedgerUseCaseTest extends TestCase
{

    private LoggerInterface $logger;
    private LedgerRepositoryInterface $ledgerRepository;

    private function buildLedgerRequest(): CreateLedgerRequest
    {
        $lgRequest = new CreateLedgerRequest();
        $lgRequest->name = 'Test ledger';
        $lgRequest->description = 'Test ledger description';
        $lgRequest->code = 'TEST';
        $lgRequest->ledgerType = 'asset';
        $lgRequest->currency = 'USD';

        return $lgRequest;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->ledgerRepository = $this->createMock(LedgerRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->useCase = new CreateLedgerUseCase(
            $this->logger,
            $this->ledgerRepository
        );

    }

    public function testCreateLedgerSuccess(): void
    {
        $lgRequest = $this->buildLedgerRequest();

        $this->ledgerRepository
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function (Ledger $ledger) use ($lgRequest) {
                    return
                        $ledger->getName() === $lgRequest->name &&
                        $ledger->getDescription() === $lgRequest->description &&
                        $ledger->getCode() === $lgRequest->code &&
                        $ledger->getLedgerType() === $lgRequest->ledgerType &&
                        $ledger->getCurrencyId() === $lgRequest->currency;
                })
            );

        $this->logger->expects($this->once())
            ->method('info')
            ->with(sprintf('Requested creation of ledger %s', $lgRequest->name));

        $this->useCase->execute($lgRequest);
    }

    public function testCreateLedgerFailureOnStoreExceptions(): void
    {
        $lgRequest = $this->buildLedgerRequest();
        $this->ledgerRepository
            ->method('save')
            ->willThrowException(new StoreException('Unexpected exception'));

        $this->logger->expects($this->once())
            ->method('error')
            ->with(sprintf('Error creating ledger %s', $lgRequest->name));

        $this->expectException(StoreException::class);

        $this->useCase->execute($lgRequest);
    }

}

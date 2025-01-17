<?php

namespace App\Tests\Unit\Application\UseCases\Transactions;

use App\Application\Exceptions\NotFoundException;
use App\Application\Exceptions\StoreException;
use App\Application\Ports\LedgerRepositoryInterface;
use App\Application\Ports\TransactionsRepositoryInterface;
use App\Application\UseCases\Transactions\CreateTransactionUseCase;
use App\Domain\BalanceSource;
use App\Domain\Ledger;
use App\Domain\Transaction;
use App\Infrastructure\Transactions\Requests\CreateTransactionRequest;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class CreateTransactionUseCaseTest extends TestCase
{

    private TransactionsRepositoryInterface $transactionRepository;
    private LoggerInterface $logger;
    private LedgerRepositoryInterface $ledgerRepository;

    /**
     * @return CreateTransactionRequest
     */
    private function buildTransactionRequest(): CreateTransactionRequest
    {
        $txRequest = new CreateTransactionRequest();
        $txRequest->ledgerId = Uuid::v7();
        $txRequest->externalId = Uuid::v7();
        $txRequest->amount = 100;
        $txRequest->description = 'Test transaction';
        $txRequest->currency = 'USD';
        $txRequest->direction = BalanceSource::DIRRECTION_DEBIT;
        $txRequest->transactionDate = new \DateTime();
        return $txRequest;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->transactionRepository = $this->createMock(TransactionsRepositoryInterface::class);
        $this->ledgerRepository = $this->createMock(LedgerRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->useCase = new CreateTransactionUseCase(
            $this->logger,
            $this->transactionRepository,
            $this->ledgerRepository
        );

    }

    public function testCreateTransactionSuccess(): void
    {
        $txRequest = $this->buildTransactionRequest();

        $testLedger = new Ledger();
        $testLedger->setId($txRequest->ledgerId);
        $this->ledgerRepository->method('getById')->willReturn($testLedger);

        $this->transactionRepository
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function (Transaction $transaction) use ($txRequest) {
                    return
                        $transaction->getAmount() === $txRequest->amount &&
                        $transaction->getDescription() === $txRequest->description &&
                        $transaction->getExternalId() === $txRequest->externalId &&
                        $transaction->getDirection() === $txRequest->direction &&
                        $transaction->getLedgerId()->getId() === $txRequest->ledgerId;
                })
            );

        $this->logger->expects($this->once())
            ->method('info')
            ->with(sprintf('Requested creation of Transaction with external id %s', $txRequest->externalId));

        $this->useCase->execute($txRequest);
    }

    public function testCreateTransactionFailureOnNonExistingLedger(): void
    {
        $txRequest = $this->buildTransactionRequest();
        $this->ledgerRepository->expects($this->once())
            ->method('getById')
            ->willThrowException(new NotFoundException(sprintf('Ledger not found by id: %s', $txRequest->ledgerId)));

        $this->logger->expects($this->once())
            ->method('error')
            ->with(sprintf('Not found ledger for transaction with id: %s', $txRequest->ledgerId));

        $this->expectException(StoreException::class);
        $this->expectExceptionMessage('Not found ledger for transaction');

        $this->useCase->execute($txRequest);
    }

    public function testCreateTransactionFailureOnStoreExceptions(): void
    {
        $txRequest = $this->buildTransactionRequest();
        $testLedger = new Ledger();
        $testLedger->setId($txRequest->ledgerId);
        $this->ledgerRepository
            ->method('getById')
            ->willReturn($testLedger);
        $this->transactionRepository
            ->method('save')
            ->willThrowException(new StoreException('Unexpected exception'));
        $this->logger->expects($this->once())
            ->method('error')
            ->with(sprintf('Unexpected exception: %s', $txRequest->ledgerId));

        $this->expectException(StoreException::class);

        $this->useCase->execute($txRequest);
    }
}

<?php

namespace Api;

use App\Domain\Ledger;
use App\Domain\Transaction;
use App\Tests\Support\ApiTester;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class CreateTransactionCest
{
    // tests
    public function createTransaction(ApiTester $I)
    {
        $id = $I->haveInRepository(Ledger::class, [
            'code' => "TRANSACTION_TEST",
            'name' => "Transaction Test Ledger",
            'description' => "Test ledger for transactions",
            'ledgerType' => "asset",
            'currencyId' => "USD",
            'created_at' => new \DateTime(),
            'updated_at' => new \DateTime(),
        ]);

        $payload = [
            "externalId" => Uuid::v7(),
            "ledgerId" => $id,
            "amount" => 1000,
            "currency" => "EUR",
            "direction" => "CREDIT",
            "description" => "Initial deposit",
            "transactionDate" => "2025-01-15 14:49:49.000000"
        ];
        $I->sendPost('/transactions', $payload);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesJsonPath('$.id');
        $I->seeResponseJsonMatchesJsonPath('$.externalId');
        $I->seeResponseJsonMatchesJsonPath('$.ledgerId');
        $I->seeResponseJsonMatchesJsonPath('$.amount');
        $I->seeResponseJsonMatchesJsonPath('$.currency');
        $I->seeResponseJsonMatchesJsonPath('$.direction');
        $I->seeResponseJsonMatchesJsonPath('$.description');
        $I->seeResponseJsonMatchesJsonPath('$.transactionDate');
        $newId = $I->grabDataFromResponseByJsonPath('$.id');

        $I->canSeeInRepository(Transaction::class, ['id' => $newId]);
    }

    public function createTransactionFailureNonExistingLedger(ApiTester $I)
    {
        $payload = [
            "externalId" => Uuid::v7(),
            "ledgerId" => Uuid::v7(),
            "amount" => 1000,
            "currency" => "EUR",
            "direction" => "CREDIT",
            "description" => "Initial deposit",
            "transactionDate" => "2025-01-15 14:49:49.000000"
        ];
        $I->sendPost('/transactions', $payload);
        $I->seeResponseCodeIs(Response::HTTP_EXPECTATION_FAILED);
    }

    public function createTransactionFailureInputValidation(ApiTester $I)
    {
        $payload = [
            "externalId" => Uuid::v7(),
            "ledgerId" => Uuid::v7(),
            "amount" => "1000",
            "currency" => "Dollars",
            "direction" => "CREDIT",
            "description" => "Initial deposit",
            "transactionDate" => "2025-01-15 14:49:49.000000"
        ];
        $I->sendPost('/transactions', $payload);
        $I->seeResponseCodeIs(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

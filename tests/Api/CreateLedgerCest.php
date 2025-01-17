<?php

namespace App\Tests\Api;

use App\Domain\Ledger;
use App\Tests\Support\ApiTester;
use Symfony\Component\HttpFoundation\Response;

class CreateLedgerCest
{
    // tests
    public function createLedger(ApiTester $I)
    {
        $code = "MAIN_TEST";
        $payload = [
            "code" => $code,
            "name" => "Main Ledger",
            "description" => "Primary ledger for all transactions",
            "ledgerType" => "GENERAL",
            "currency" => "USD"
        ];
        $I->sendPost('/ledgers', $payload);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesJsonPath('$.id');
        $I->seeResponseJsonMatchesJsonPath('$.code');
        $I->seeResponseJsonMatchesJsonPath('$.name');
        $I->seeResponseJsonMatchesJsonPath('$.ledgerType');
        $I->seeResponseJsonMatchesJsonPath('$.currencyId');

        $I->canSeeInRepository(Ledger::class, ['code' => $code]);
    }

    public function createLedgerFailureInputValidation(ApiTester $I)
    {
        $code = "MAIN_TEST";
        $payload = [
            "code" => $code,
            "name" => "Main Ledger",
            "description" => "Primary ledger for all transactions",
            "ledgerType" => "GENERAL",
            "currency" => "Dollar"
        ];
        $I->sendPost('/ledgers', $payload);
        $I->seeResponseCodeIs(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function createLedgerFailureLedgerWithSameCodeExists(ApiTester $I)
    {
        $code = "MAIN_TEST";

        $I->haveInRepository(Ledger::class, [
            'code' => $code,
            'name' => "Transaction Test Ledger",
            'description' => "Test ledger for transactions",
            'ledgerType' => "asset",
            'currencyId' => "USD",
            'created_at' => new \DateTime(),
            'updated_at' => new \DateTime(),
        ]);

        $payload = [
            "code" => $code,
            "name" => "Main Ledger",
            "description" => "Primary ledger for all transactions",
            "ledgerType" => "GENERAL",
            "currency" => "USD"
        ];
        $I->sendPost('/ledgers', $payload);
        $I->seeResponseCodeIs(Response::HTTP_EXPECTATION_FAILED);
    }
}

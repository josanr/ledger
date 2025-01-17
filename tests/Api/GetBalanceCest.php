<?php

namespace Api;

use App\Domain\Ledger;
use App\Domain\Transaction;
use App\Tests\Support\ApiTester;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Date;

class GetBalanceCest
{
    // tests
    public function getBalance(ApiTester $I)
    {
        $expectedBalance = 500;

        $id = $I->haveInRepository(Ledger::class, [
            'code' => "TRANSACTION_TEST",
            'name' => "Transaction Test Ledger",
            'description' => "Test ledger for transactions",
            'ledgerType' => "asset",
            'currencyId' => "USD",
            'created_at' => new \DateTime(),
            'updated_at' => new \DateTime(),
        ]);
        $ledger = $I->grabEntityFromRepository(Ledger::class, ['id' => $id]);
        $I->haveInRepository(Transaction::class, [
            'externalId' => Uuid::v7(),
            'description' => "test transaction",
            'transactionDate' => new \DateTime(),
            'ledgerId' => $ledger,
            'amount' => 1000,
            'currencyId' => 'USD',
            'direction' => 'DEBIT',
            'createdAt' => new \DateTime(),
            'updatedAt' => new \DateTime(),
        ]);
        $I->haveInRepository(Transaction::class, [
            'externalId' => Uuid::v7(),
            'description' => "test transaction",
            'transactionDate' => new \DateTime(),
            'ledgerId' => $ledger,
            'amount' => 500,
            'currencyId' => 'USD',
            'direction' => 'CREDIT',
            'createdAt' => new \DateTime(),
            'updatedAt' => new \DateTime(),
        ]);

        $I->sendGet('/balances/' . $id);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesJsonPath('$[0].ledgerId');
        $I->seeResponseJsonMatchesJsonPath('$[0].debit');
        $I->seeResponseJsonMatchesJsonPath('$[0].credit');
        $I->seeResponseJsonMatchesJsonPath('$[0].balance');
        $I->seeResponseJsonMatchesJsonPath('$[0].currency');
        $response = json_decode($I->grabResponse(), true);

        $I->assertEquals($expectedBalance, $response[0]['balance']);
    }
}

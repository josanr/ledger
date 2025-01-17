A Test Ledger Built on Symfony
=====================

### Architecture

This project uses an event-sourcing mechanism. All transactions are stored as events in the ledger, making balance calculation simple and efficient.
Although this design is straightforward, it offers an important advantage: the elimination of the need for database locks.

As a potential future feature, we could introduce a background job to create periodic checkpoints with pre-calculated balances.
This way, to calculate the balance, it would only be necessary to start from the most recent checkpoint and sum the transactions recorded after it.

### Installation

Clone the project repository from GitHub:
```shell
git clone git@github.com:josanr/ledger.git
```

Build and start the application:
```shell
make start
```

### Api documentation
```text
/api/doc.json
```

### Example requests

#### Create ledger
```shell
curl --location 'https://localhost/ledgers \
--header 'Content-Type: application/json' \
--data '{
    "code": "MAIN2",
    "name": "Main Ledger",
    "description": "Primary ledger for all transactions",
    "ledgerType": "GENERAL",
    "currency": "USD"
}'
```

#### Get ledgers
```shell
curl --location 'https://localhost/ledgers'
```

#### Create transaction
```shell
curl --location 'https://localhost/transactions \
--header 'Content-Type: application/json' \
--data '{
    "externalId": "01946a9d-d57e-753a-a122-4651754a20d9",
    "ledgerId": <id of existing ledger>,
    "amount": 1000,
    "currency": "EUR",
    "direction": <"CREDIT" or "DEBIT">,
    "description": "Initial deposit",
    "transactionDate": "2025-01-15 14:49:49.000000"
}'
```

#### Get balances
```shell
curl --location 'https://localhost/balances/01946a93-e57c-7548-896c-05d4dd48cc1d'
```

### Running Tests

#### Unit Tests
```shell
make test_unit
```

#### API Tests
```shell
make test_api
```

### Deploying to Production

Use the following command to deploy the application to production:
```shell
SERVER_NAME=your-domain-name.example.com \
APP_SECRET=ChangeMe \
CADDY_MERCURE_JWT_SECRET=ChangeThisMercureHubJWTSecretKey \
docker compose -f compose.yaml -f compose.prod.yaml up -d --wait
```

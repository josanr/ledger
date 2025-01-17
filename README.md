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

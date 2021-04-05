# Rate app

An API to provide crypto-currency rates.

This implementation uses BitPay as a rate provider and currently fetches all available conversion rates for BTC and ETH from BitPay.

Inverse rates, such as USD -> BTC are also available.

Currently, the API is able to provide only hourly rates. 

## Requirements

1. Docker Compose
   
2. Free 8080 port

## Installation

**All the steps below assume that the current working directory is the root directory of this project.**

Initial run

`docker-compose up -d`

At the initial run the database will have no rate entries, so 2 options are available:

1. Wait for the app to fetch the rates (happens each hour at 0 minutes, see `app/docker/cron/conf/crontab` )

2. Fetch them manually:

    `docker-compose exec app-cron php /var/www/app/bin/console rate-app:update-rates BTC`

    `docker-compose exec app-cron php /var/www/app/bin/console rate-app:update-rates ETH`

## Usage

See Swagger api doc at http://localhost:8080

## Quality tools

### Tests

`docker-compose exec app /var/www/app/bin/phpunit`

* Note that currently only 2 tests exist, 1 for each test type - integration and unit. They mainly serve as an example
and reference for future coverage.

### PHPStan report

`docker-compose exec app /var/www/app/vendor/bin/phpstan`

### PHP CS report

`docker-compose exec app /var/www/app/vendor/bin/php-cs-fixer fix --dry-run`

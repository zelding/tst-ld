# Readme

Architecture template: [symfony-docker](https://github.com/dunglas/symfony-docker) 

## Install
- clone this repo

### PHPStorm
- run the "Loudly-demo" Configuration

### Manual
- run `docker compose build --no-cache --pull`
- run `docker compose up -d`

## seed
- run `php bin/console doctrine:fixtures:load`

> Users will be named `test_user_\d`  
with password/apiKey `asdasd`  
with a few accepted and more pending requests

## Run tests

### PHPStorm
- run the "Tests" Configuration

### Manual
- run `php vendor/bin/phpunit`

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/09ee35b43cd84d33a1964b7fc26f87fb)](https://www.codacy.com/gh/MerciMathieu/bilemoAPI/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=MerciMathieu/bilemoAPI&amp;utm_campaign=Badge_Grade)
[![Maintainability](https://api.codeclimate.com/v1/badges/90ab76e42d63404d52c6/maintainability)](https://codeclimate.com/github/MerciMathieu/bilemoAPI/maintainability)

# bilemoAPI
Full securized API made with Symfony 5.
Created for Bilemo company to purpose mobile phones catalog

## Requirements
*   composer
*   php 7.2 or higher
*   docker-compose CLI
*   symfony (tool to use with symfony5; replaces "php bin/console" command

## Install

### Git clone
    git clone https://github.com/MerciMathieu/bilemoAPI.git

### Install dependencies
    composer install

### create database container

    docker-compose create  

### Create Database

    symfony console doctrine:database:create

### Create tables
    symfony console doctrine:migrations:migrate

### Generate authentication certificates

    php bin/console lexik:jwt:generate-keypair --skip-if-exists

add a passphrase, then add it in .env file

    JWT_PASSPHRASE= <enter passphrase here>

## Usage

### Create your admin account

Enter your admin informations

    src/DataFixtures/AppFixtures.php

### Load data

    symfony console doctrine:fixtures:load

## Start using the API

### Start symfony web server

    symfony serve -d

### Start database container

    docker-compose start

### Stop database container

    docker-compose stop

## Documentation

https://127.0.0.1:8000/api/doc



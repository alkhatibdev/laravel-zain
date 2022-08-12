<p align="center"><img src="/socialcard.png" alt="Social Card of Laravel Zain Package"></p>

# Zain DSP API integration with Laravel

[![Latest Version](https://img.shields.io/github/release/alkhatibdev/laravel-zain.svg?style=flat-square)](https://github.com/alkhatibdev/laravel-zain/releases)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

# Introduction
Laravel Zain is Zain DSP API integration with Laravel, made to simplify the the process and API calls and let developers focus on other integration parts and logic. See also [Laravel Sudani](https://github.com/alkhatibdev/laravel-sudani).

# Installation

## Requirements

- PHP >= `7.4.x`
- Laravel >= `7.x`

## install via composer

```shell
composer require alkhatibdev/laravel-zain
```

## Publish Configs 
```shell
php artisan vendor:publish --tag=laravel-zain-config
```
A `laravel-zain.php` config file will be published on your `configs` directory, with the following content:

```php
<?php

return [

    'base_url' => env('ZAIN_SERVER_BASE_API_URL'),

    'product_code' => env('ZAIN_PRODUCT_CODE'),

    'username' => env('ZAIN_USERNAME'),

    'password' => env('ZAIN_PASSWORD'),

    'remember_token' => env('ZAIN_REMEMBER_TOKEN', false),

    'enable_logging' => false,
];

```
Don't forget to set all these variable on your `.env` file

```env
ZAIN_SERVER_BASE_API_URL=https://test.zaindsp.com:3030/api/v1/json/
ZAIN_PRODUCT_CODE=xxxxxx
ZAIN_USERNAME=xxxxx
ZAIN_PASSWORD=xxxxx
ZAIN_REMEMBER_TOKEN=false
```


# Usage

## Initial Payment/Subscription

```php
use AlkhatibDev\LaravelZain\Facades\Zain;

// Initiate payment request
$response = Zain::initiate($phone)

```

When `initiate` payment request successfully sent, a SMS with `OTP` code will be send to the `$phone` number, and `$response` will contain a `subscribe_request_id` and you should save it to the next step `verify`.

## Verify Payment/Subscription

```php

$response = Zain::verify($otp, $requestId)

```

## Check Subscription

```php

$response = Zain::checkSubscription($phone)

```

## Unsubscribe

```php

$response = Zain::unsubscribe($phone)

// cacheToken($response['token'])

```

## Login and Cache DSP token


Out of the box the package will login automatically and get the `token` and use it for each action `initiate`, `verify` ..etc per request.

If you want cache the token and use it for furthor requests of whole day, you request `token` like this:

```php
$token = Zain::token()
```
And you can cache it and use for next requests for the next 24 hours.
If you set `ZAIN_REMEMBER_TOKEN=true` token will be expired after 30 days.

Example of using cached token:

```php
// $token = getCachedToken()

$response = Zain::withToken($token)->initiate($phone)
$response = Zain::withToken($token)->verify($phone)
...
```

## Logging
You can enable logging from package config file 
```
'enable_logging' => true,
```

# Other Packages
-   ### [Laravel Sudani](https://github.com/alkhatibdev/laravel-sudani) SPay API Integration

# License

Laravel Zain is open-sourced software licensed under the [MIT license](LICENSE).

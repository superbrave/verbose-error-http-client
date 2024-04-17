# Verbose error HTTP client

[![Latest version on Packagist][ico-version]][link-version]
[![Software License][ico-license]][link-license]

Increased verbosity of error messages in the Symfony HTTP client.

## Installation using Composer
Run the following command to add the package to the composer.json of your project:

``` bash
$ composer require superbrave/verbose-error-http-client symfony/http-client
```

The `symfony/http-client` can be replaced with any other HTTP client implementing the Symfony HTTP client contracts.

## Usage
The following example shows how to create the instances required execute requests with verbose exception messages:
```php
<?php

use Superbrave\VerboseErrorHttpClient\VerboseErrorHttpClient;
use Symfony\Component\HttpClient\HttpClient;

$httpClient = HttpClient::create();
$verboseErrorHttpClient = new VerboseErrorHttpClient($httpClient);

$response = $verboseErrorHttpClient->request('GET', 'https://superbrave.nl/api');
```

## License
The Verbose error HTTP client is licensed under the MIT License. Please see the [LICENSE file][link-license] for details.

[ico-version]: https://img.shields.io/packagist/v/superbrave/verbose-error-http-client
[ico-license]: https://img.shields.io/packagist/l/superbrave/verbose-error-http-client

[link-version]: https://packagist.org/packages/superbrave/verbose-error-http-client
[link-license]: LICENSE

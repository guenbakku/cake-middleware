# CakePHP Middleware Collection

Collection of CakePHP Middlewares.

## Requirement

* PHP 7.0 or higher
* CakePHP 3.5 or higher

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require guenbakku/cake-middleware
```

## List of middlewares

### Http\ClientTimezoneMiddleware

Determine timezone of client. This will check value with the following priority:
   1. query parameter (default: `timezone`)
   2. request header (default: `X-Timezone`)

Support timezone:  
https://www.php.net/manual/en/timezones.php

```php
<?php

// In Application.php

use Guenbakku\Middleware\Http\ClientTimezoneMiddleware;

public function middleware($middlewareQueue)
{
    $middlewareQueue
        ->add(new ClientTimezoneMiddleware());
        // Other middlewares...

    return $middlewareQueue;
}
```

```php
<?php

// In other place in source code

use Guenbakku\Middleware\Http\ClientTimezoneMiddleware;

$clientTimezone = ClientTimezoneMiddleware::getClientTimezone();
```

### Http\CorsMiddleware

Add `Access-Control-Allow-Origin` and other related headers to resources on an API, make that API can be called via cross origin.

NOTE: `CorsMiddleware` should be inserted into the first position of Middleware queue (before `Cake\Error\Middleware\ErrorHandlerMiddleware`) to make it still work correctly in case there is exception thrown from inside of your application.

```php

<?php

// In Application.php

use Guenbakku\Middleware\Http\CorsMiddleware;

public function middleware($middlewareQueue)
{
    // Use with default settings
    $middlewareQueue
        ->add(new CorsMiddleware());
        // Other middlewares...

    // Use with customize settings
    $middlewareQueue
        ->add(new CorsMiddleware([
            'allowOrigin' => ['*.domain.com'],
            'allowMethods' => ['GET', 'POST'],
            'allowHeaders' => ['*'],
            'allowCredentials' => true,
            'exposeHeaders' => ['Link'],
            'maxAge' => 300,
        ]));
        // Other middlewares...

    return $middlewareQueue;
}
```

### Http\LocaleSelectorMiddleware

Sets the runtime default locale for the request based on the Accept-Language header. The default will only be set if it matches the list of passed valid locales.

This is a customized middleware of `\Cake\I18n\Middleware\LocaleSelectorMiddleware` but different with the original middleware, this only gets the first 2 characters in locale code (ISO 639-1 language code, e.g: `en`, `ja`...).

```php
<?php

// In Application.php

use Guenbakku\Middleware\Http\LocaleSelectorMiddleware;

public function middleware($middlewareQueue)
{
    // Accept all locales
    $middlewareQueue
        ->add(new LocaleSelectorMiddleware());
        // Other middlewares...

    // Or specific allowed locales
    $middlewareQueue
        ->add(new LocaleSelectorMiddleware(['en', 'ja']));
        // Other middlewares...

    return $middlewareQueue;
}

```

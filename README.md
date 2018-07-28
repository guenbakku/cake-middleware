# CakePHP Middleware Collection

Collection of CakePHP Middlewares.

## Requirement

* PHP 7.0 or higher
* CakePHP 3.5 or higher

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require guenbakku/cakephp-middleware
```

## List of middlewares

### Http\CorsMiddleware

Add `Access-Control-Allow-Origin` and other related headers to resources on an API, make that API can be called via cross origin.

Usage:

~~~php

<?php

// In Application.php

use Guenbakku\Middleware\Http\CorsMiddleware

public function middleware($middlewareQueue)
{
    // Use with default settings
    $middlewareQueue
        ->add(new CorsMiddleware());

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

    return $middlewareQueue;
}
~~~

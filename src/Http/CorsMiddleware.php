<?php

namespace Guenbakku\Middleware\Http;

use Cake\Core\InstanceConfigTrait;

class CorsMiddleware
{

    use InstanceConfigTrait;

    protected $_defaultConfig = [
        'allowOrigin' => ['*'],
        'allowMethods' => ['*'],
        'allowHeaders' => ['*'],
        'allowCredentials' => true,
        'exposeHeaders' => ['Link'],
        'maxAge' => 300,
    ];

    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    public function __invoke($request, $response, $next)
    {
        // Terminate runner immediately if request method is `OPTIONS`
        // otherwise delegate request/response to next middleware.
        if ($request->getMethod() == 'OPTIONS') {
            $cors = $response->cors($request)
                ->allowOrigin($this->getConfig('allowOrigin'))
                ->allowMethods($this->getConfig('allowMethods'))
                ->allowHeaders($this->getConfig('allowHeaders'))
                ->exposeHeaders($this->getConfig('exposeHeaders'))
                ->maxAge($this->getConfig('maxAge'));

            if ($this->getConfig('allowCredentials')) {
                $cors->allowCredentials();
            }

            $response = $cors->build();
        } else {
            $response = $next($request, $response);
            $response = $response->cors($request)
                ->allowOrigin($this->getConfig('allowOrigin'))
                ->build();
        }

        return $response;
    }
}

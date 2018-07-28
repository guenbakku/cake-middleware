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
        $cors = $response->cors($request)
            ->allowOrigin($this->getConfig('allowOrigin'));

        if ($request->getMethod() == 'OPTIONS') {
            $cors = $cors
                ->allowMethods($this->getConfig('allowMethods'))
                ->allowHeaders($this->getConfig('allowHeaders'))
                ->exposeHeaders($this->getConfig('exposeHeaders'))
                ->maxAge($this->getConfig('maxAge'));

            if ($this->getConfig('allowCredentials')) {
                $cors->allowCredentials();
            }

            $response = $cors->build();
            return $response;
        }

        $response = $cors->build();
        return $next($request, $response);
    }
}

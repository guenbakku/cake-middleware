<?php

namespace Guenbakku\Middleware\Http;

use Cake\Core\InstanceConfigTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CorsMiddleware
{

    use InstanceConfigTrait;

    protected $_defaultConfig = [
        'allowOrigin' => ['*'],
        'allowMethods' => null,
        'allowHeaders' => null,
        'allowCredentials' => true,
        'exposeHeaders' => ['Link'],
        'maxAge' => 300,
    ];

    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Handle invoking middleware
     *
     * @param \Psr\Http\Message\ResponseInterface $request The request.
     * @param \Psr\Http\Message\ResponseInterface $response The response.
     * @param callable $next The next middleware to call.
     */
    public function __invoke(ResponseInterface $request, ServerRequestInterface $response, $next)
    {
        // Terminate runner immediately if request method is `OPTIONS`
        // otherwise delegate request/response to next middleware.
        if ($request->getMethod() == 'OPTIONS') {
            $this->_setDefaultCorsBuidlerOptions($request);
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

            // Redirect response does not contain method `cors`
            // so we need to check if method `cors` exists before using it.
            if (method_exists($response, 'cors')) {
                $response = $response->cors($request)
                    ->allowOrigin($this->getConfig('allowOrigin'))
                    ->build();
            }
        }

        return $response;
    }

    /**
     * Set default value to some options of CorsBuilder.
     * This will use value of headers of OPTIONS request as
     * default value of related headers of response.
     *
     * @param \Psr\Http\Message\ResponseInterface $request The request.
     */
    protected function _setDefaultCorsBuidlerOptions(ServerRequestInterface $request)
    {
        if ($this->getConfig('allowHeaders') === null) {
            $this->setConfig(
                'allowHeaders',
                explode(',', $request->getHeaderLine('Access-Control-Request-Headers'))
            );
        }

        if ($this->getConfig('allowMethods') === null) {
            $this->setConfig(
                'allowMethods',
                explode(',', $request->getHeaderLine('Access-Control-Request-Method'))
            );
        }
    }
}

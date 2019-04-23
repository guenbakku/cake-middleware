<?php
/**
 * Customized middleware of \Cake\I18n\Middleware\LocaleSelectorMiddleware
 * Different with the original middleware, this only gets the first 2
 * characters in locale code.
 */
namespace Guenbakku\Middleware\Http;

use Cake\I18n\I18n;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Sets the runtime default locale for the request based on the
 * Accept-Language header. The default will only be set if it
 * matches the list of passed valid locales.
 */
class LocaleSelectorMiddleware
{
    /**
     * List of valid locales for the request
     *
     * @var array
     */
    protected $allowedLocales = [];

    /**
     * Constructor.
     *
     * @param array $locales A list of accepted locales, or array ['*'] to accept any
     *   locale header value.
     */
    public function __construct(array $allowedLocales = ['*'])
    {
        $this->allowedLocales = $allowedLocales;
    }

    /**
     * Handle invoking middleware
     *
     * @param \Psr\Http\Message\ResponseInterface $request The request.
     * @param \Psr\Http\Message\ResponseInterface $response The response.
     * @param callable $next The next middleware to call.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $locale = \Locale::acceptFromHttp($request->getHeaderLine('Accept-Language'));
        if (!$locale) {
            return $next($request, $response);
        }

        // We only care ISO 639-1 language code (e.g: ja, en...)
        $locale = preg_replace('/([a-z]+)\_.*/', '$1', $locale);

        if (in_array($locale, $this->allowedLocales) || $this->allowedLocales === ['*']) {
            I18n::setLocale($locale);
        }

        return $next($request, $response);
    }
}

<?php

namespace App\Middleware;

use Cake\Core\InstanceConfigTrait;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\I18n\Time;
use Cake\Http\Exception\BadRequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Determine timezone of client.
 * This will check value with the following priority
 *     1. query parameter (default: timezone)
 *     2. header (default: X-Timezone)
 *
 * If there is not any valid value can be found,
 * this will set config `App.defaultTimezone` as client timezone.
 *
 * Support timezone: https://www.php.net/manual/en/timezones.php
 */
class ClientTimezoneMiddleware
{

    use InstanceConfigTrait;

    protected $_defaultConfig = [
        // Key in query parameter
        'query' => 'timezone',

        // Key in HTTP header
        'header' => 'X-Timezone',
    ];

    protected static $clientTimezone = null;

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
     * @throws \Cake\Http\Exception\BadRequestException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $timezone = $this->determine($request);
        try {
            new \DateTimeZone($timezone);
        } catch (\Exception $e) {
            throw new BadRequestException("Unknown or bad timezone: `$timezone`");
        }

        static::$clientTimezone = $timezone;
        return $next($request, $response);
    }

    /**
     * Determine timezone of client
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return String
     */
    protected function determine(ServerRequestInterface $request)
    {
        $queryStr = $request->getUri()->getQuery();
        parse_str($queryStr, $query);
        $timezone = Hash::get($query, $this->getConfig('query'));
        if (!empty($timezone)) {
            return $timezone;
        }

        $timezone = $request->getHeaderLine($this->getConfig('header'));
        if (!empty($timezone)) {
            return $timezone;
        }

        return Configure::read('App.defaultTimezone');
    }

    /**
     * Static getter to get client timezone
     *
     * @param Void
     * @return String
     */
    public static function getClientTimezone()
    {
        if (!static::$clientTimezone) {
            throw new \LogicException(__class__ . ' has not been configured correctly.');
        }
        return static::$clientTimezone;
    }
}

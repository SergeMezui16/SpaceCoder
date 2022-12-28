<?php
namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Utilities functions for request
 */
class RequestUtil
{

    public static function pageHasBeenRefreched(Request $request, CacheInterface $cache): bool
    {
        // return isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
        dd($request->headers, $cache, $_SERVER);
    }
}
<?php
namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Utilities functions for request
 * 
 * Many method helpfull for managing Requests
 */
class RequestUtil
{

    /**
     * Verify if the page has been refreshed
     * 
     * @deprecated 
     * @todo make a real body
     *
     * @param Request $request
     * @param CacheInterface $cache
     * @return boolean
     */
    public static function pageHasBeenRefreched(Request $request, CacheInterface $cache): bool
    {
        // return isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
        dd($request->headers, $cache, $_SERVER);
    }
}
<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this vinylSourceStream code.
 */

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) || php_sapi_name() === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

$loaderPathInfo = function () {
    if (false === $realPath = realpath(__DIR__.'/../app/autoload.php')) {
        throw new \RuntimeException('Could not locate auto loader. Use `composer install` to generate one.');
    }

    return $realPath;
};

require_once $loaderPathInfo();

Debug::enable();

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();

$response = $kernel->handle($request = Request::createFromGlobals());
$response->send();

$kernel->terminate($request, $response);

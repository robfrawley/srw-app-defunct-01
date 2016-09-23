<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;

$loaderPathInfo = function () {
    if (false === $realPath = realpath(__DIR__.'/../app/autoload.php')) {
        throw new \RuntimeException('Could not locate auto loader. Use `composer install` to generate one.');
    }

    return $realPath;
};

$bootstrapPathInfo = function () {
    if (false === $realPath = realpath(__DIR__.'/../var/bootstrap.php.cache')) {
        throw new \RuntimeException('Could not locate bootstrap cache. Cache warming should generate this.');
    }

    return $realPath;
};

require_once $loaderPathInfo();
require_once $bootstrapPathInfo();

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
$kernel = new AppCache($kernel);

$response = $kernel->handle($request = Request::createFromGlobals());
$response->send();

$kernel->terminate($request, $response);

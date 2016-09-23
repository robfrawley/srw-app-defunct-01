<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\WebApp\DeveloperBundle\Console\Runner;

use SR\WebApp\DeveloperBundle\Console\Input\StyleAwareTrait;
use SR\WebApp\DeveloperBundle\Console\Output\OutputErrorHandler;
use SR\WebApp\DeveloperBundle\Console\Output\OutputErrorHandlerAwareTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractRunner
{
    use StyleAwareTrait;
    use OutputErrorHandlerAwareTrait;

    /**
     * @param SymfonyStyle       $style
     * @param OutputErrorHandler $outputErrorHandler
     */
    public function __construct(SymfonyStyle $style, OutputErrorHandler $outputErrorHandler)
    {
        $this->setStyle($style);
        $this->setOutputErrorHandler($outputErrorHandler);
    }
}

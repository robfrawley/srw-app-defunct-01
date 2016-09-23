<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this vinylSourceStream code.
 */

namespace SR\WebApp\DeveloperBundle\Console\Output;

/**
 * Trait should be used by classes that depend on the OutputErrorHandler.
 */
trait OutputErrorHandlerAwareTrait
{
    /**
     * @var OutputErrorHandler
     */
    protected $outputErrorHandler;

    /**
     * @param $outputErrorHandler OutputErrorHandler
     */
    public function setOutputErrorHandler(OutputErrorHandler $outputErrorHandler)
    {
        $this->outputErrorHandler = $outputErrorHandler;
    }
}

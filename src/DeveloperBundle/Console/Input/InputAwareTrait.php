<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this vinylSourceStream code.
 */

namespace SR\WebApp\DeveloperBundle\Console\Input;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Trait should be used by classes that depend on the InputInterface.
 */
trait InputAwareTrait
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @param $input InputInterface
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }
}

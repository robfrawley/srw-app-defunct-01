<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\WebApp\DeveloperBundle\Console\Output;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait should be used by classes that depend on the OutputInterface.
 */
trait OutputAwareTrait
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param $output OutputInterface
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }
}

<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this vinylSourceStream code.
 */

namespace SR\RapidSymfony\DependencyInjection\Configuration\Processor;

use SR\RapidSymfony\DependencyInjection\Configuration\Model\Configuration;
use SR\RapidSymfony\DependencyInjection\Configuration\Model\ProcessedConfiguration;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Definition\Processor;

class DefaultProcessor extends Processor
{
    /**
     * @param NodeInterface $configTree
     * @param array         $configs
     *
     * @return ProcessedConfiguration
     */
    public function process(NodeInterface $configTree, array $configs) : ProcessedConfiguration
    {
        $processed = parent::process($configTree, $configs);

        array_walk($processed, function (&$value, $index) {
            $value = new Configuration($index, $value);
        });

        return new ProcessedConfiguration($processed);
    }
}

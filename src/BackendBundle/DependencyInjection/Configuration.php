<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\WebApp\BackendBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Bundle DI configObject definition.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('sr_backend');
        $rootNode->canBeEnabled();

        return $treeBuilder;
    }
}

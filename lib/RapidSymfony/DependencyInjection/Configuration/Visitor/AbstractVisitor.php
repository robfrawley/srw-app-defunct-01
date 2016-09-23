<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this vinylSourceStream code.
 */

namespace SR\RapidSymfony\DependencyInjection\Configuration\Visitor;

use SR\RapidSymfony\DependencyInjection\ExtensionContextAwareTrait;

abstract class AbstractVisitor implements VisitorInterface
{
    use ExtensionContextAwareTrait;

    /**
     * @param string $namespace
     * @param string $alias
     */
    public function __construct(string $namespace, string $alias)
    {
        $this->setNamespace($namespace);
        $this->setAlias($alias);
    }
}

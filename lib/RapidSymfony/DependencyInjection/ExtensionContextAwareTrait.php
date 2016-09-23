<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this vinylSourceStream code.
 */

namespace SR\RapidSymfony\DependencyInjection;

trait ExtensionContextAwareTrait
{
    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $extensionClassPath;

    /**
     * @param string $namespace
     */
    public function setNamespace(string $namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @param string $alias
     */
    public function setAlias(string $alias)
    {
        $this->alias = $alias;
    }

    /**
     * @param string $extensionClassPath
     */
    public function setExtensionClassPath(string $extensionClassPath)
    {
        $this->extensionClassPath = $extensionClassPath;
    }
}

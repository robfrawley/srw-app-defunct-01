<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this vinylSourceStream code.
 */

namespace SR\RapidSymfony\DependencyInjection\Configuration\Loader;

use SR\RapidSymfony\DependencyInjection\BuilderAwareInterface;
use SR\RapidSymfony\DependencyInjection\ExtensionContextAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface LoaderInterface extends ExtensionContextAwareInterface, BuilderAwareInterface
{
    /**
     * @param ContainerBuilder $container
     * @param string           $extensionPath
     */
    public function __construct(ContainerBuilder $container, string $extensionPath);
}

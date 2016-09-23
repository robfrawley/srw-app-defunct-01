<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\RapidSymfony\DependencyInjection\Configuration\Loader;

use SR\Exception\InvalidArgumentException;
use SR\RapidSymfony\DependencyInjection\BuilderAwareTrait;
use SR\RapidSymfony\DependencyInjection\ExtensionContextAwareTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ExtensionResourceLoader implements LoaderInterface
{
    use ExtensionContextAwareTrait;
    use BuilderAwareTrait;

    /**
     * @param ContainerBuilder $builder
     * @param string           $extensionPath
     */
    public function __construct(ContainerBuilder $builder, string $extensionPath)
    {
        $this->setBuilder($builder);
        $this->setExtensionClassPath($extensionPath);
    }

    public function load(array $resources)
    {
        foreach ($resources as $name) {
            $this->loadResource($name);
        }
    }

    private function loadResource(string $name)
    {
        $loader = new YamlFileLoader($this->builder, new FileLocator($this->getResourcePath()));
        $loader->load($name);
    }

    /**
     * @return string
     */
    private function getResourcePath() : string
    {
        $path = sprintf('%s/../Resources/config', dirname($this->extensionClassPath));

        if (false === $realPath = realpath($path)) {
            throw new InvalidArgumentException('Could not resource extension resouces path %s', $path);
        }

        return $realPath;
    }
}

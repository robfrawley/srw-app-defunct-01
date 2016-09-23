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

use SR\Exception\InvalidArgumentException;
use SR\RapidSymfony\DependencyInjection\Configuration\Loader\ExtensionResourceLoader;
use SR\RapidSymfony\DependencyInjection\Configuration\Model\ProcessedConfiguration;
use SR\RapidSymfony\DependencyInjection\Configuration\Processor\DefaultProcessor;
use SR\RapidSymfony\DependencyInjection\Configuration\Visitor\BuilderAwareVisitorInterface;
use SR\RapidSymfony\DependencyInjection\Configuration\Visitor\ParameterAssignerVisitor;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as BaseExtension;

abstract class AbstractExtension extends BaseExtension
{
    /**
     * @return ConfigurationInterface
     */
    abstract protected function getConfigurationTreeBuilder();

    /**
     * @return string[]
     */
    protected function getResources()
    {
        return [
            'services.yml',
        ];
    }

    /**
     * @return Processor
     */
    protected function getConfigurationProcessor()
    {
        return new DefaultProcessor();
    }

    /**
     * @return string[]
     */
    protected function getConfigurationVisitors()
    {
        return [
            ParameterAssignerVisitor::class,
        ];
    }

    /**
     * @param ConfigurationInterface $config
     * @param array[]                $configTree
     *
     * @return ProcessedConfiguration
     */
    protected function loadConfiguration(array $configTree, ConfigurationInterface $config)
    {
        return $this->getConfigurationProcessor()->processConfiguration($config, $configTree);
    }

    /**
     * @param array            $configTree
     * @param ContainerBuilder $container
     */
    final public function load(array $configTree, ContainerBuilder $container)
    {
        $config = $this->loadConfiguration($configTree, $this->getConfigurationTreeBuilder());

        if ($this->isProcessedConfigEnabled($container, $config)) {
            $this->visitConfiguration($config, $container);
            $this->loadResources($container);
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    final private function loadResources(ContainerBuilder $container)
    {
        $reflector = new \ReflectionObject($this);
        $loader = new ExtensionResourceLoader($container, $reflector->getFileName());
        $loader->load($this->getResources());
    }

    /**
     * @param ProcessedConfiguration $config
     * @param ContainerBuilder       $container
     *
     * @return ProcessedConfiguration
     */
    final private function visitConfiguration(ProcessedConfiguration $config, ContainerBuilder $container) : ProcessedConfiguration
    {
        foreach ($this->getConfigurationVisitors() as $visitorName) {
            $this->visitProcessedConfiguration($config, $container, $visitorName);
        }

        return $config;
    }

    /**
     * @param ProcessedConfiguration $config
     * @param ContainerBuilder       $container
     * @param string                 $visitorName
     */
    final private function visitProcessedConfiguration(ProcessedConfiguration $config, ContainerBuilder $container, string $visitorName)
    {
        $visitor = new $visitorName($this->getNamespace(), $this->getAlias());

        if ($visitor instanceof BuilderAwareVisitorInterface) {
            $visitor->setBuilder($container);
        }

        $visitor->visit($config);
    }

    /**
     * @param ContainerBuilder       $container
     * @param ProcessedConfiguration $config
     *
     * @return bool
     */
    final private function isProcessedConfigEnabled(ContainerBuilder $container, ProcessedConfiguration $config)
    {
        try {
            $state = $config->findMatchingIndex('enabled');

            return (bool) $container->getParameterBag()->resolveValue($state->getValue());
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }
}

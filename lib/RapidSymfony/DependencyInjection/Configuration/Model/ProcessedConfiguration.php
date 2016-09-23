<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this vinylSourceStream code.
 */

namespace SR\RapidSymfony\DependencyInjection\Configuration\Model;

use SR\Exception\InvalidArgumentException;

class ProcessedConfiguration implements \IteratorAggregate
{
    /**
     * @var Configuration[]
     */
    private $configs;

    /**
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        $this->configs = array_filter($configs, function ($c) {
            return $c instanceof Configuration;
        });
    }

    /**
     * @return array|Configuration[]
     */
    public function __toArray() : array
    {
        return $this->configs;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->__toArray());
    }

    /**
     * @param Configuration $config
     *
     * @return ProcessedConfiguration
     */
    public function addConfig(Configuration $config) : ProcessedConfiguration
    {
        $this->configs[] = $config;

        return $this;
    }

    /**
     * @param string $index
     *
     * @return Configuration
     */
    public function findMatchingIndex(string $index) : Configuration
    {
        $matches = array_filter($this->configs, function (Configuration $c) use ($index) {
            return $index === $c->getIndex();
        });

        if (count($matches) === 0) {
            throw new InvalidArgumentException('Config with index "%s" does not exist', $index);
        }

        if (count($matches) > 1) {
            throw new InvalidArgumentException('Config with index "%s" matched more than once', $index);
        }

        return array_pop($matches);
    }

    /**
     * @param string $value
     *
     * @return Configuration
     */
    public function findMatchingValue(string $value) : Configuration
    {
        $matches = array_filter($this->configs, function (Configuration $c) use ($value) {
            return $value === $c->getValue();
        });

        if (count($matches) === 0) {
            throw new InvalidArgumentException('Config with value "%s" does not exist', var_export($value, true));
        }

        if (count($matches) > 1) {
            throw new InvalidArgumentException('Config with value "%s" matched more than once', var_export($value, true));
        }

        return $matches[0];
    }
}

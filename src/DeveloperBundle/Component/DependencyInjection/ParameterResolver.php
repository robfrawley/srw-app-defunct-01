<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\WebApp\DeveloperBundle\Component\DependencyInjection;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Resolves any parameters in a given value using a container instance.
 */
class ParameterResolver implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param mixed ...$values
     *
     * @return mixed[]
     */
    public function resolve(...$values)
    {
        return array_map(function ($v) {
            return $this->resolveValue($v);
        }, $values);
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function resolveValue($value)
    {
        if (null === $parameters = $this->findParameters($value)) {
            return $value;
        }

        return $this->replaceParameters($value, $this->resolveParameters($parameters));
    }

    /**
     * @param string $value
     *
     * @return null|array[]
     */
    private function findParameters($value)
    {
        if (false === preg_match_all('{%([^%]+)%}', $value, $matches, PREG_SET_ORDER)) {
            return null;
        }

        return $matches;
    }

    /**
     * @param array[] $parameters
     *
     * @throws InvalidArgumentException
     *
     * @return array[]
     */
    private function resolveParameters($parameters)
    {
        return array_map(function ($match) {
            list($match, $name) = $match;

            if (!$this->container->hasParameter($name)) {
                throw new InvalidArgumentException(sprintf('Container parameter "%%%s%%" does not exist!', $name));
            }

            return [
                $match,
                $this->container->getParameter($name),
            ];
        }, $parameters);
    }

    /**
     * @param mixed   $value
     * @param array[] $parameters
     *
     * @return mixed
     */
    private function replaceParameters($value, $parameters)
    {
        foreach ($parameters as $p) {
            list($search, $replace) = $p;

            $value = str_replace($search, $replace, $value);
        }

        return $value;
    }
}

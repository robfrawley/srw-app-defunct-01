<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\RapidSymfony\Repository;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use SR\Doctrine\Exception\OrmException;
use SR\Utility\ClassInspect;
use SR\Utility\StringTransform;

abstract class AbstractRepository extends EntityRepository
{
    /**
     * @var bool
     */
    const CACHE_ENABLED = true;

    /**
     * @var int
     */
    const DEFAULT_TTL = 300;

    /**
     * @param callable|null $config
     * @param string|null   $alias
     *
     * @return \Doctrine\ORM\Query
     */
    protected function getQuery(callable $config = null, $alias = null)
    {
        if ($alias === null) {
            $alias = $this->getAliasFromEntityName();
        }

        $builder = $this->createQueryBuilder($alias);

        if (is_callable($config)) {
            call_user_func_array($config, [$builder, $alias]);
        }

        return $builder->getQuery();
    }

    /**
     * @param callable|null $build
     * @param bool          $single
     * @param int|null      $ttl
     *
     * @return mixed
     */
    protected function getResult(callable $build = null, $single = false, $ttl = null)
    {
        $query = $this->getQuery($build);

        if (self::CACHE_ENABLED) {
            $index = $this->getCacheKey($query);
            $cache = $this->getCacheDriver();

            if ($cache->contains($index)) {
                return $cache->fetch($index);
            }
        }

        if ($single === true) {
            $result = $query->getSingleResult();
        } else {
            $result = $query->getResult();
        }

        if (isset($cache) && isset($index)) {
            $cache->save($index, $result, $ttl ?: self::DEFAULT_TTL);
        }

        return $result;
    }

    /**
     * @param Query $query
     *
     * @return string
     */
    private function getCacheKey(Query $query)
    {
        $k = sprintf('silver-papillon-%s-%s-', $this->getClassNameShort(), $query->getDQL());

        foreach ($query->getParameters() as $parameter) {
            $k .= $this->getCacheKeyPartFromParameter($parameter).'-';
        }

        return StringTransform::toAlphanumericAndDashes(strtolower(substr($k, 0, strlen($k) - 1)));
    }

    /**
     * @param Query\Parameter $parameter
     *
     * @throws OrmException
     *
     * @return string
     */
    private function getCacheKeyPartFromParameter(Query\Parameter $parameter)
    {
        $name = $parameter->getName();
        $value = $parameter->getValue();

        if (is_object($value) && !method_exists($value, '__toString')) {
            throw OrmException::create()
                ->setMessage('Could not convert parameter to string for doctrin result cache in %s')
                ->with($this->getClassName());
        }

        return $name.'='.(string) $value;
    }

    /**
     * @return ApcCache
     */
    private function getCacheDriver()
    {
        static $cacheDriver;

        if ($cacheDriver === null) {
            $cacheDriver = new ApcCache();
        }

        return $cacheDriver;
    }

    /**
     * @return string
     */
    private function getAliasFromEntityName()
    {
        return strtolower(substr($this->getClassNameShort(), 0, 1));
    }

    /**
     * @return string
     */
    private function getClassNameShort()
    {
        return ClassInspect::getNameShort($this->getClassName());
    }
}

/* EOF */

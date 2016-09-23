<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\RapidSymfony\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait BuilderAwareTrait
{
    /**
     * @var ContainerBuilder
     */
    protected $builder;

    /**
     * @param ContainerBuilder $builder
     *
     * @return $this
     */
    public function setBuilder(ContainerBuilder $builder)
    {
        $this->builder = $builder;

        return $this;
    }
}

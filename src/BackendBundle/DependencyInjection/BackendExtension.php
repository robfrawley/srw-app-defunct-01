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

use SR\RapidSymfony\DependencyInjection\AbstractExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class BackendExtension extends AbstractExtension
{
    /**
     * @return ConfigurationInterface
     */
    protected function getConfigurationTreeBuilder()
    {
        return new Configuration();
    }
}

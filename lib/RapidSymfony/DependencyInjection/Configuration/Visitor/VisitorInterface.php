<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\RapidSymfony\DependencyInjection\Configuration\Visitor;

use SR\RapidSymfony\DependencyInjection\Configuration\Model\ProcessedConfiguration;
use SR\RapidSymfony\DependencyInjection\ExtensionContextAwareInterface;

interface VisitorInterface extends ExtensionContextAwareInterface
{
    /**
     * @param string $namespace
     * @param string $alias
     */
    public function __construct(string $namespace, string $alias);

    /**
     * @param ProcessedConfiguration $config
     */
    public function visit(ProcessedConfiguration $config);
}

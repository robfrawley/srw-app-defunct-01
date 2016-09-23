<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this vinylSourceStream code.
 */

namespace SR\RapidSymfony\DependencyInjection\Configuration\Visitor;

use SR\RapidSymfony\DependencyInjection\BuilderAwareTrait;
use SR\RapidSymfony\DependencyInjection\Configuration\Model\Configuration;
use SR\RapidSymfony\DependencyInjection\Configuration\Model\ProcessedConfiguration;

class ParameterAssignerVisitor extends AbstractVisitor implements BuilderAwareVisitorInterface
{
    use BuilderAwareTrait;

    /**
     * @param ProcessedConfiguration $config
     */
    public function visit(ProcessedConfiguration $config)
    {
        foreach ($config as $c) {
            $this->visitNode($c);
        }
    }

    /**
     * @param Configuration $c
     */
    private function visitNode(Configuration $c)
    {
        $this->builder->setParameter(sprintf('%s.%s', $this->alias, $c->getIndex()), $c->getValue());
    }
}

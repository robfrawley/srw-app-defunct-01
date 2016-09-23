<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\WebApp\DeveloperBundle\Console\Output;

use SR\WebApp\DeveloperBundle\Console\Input\StyleAwareTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Facilitates error message reporting and exception throwing.
 */
class OutputErrorHandler
{
    use StyleAwareTrait;

    /**
     * @param SymfonyStyle $style
     */
    public function __construct(SymfonyStyle $style)
    {
        $this->setStyle($style);
    }

    /**
     * @param string $message
     * @param mixed  ...$replacements
     */
    public function raiseWarning($message, ...$replacements)
    {
        $this->style->block($this->buildMessage($message, ...$replacements), 'WARNING', 'fg=red;bg=white', ' ', true);
    }

    /**
     * @param string $message
     * @param mixed  ...$replacements
     */
    public function raiseError($message, ...$replacements)
    {
        $this->style->error($this->buildMessage($message, ...$replacements));
    }

    /**
     * @param string $message
     * @param mixed  ...$replacements
     *
     * @throws \RuntimeException
     */
    public function raiseCritical($message, ...$replacements)
    {
        $this->style->block($this->buildMessage($message, ...$replacements), 'CRITICAL', 'fg=white;bg=red', ' ', true);
        $this->outputHelp(true);

        throw new \RuntimeException('Fatal error encountered: '.$message);
    }

    /**
     * @param string $message
     * @param mixed  ...$replacements
     */
    public function raiseCriticalAndExitImmediately($message, ...$replacements)
    {
        $this->style->block($this->buildMessage($message, ...$replacements), 'CRITICAL', 'fg=white;bg=red', ' ', true);
        $this->outputHelp(true);

        exit(-1);
    }

    private function outputHelp($fatal = true)
    {
        $extra = $fatal ? 'Exiting due to prior fatal error...' : 'Attempting to continue despite prior error...';
        $this->style->comment(sprintf('Use "--help" to display command usage details. %s', $extra));
    }

    /**
     * @param string $message
     * @param mixed[] ...$replacements
     *
     * @return string
     */
    private function buildMessage($message, ...$replacements)
    {
        if (count($replacements) > 0) {
            return sprintf($message, ...$replacements);
        }

        return $message;
    }
}

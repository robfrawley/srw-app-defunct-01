<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\WebApp\DeveloperBundle\Command;

use SR\WebApp\DeveloperBundle\Component\DependencyInjection\ParameterResolver;
use SR\WebApp\DeveloperBundle\Console\Input\InputAwareTrait;
use SR\WebApp\DeveloperBundle\Console\Input\InputParameterResolver;
use SR\WebApp\DeveloperBundle\Console\Input\StyleAwareTrait;
use SR\WebApp\DeveloperBundle\Console\Output\OutputAwareTrait;
use SR\WebApp\DeveloperBundle\Console\Output\OutputErrorHandler;
use SR\WebApp\DeveloperBundle\Console\Output\OutputErrorHandlerAwareTrait;
use SR\WebApp\DeveloperBundle\Console\Runner\EnvironmentFileCleanRunner;
use SR\WebApp\DeveloperBundle\Console\Runner\EnvironmentFileInstallationRunner;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EnvironmentInstallFilesCommand extends ContainerAwareCommand
{
    use InputAwareTrait;
    use StyleAwareTrait;
    use OutputAwareTrait;
    use OutputErrorHandlerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('sr:env:install-files')
            ->setAliases(['env:install-files'])
            ->setDescription('Installs select environment dot files to repository root')
            ->addArgument('environment_name', InputArgument::OPTIONAL, 'Name of environment to use', 'default')
            ->addOption('repo-root', ['r'], InputOption::VALUE_REQUIRED, 'Repository root path', '%kernel.root_dir%/../')
            ->addOption('config-root', ['c'], InputOption::VALUE_REQUIRED, 'Environment config file root path', '%kernel.root_dir%/../.env/')
            ->addOption('leave-all', ['L'], InputOption::VALUE_NONE, 'Disables removal of all prior dot files')
            ->addOption('leave-file', ['l'], InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'One or more files to not remove', []);
    }

    /**
     * Execute command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setInput($input);
        $this->setOutput($output);
        $this->setStyle($style = new SymfonyStyle($input, $output));
        $this->setOutputErrorHandler($error = new OutputErrorHandler($style));

        try {
            $resolver = $this->getInputParameterResolver();
            $env = $resolver->getResolvedArgument('environment_name');
            $confPath = $resolver->getResolvedOption('config-root');
            $repoPath = $resolver->getResolvedOption('repo-root');
        } catch (\RuntimeException $e) {
            return -1;
        }

        $ignorePrior = $this->input->getOption('leave-all');
        $ignoreList = array_map(function ($file) {
            return basename($file);
        }, $this->input->getOption('leave-file'));

        if (false === $realRepoPath = realpath($repoPath)) {
            $this->outputErrorHandler->raiseCritical('Path does not exist %s', $repoPath);
        }

        if (false === $realConfPath = realpath($confPath)) {
            $this->outputErrorHandler->raiseCritical('Path does not exist %s', $confPath);
        }

        $repoPath = $realRepoPath;
        $confPath = $realConfPath;

        $this->writeConfiguration([
            ['environment', $env],
            ['repository', $repoPath],
            ['config root', $confPath],
            ['ignore files', implode(', ', $ignoreList) ?: 'null'],
            ['all ignored', $ignorePrior ? 'true' : 'false'],
        ]);

        if (false === $this->confirm('Continue using this configObject?', true)) {
            return $this->userRequestedExit();
        }

        if ($ignorePrior) {
            $this->style->note('Skipping removal of existing files');
        } else {
            $this
                ->getCleanerRunner()
                ->setRoot($repoPath)
                ->setIgnore($ignoreList)
                ->run();
        }

        $result = $this
            ->getInstallRunner()
            ->setEnvironment($env)
            ->setIgnore($ignoreList)
            ->setRootPaths($repoPath, $confPath)
            ->run();

        if ($result === 0) {
            $this->style->success('Completed');
        }

        return $result;
    }

    /**
     * Write configuration for use to see.
     *
     * @param  array[] $rows
     */
    private function writeConfiguration(array $rows)
    {
        if (!$this->style->isVerbose()) {
            return;
        }

        $rows = array_map(function ($r) {
            $r[0] = sprintf('<fg=blue;option=bold>%s</>', strtoupper($r[0]));

            return $r;
        }, $rows);

        $this->style->comment('resolved configObject');
        $this->style->table(['Index', 'Value'], $rows);
    }

    /**
     * @param string $message
     * @param bool   $verboseOnly
     *
     * @return bool
     */
    protected function confirm($message, $verboseOnly = false)
    {
        if (!$this->style->isVerbose() && $verboseOnly) {
            return true;
        }

        if (!$this->input->isInteractive()) {
            return true;
        }

        return (bool) $this->style->confirm($message);
    }

    /**
     * @return int
     */
    protected function userRequestedExit()
    {
        $this->style->comment('use requested script termination');

        return 0;
    }

    /**
     * @return EnvironmentFileCleanRunner
     */
    private function getCleanerRunner()
    {
        return new EnvironmentFileCleanRunner($this->style, $this->outputErrorHandler);
    }

    /**
     * @return EnvironmentFileInstallationRunner
     */
    private function getInstallRunner()
    {
        return new EnvironmentFileInstallationRunner($this->style, $this->outputErrorHandler);
    }

    /**
     * @return InputParameterResolver
     */
    private function getInputParameterResolver()
    {
        return new InputParameterResolver($this->input, $this->outputErrorHandler, $this->getParameterResolver());
    }

    /**
     * @return ParameterResolver
     */
    private function getParameterResolver()
    {
        return $this->getContainer()->get('sr.web_app.config.parameter_resolver');
    }
}

<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this vinylSourceStream code.
 */

namespace SR\WebApp\DeveloperBundle\Console\Runner;

class EnvironmentFileInstallationRunner extends AbstractRunner
{
    /**
     * @var string
     */
    private $environment;

    /**
     * @var string
     */
    private $pathConf;

    /**
     * @var string
     */
    private $pathRepo;

    /**
     * @var string[]
     */
    private $ignore;

    /**
     * @param string $env
     *
     * @return $this
     */
    public function setEnvironment($env)
    {
        $this->environment = $env;

        return $this;
    }

    /**
     * @param string[] $ignore
     *
     * @return $this
     */
    public function setIgnore($ignore)
    {
        $this->ignore = $ignore;

        return $this;
    }

    /**
     * @param string $pathRepo
     * @param string $pathConf
     *
     * @return $this
     */
    public function setRootPaths($pathRepo, $pathConf)
    {
        $this->pathRepo = $pathRepo;
        $this->pathConf = $pathConf;

        return $this;
    }

    /**
     * @return int
     */
    public function run()
    {
        $this->style->section(sprintf('Install %s env', $this->environment));

        $installed = $skipped = [];
        foreach ($this->scanRoot() as $file) {
            if (in_array(basename($file), $this->ignore)) {
                $skipped[] = $file;
                continue;
            }

            if (false === $removeFile = realpath($file)) {
                $this->style->note(sprintf('Encountered unknown error resolving %s', $file));
                continue;
            }

            $installTo = $this->pathRepo.DIRECTORY_SEPARATOR.'.'.basename($file);
            $installed[] = sprintf('%s -> %s', $file, $installTo);

            @copy($file, $installTo);
        }

        if (count($skipped) > 0) {
            $this->style->comment('skipped '.count($skipped).' existing files');
        }

        if ($this->style->isVerbose() && count($skipped) > 0) {
            $this->style->listing($skipped);
        }

        $this->style->comment('installed '.count($installed).' existing files');

        if ($this->style->isVerbose() && count($installed) > 0) {
            $this->style->listing($installed);
        }

        return 0;
    }

    /**
     * @return array
     */
    private function scanRoot()
    {
        $files = array_filter(scandir($this->getEnvPath()), function ($f) {
            return $f !== '.' && $f !== '..' && !is_dir($this->pathRepo.DIRECTORY_SEPARATOR.$f);
        });

        $files = array_map(function ($f) {
            return $this->getEnvPath().DIRECTORY_SEPARATOR.$f;
        }, $files);

        return array_values($files);
    }

    /**
     * @return string
     */
    private function getEnvPath()
    {
        if (false === $path = realpath($this->pathConf.DIRECTORY_SEPARATOR.$this->environment)) {
            $this->outputErrorHandler->raiseCritical('Environment %s does not exist in %s', $this->environment, $this->pathConf);
        }

        return $path;
    }
}

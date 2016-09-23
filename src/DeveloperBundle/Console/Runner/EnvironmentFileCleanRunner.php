<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\WebApp\DeveloperBundle\Console\Runner;

class EnvironmentFileCleanRunner extends AbstractRunner
{
    /**
     * @var string
     */
    private $pathRepo;

    /**
     * @var string[]
     */
    private $ignore;

    /**
     * @param string $pathRepo
     * @param string $pathConf
     *
     * @return $this
     */
    public function setRoot($pathRepo)
    {
        $this->pathRepo = $pathRepo;

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
     * @return int
     */
    public function run()
    {
        $this->style->section('Cleaning existing');

        $removed = $skipped = [];
        foreach ($this->scanRoot() as $file) {
            if (in_array(basename($file), $this->ignore)) {
                $skipped[] = $file;
                continue;
            }

            if (false === $removeFile = realpath($file)) {
                $this->style->note(sprintf('Encountered unknown error resolving %s', $file));
                continue;
            }

            if (0 !== strpos($removeFile, $this->pathRepo)) {
                $this->style->note(sprintf('Cannot remove %s as it resides outside the repo root', basename($file)));
                continue;
            }

            @unlink($file);
            $removed[] = $removeFile;
        }

        if (count($skipped) > 0) {
            $this->style->comment('skipped '.count($skipped).' existing files');
        }

        if ($this->style->isVerbose() && count($skipped) > 0) {
            $this->style->listing($skipped);
        }

        $this->style->comment('removed '.count($removed).' existing files');

        if ($this->style->isVerbose() && count($removed) > 0) {
            $this->style->listing($removed);
        }

        return 0;
    }

    /**
     * @return array
     */
    private function scanRoot()
    {
        $files = array_filter(scandir($this->pathRepo), function ($f) {
            return $f !== '.' && $f !== '..' && 0 === strpos($f, '.') && !is_dir($this->pathRepo.DIRECTORY_SEPARATOR.$f);
        });

        $files = array_map(function ($f) {
            return $this->pathRepo.DIRECTORY_SEPARATOR.$f;
        }, $files);

        return array_values($files);
    }
}

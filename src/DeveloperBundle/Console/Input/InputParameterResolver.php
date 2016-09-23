<?php

/*
 * This file is part of the `src-run/srw-app` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\WebApp\DeveloperBundle\Console\Input;

use SR\WebApp\DeveloperBundle\Component\DependencyInjection\ParameterResolver;
use SR\WebApp\DeveloperBundle\Console\Output\OutputErrorHandler;
use SR\WebApp\DeveloperBundle\Console\Output\OutputErrorHandlerAwareTrait;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputAwareInterface;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Resolves any parameters in a given value using a container instance.
 */
class InputParameterResolver implements InputAwareInterface
{
    use InputAwareTrait;
    use OutputErrorHandlerAwareTrait;

    /**
     * @var ParameterResolver
     */
    private $parameterResolver;

    /**
     * @param InputInterface     $input
     * @param OutputErrorHandler $outputErrorHandler
     */
    public function __construct(InputInterface $input, OutputErrorHandler $outputErrorHandler, ParameterResolver $parameterResolver)
    {
        $this->setInput($input);
        $this->setOutputErrorHandler($outputErrorHandler);

        $this->parameterResolver = $parameterResolver;
    }

    /**
     * @param string $name
     * @param bool   $required
     *
     * @return mixed
     */
    public function getResolvedArgument($name, $required = true)
    {
        return $this->getResolved('argument', $name, $required);
    }

    /**
     * @param string $name
     * @param bool   $required
     *
     * @return mixed
     */
    public function getResolvedOption($name, $required = true)
    {
        return $this->getResolved('option', $name, $required);
    }

    /**
     * @param string $type
     * @param string $name
     * @param bool   $required
     *
     * @return mixed|null
     */
    private function getResolved($type, $name, $required)
    {
        try {
            return $this->resolveParameters($this->getValue($type, $name));
        } catch (InvalidArgumentException $exception) {
            return $this->thrownToMessage($type, $name, $required, $exception);
        }
    }

    /**
     * @param string $type
     * @param string $name
     *
     * @return mixed
     */
    private function getValue($type, $name)
    {
        if ($type === 'option') {
            return $this->input->getOption($name);
        }

        return $this->input->getArgument($name);
    }

    /**
     * @param string     $type
     * @param string     $name
     * @param bool       $required
     * @param \Exception $exception
     *
     * @return mixed
     */
    private function thrownToMessage($type, $name, $required, \Exception $exception)
    {
        if ($message = $this->thrownInternalMessage($type, $name) !== $exception->getMessage()) {
            $message = $this->thrownExternalMessage($type, $name, $exception);
        }

        return $this->handleError($message, $required);
    }

    /**
     * @param string $type
     * @param string $name
     *
     * @return string[]
     */
    private function thrownInternalMessage($type, $name)
    {
        return [
            sprintf('Missing required %s "%s"', $type, $name),
        ];
    }

    /**
     * @param string     $type
     * @param string     $name
     * @param \Exception $exception
     *
     * @return string[]
     */
    private function thrownExternalMessage($type, $name, \Exception $exception)
    {
        try {
            $value = (string) $this->getValue($type, $name);
        } catch (\Exception $e) {
            $value = '<null-value>';
        }

        return [
            //sprintf('Command %s "%s=%s" is not resolvable.', $type, $name, $value),
            sprintf('%s (Invalid value for %s "%s")', $exception->getMessage(), $type, $name),
        ];
    }

    /**
     * @param string[] $message
     * @param bool     $fatal
     *
     * @return mixed
     */
    private function handleError(array $message, $fatal)
    {
        $raiseWarning = [$this->outputErrorHandler, 'raiseWarning'];
        $raiseError = [$this->outputErrorHandler, 'raiseError'];
        $raiseThrown = [$this->outputErrorHandler, 'raiseCritical'];
        $raiseFinal = $fatal === true ? $raiseThrown : $raiseError;

        foreach ((array) array_splice($message, 0, count($message) - 1) as $m) {
            call_user_func($raiseWarning, $m);
        }

        return call_user_func($raiseFinal, $message[count($message) - 1]);
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function resolveParameters($value)
    {
        return $this->parameterResolver->resolveValue($value);
    }
}

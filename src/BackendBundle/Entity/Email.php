<?php

/*
 * This file is part of the `src-run/srw-client-silverpapillon` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\WebApp\BackendBundle\Entity;

use WhiteOctober\SwiftMailerDBBundle\EmailInterface;

/**
 * Class Email.
 */
class Email implements EmailInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $message;

    /**
     * @var bool
     */
    private $sendDone;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var string[]
     */
    private $sentTo;

    /**
     * @var string[]
     */
    private $sentFrom;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var \DateTime
     */
    private $createdOn;

    public function __construct()
    {
        $this->sendDone = false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param $message string Serialized \Swift_Mime_Message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->assignExtraFields($message);
        $this->message = $message;

        return $this;
    }

    /**
     * @param $status string
     *
     * @return $this
     */
    public function setStatus($status)
    {
        if ($status == '3') {
            $this->sendDone = true;
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @param $environment string
     *
     * @return $this
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @return \string[]
     */
    public function getSentTo(): array
    {
        return $this->sentTo;
    }

    /**
     * @return \string[]
     */
    public function getSentFrom(): array
    {
        return $this->sentFrom;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn(): \DateTime
    {
        return $this->createdOn;
    }

    /**
     * @return bool
     */
    public function isSendDone(): bool
    {
        return $this->sendDone;
    }

    /**
     * @param $message
     */
    private function assignExtraFields($message)
    {
        $sm = $this->getSwiftMailerInstance($message);

        $sentTo = $sm->getTo();
        if (is_array($sentTo)) {
            $this->sentTo[] = current($sentTo);
            $this->sentTo[] = key($sentTo);
        } else {
            $this->sentTo[] = $sentTo;
        }

        $sendFrom = $sm->getFrom();
        if (is_array($sendFrom)) {
            $this->sentFrom[] = current($sendFrom);
            $this->sentFrom[] = key($sendFrom);
        } else {
            $this->sentFrom[] = $sendFrom;
        }

        $this->subject = $sm->getSubject();
        $this->createdOn = new \DateTime('@'.$sm->getDate());
    }

    /**
     * @param string $message
     *
     * @return \Swift_Message
     */
    private function getSwiftMailerInstance($message)
    {
        return unserialize($message);
    }
}

/* EOF */

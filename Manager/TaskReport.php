<?php

namespace RybakDigital\QueueBundle\Manager;

use RybakDigital\QueueBundle\Manager\TaskReportInterface;

/**
 * RybakDigital\QueueBundle\Manager\TaskReport
 *
 * @author Kris Rybak <kris.rybak@rybakdigital.com>
 */
class TaskReport implements TaskReportInterface
{
    private $isCompleted;

    private $message;

    public function __construct()
    {
        $this->isCompleted = false;
    }

    public function setIsCompleted(?bool $isCompleted): ?self
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    public function getIsCompleted(): ?bool
    {
        return $this->isCompleted;
    }

    /**
     * Alias of self::getIsCompleted()
     */
    public function isCompleted(): ?bool
    {
        return $this->getIsCompleted();
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}

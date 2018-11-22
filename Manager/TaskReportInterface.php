<?php

namespace RybakDigital\QueueBundle\Manager;

/**
 * RybakDigital\QueueBundle\Manager\TaskReportInterface
 *
 * @author Kris Rybak <kris.rybak@rybakdigital.com>
 */
interface TaskReportInterface
{
    public function getIsCompleted(): ?bool;
    public function isCompleted(): ?bool;
    public function getMessage(): ?string;
}

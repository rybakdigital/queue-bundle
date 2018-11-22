<?php

namespace RybakDigital\QueueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RybakDigital\QueueBundle\Entity\Task
 *
 * @author Kris Rybak <kris.rybak@rybakdigital.com>
 * @ORM\Entity(repositoryClass="RybakDigital\QueueBundle\Repository\TaskRepository")
 * @ORM\Table(name="rd_queue_tasks")
 */
class Task
{
    const QUEUE_NAME = 'main';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Queue name
     *
     * @var string
     * @ORM\Column(name="queue", type="string", length=256, nullable=true)
     */
    private $queue;

    /**
     * @var string
     * @ORM\Column(name="callable", type="string", length=256, nullable=true)
     */
    private $callable;

    /**
     * @var string
     * @ORM\Column(name="method", type="string", length=256, nullable=true)
     */
    private $method;

    /**
     * @var integer
     * @ORM\Column(name="attempts", type="integer", nullable=true)
     */
    private $attempts;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="first_booked_in_at", type="datetime", nullable=true)
     */
    private $firstBookedInAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="booked_in_at", type="datetime", nullable=true)
     */
    private $bookedInAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="started_at", type="datetime", nullable=true)
     */
    private $startedAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="last_attempted_at", type="datetime", nullable=true)
     */
    private $lastAttemptedAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="completed_at", type="datetime", nullable=true)
     */
    private $completedAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="cancelled_at", type="datetime", nullable=true)
     */
    private $cancelledAt;

    /**
     * @ORM\Column(name="options", type="json", nullable=true)
     */
    private $options;

    /**
     * @ORM\Column(name="data", type="json", nullable=true)
     */
    private $data;

    public function __construct(?string $queue = self::QUEUE_NAME)
    {
        $this->queue = $queue;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttempts(): ?int
    {
        return $this->attempts;
    }

    public function setAttempts(?int $attempts): self
    {
        $this->attempts = $attempts;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getBookedInAt(): ?\DateTimeInterface
    {
        return $this->bookedInAt;
    }

    public function setBookedInAt(?\DateTimeInterface $bookedInAt): self
    {
        $this->bookedInAt = $bookedInAt;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getLastAttemptedAt(): ?\DateTimeInterface
    {
        return $this->lastAttemptedAt;
    }

    public function setLastAttemptedAt(?\DateTimeInterface $lastAttemptedAt): self
    {
        $this->lastAttemptedAt = $lastAttemptedAt;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeInterface $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getCancelledAt(): ?\DateTimeInterface
    {
        return $this->cancelledAt;
    }

    public function setCancelledAt(?\DateTimeInterface $cancelledAt): self
    {
        $this->cancelledAt = $cancelledAt;

        return $this;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function setOptions(?array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getFirstBookedInAt(): ?\DateTimeInterface
    {
        return $this->firstBookedInAt;
    }

    public function setFirstBookedInAt(?\DateTimeInterface $firstBookedInAt): self
    {
        $this->firstBookedInAt = $firstBookedInAt;

        return $this;
    }

    public function bookIn()
    {
        $now = new \DateTime;

        $this
            ->setFirstBookedInAt($now)
            ->setBookedInAt($now);

        return $this;
    }

    /**
     * This method rebooks task. Rebooking marks task as incomplete.
     * It will essentially move it to the bottom of the queue.
     * This is to ensure that task does not stick at the top
     * and prevents other task from being executed.
     */
    public function rebook()
    {
        $this
            ->setBookedInAt(new \DateTime)
            ->setCancelledAt(null)
            ->setCompletedAt(null);

        return $this;
    }

    public function bury()
    {
        $this->setCancelledAt(new \DateTime);

        return $this;
    }

    public function isBuried()
    {
        if (is_null($this->getCancelledAt())) {
            return false;
        }

        return true;
    }

    public function getCallable(): ?string
    {
        return $this->callable;
    }

    public function setCallable(?string $callable): self
    {
        $this->callable = $callable;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(?string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getQueue(): ?string
    {
        return $this->queue;
    }

    public function setQueue(?string $queue): self
    {
        $this->queue = $queue;

        return $this;
    }
}

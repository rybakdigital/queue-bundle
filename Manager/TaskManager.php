<?php

namespace RybakDigital\QueueBundle\Manager;

use RybakDigital\QueueBundle\Exception\QueueProcessException;
use RybakDigital\QueueBundle\Manager\TaskReport;
use RybakDigital\QueueBundle\Entity\Task;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * RybakDigital\QueueBundle\Manager\TaskManager
 *
 * @author Kris Rybak <kris.rybak@rybakdigital.com>
 */
class TaskManager implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Number of failed attempts before worker should cancel the task. Default 5.
     * @var int
     */
    protected $noOfAttempts;

    public function __construct(ContainerInterface $container, int $noOfAttempts = 5)
    {
        $this->setContainer($container);
        $this->noOfAttempts = $noOfAttempts;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function process(Task $task): TaskReport
    {
        $report = new TaskReport;

        $callable   = $task->getCallable();
        $method     = $task->getMethod();

        try {
            $service = $this->container->get($callable);
            
            $service->$method($task->getOptions(), $task->getData());
            $report->setIsCompleted(true);
        } catch (QueueProcessException $e) {
            $report->setMessage($e->getMessage());
        } catch (ServiceNotFoundException $e) {
            // Looks like task is linked a non-existent service
            // Lets try calling callas directly
            if(is_callable($callable, $method)) {
                $ret = call_user_func_array(array($callable, $method), array($task->getOptions(), $task->getData()));
                if ($ret) {
                    $report->setIsCompleted(true);
                }
            } else {
                $report->setMessage("Looks like task is linked to a non-existent service or PHP callable");
            }
        }

        if (!$report->isCompleted() && ($task->getAttempts()) >= $this->noOfAttempts) {
            // Bury the task
            $task->bury();
        }

        return $report;
    }
}

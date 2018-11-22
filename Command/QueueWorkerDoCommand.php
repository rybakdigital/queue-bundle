<?php

namespace RybakDigital\QueueBundle\Command;

use RybakDigital\QueueBundle\Manager\TaskManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

class QueueWorkerDoCommand extends Command
{
    protected static $defaultName = 'rybakdigital:queue:worker:do';

    protected $em;

    protected $manager;

    public function __construct(EntityManagerInterface $em, TaskManager $manager)
    {
        $this->em       = $em;
        $this->manager  = $manager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Asks worker to execute next available task in the queue')
            ->addArgument('queue', InputArgument::OPTIONAL, 'Queue name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        // Get next available task
        $queue = $input->getArgument('queue');
        $task = $this->em->getRepository('RybakDigitalQueueBundle:Task')->getNext($queue);

        // Only continue if you have task to work with
        if (!$task){
            return $io->note('You have no new tasks in queue.');
        }

        // First mark task as started
        $now = new \Datetime; 
        $task
            ->setStartedAt($now)
            ->setLastAttemptedAt($now)
            ->setAttempts($task->getAttempts() + 1)
            ->rebook();

        // Update task status
        try {
            $this->em->persist($task);
            $this->em->flush($task);
        } catch (ServerException $e) {
            return $io->error($e->getMessage());
        }

        $taskReport = $this->manager->process($task);

        if (!$taskReport->isCompleted()) {
            if (!$task->isBuried()) {
                // Rebook task
                $task->rebook();
            }
        } else {
            // Mark task as completed
            $task->setCompletedAt(new \Datetime);
        }

        // Update task status
        try {
            $this->em->persist($task);
            $this->em->flush($task);
        } catch (ServerException $e) {
            return $io->error($e->getMessage());
        }

        if (!$taskReport->isCompleted()) {
            if ($task->isBuried()) {
                return $io->warning($task->getId()
                . ' - Queue Manager has tried hard but could not complete the task.'
                . ' The task has now been buried to prevent bubbling. Error Message: ' . $taskReport->getMessage());
            }

            return $io->warning($task->getId() . ' - Task could not be completed at this time and has been rebooked. Error Message: ' . $taskReport->getMessage());
        }

        return $io->success($task->getId() . ' - Task has been completed successfully.');
    }
}

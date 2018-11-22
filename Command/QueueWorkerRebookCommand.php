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

class QueueWorkerRebookCommand extends Command
{
    protected static $defaultName = 'rybakdigital:queue:worker:rebook';

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Asks worker to rebook an existing task. Completed or cancelled tasks will be rebooked and added to the queue.')
            ->addArgument('id', InputArgument::REQUIRED, 'Task id');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        // Get next available task
        $id = $input->getArgument('id');
        $task = $this->em->getRepository('RybakDigitalQueueBundle:Task')->findOneById($id);

        // Only continue if you have task to work with
        if (!$task){
            return $io->warning('Task with id ' . $id . ' does not exists.');
        }

        // Rebook the task
        $task->rebook();

        // Update task status
        try {
            $this->em->persist($task);
            $this->em->flush($task);
        } catch (ServerException $e) {
            return $io->error($e->getMessage());
        }

        return $io->success('Task has been rebooked successfully.');
    }
}

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

class QueueWorkerBookCommand extends Command
{
    protected static $defaultName = 'rybakdigital:queue:worker:book';

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em       = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Asks worker to book in existing task in a queue')
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

        if (!is_null($task->getCompletedAt())){
            return $io->warning('Task ' . $id . ' has already been completed. Consider rebooking the task instead: rybakdigital:queue:worker:rebook');
        }

        if (!is_null($task->getBookedInAt())){
            return $io->warning('Task ' . $id . ' has already been booked in. Consider rebooking the task instead: rybakdigital:queue:worker:rebook');
        }

        // Book the task in
        $task->bookIn();

        // Update task status
        try {
            $this->em->persist($task);
            $this->em->flush($task);
        } catch (ServerException $e) {
            return $io->error($e->getMessage());
        }

        return $io->success('Task has been booked in successfully.');
    }
}

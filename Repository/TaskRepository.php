<?php

namespace RybakDigital\QueueBundle\Repository;

use RybakDigital\QueueBundle\Entity\Task;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends EntityRepository
{
    public function getNext($queue = null)
    {
        // Create query
        $qb = $this
            ->createQueryBuilder('task');

        $qb
            ->select('task')
            ->andWhere('task.bookedInAt is not NULL')
            ->andWhere('task.completedAt is NULL')
            ->andWhere('task.cancelledAt is NULL')
            ->orderBy('task.bookedInAt', 'ASC')
            ->addOrderBy('task.bookedInAt', 'ASC')
            ->setMaxResults(1);

        if (!is_null($queue)) {
            $qb
                ->andWhere('task.queue = :queue')
                ->setParameter('queue', $queue);
        }

        // Execute
        $query = $qb->getQuery();

        try {
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}

<?php
namespace EasyScrumREST\TaskBundle\Entity;
use EasyScrumREST\SprintBundle\Entity\Sprint;

use EasyScrumREST\UserBundle\Entity\ApiUser;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class HoursSpentRepository extends EntityRepository
{

    public function getHoursSpentByUser(Task $task, $date)
    {
        $qb = $this->createQueryBuilder('h');

        $qb->select('h, t, u');
        $qb->join('h.task', 't');
        $qb->join('h.user', 'u');
        $qb->andWhere($qb->expr()->eq('t.id', $task->getId()));
        $qb->andWhere($qb->expr()->gte('h.created', '\''.$date->format('Y-m-d').' 00:00:00\''));
        $qb->andWhere($qb->expr()->lte('h.created', '\''.$date->format('Y-m-d').' 23:59:59\''));

        return $qb->getQuery()->getResult();
    }
    
}

<?php
namespace EasyScrumREST\TaskBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class TaskRepository extends EntityRepository
{

    public function findTaskBySearch($search = array(), $limit = 50, $offset = 0, $orderby = null)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t');
        if (isset($search['name'])) {
            $qb->orWhere($qb->expr()->like('t.name', "'%".$search['name']."%'"));
        }

        if (isset($search['sprint'])) {
            $qb->andWhere($qb->expr()->eq('t.sprint', $search['sprint']));
        }

        if (isset($search['sprint_salt'])) {
            $qb->join('t.sprint', 's');
            $qb->andWhere($qb->expr()->eq('s.salt', "'".$search['sprint_salt']."'"));
        }

        if (isset($search['state'])) {
            $qb->andWhere($qb->expr()->eq('t.state', "'".$search['state']."'"));
        }

        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);
        $qb->orderBy('t.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findNotInEntities($user, $entities = array())
    {
        $qb = $this->createQueryBuilder('t');

        $qb->select('t, s.salt as sprint_salt');
        $qb->join('t.sprint', 's');
        $qb->andWhere($qb->expr()->eq('s.company', $user));
        if (count($entities)>0) {
            $subqb = $this->createQueryBuilder('task');
            $subqb->select('task.id');
            foreach ($entities as $entity) {
                $subqb->orWhere($subqb->expr()->eq('task.id', $entity));
            }
            $qb->andWhere($qb->expr()->notIn('t.id', $subqb->getDQL()));
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
    
    public function findBySearch($search = null)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t, s');
        $qb->join('t.sprint', 's');

        $qb->andWhere($qb->expr()->like('t.state', '\''."DONE".'\''));
        $qb->andWhere($qb->expr()->eq('s.company', $search['company']));
        if (isset($search['project'])) {
            $qb->andWhere($qb->expr()->eq('s.project', $search['project']->getId()));
        }

        if (isset($search['sprint'])) {
            $qb->andWhere($qb->expr()->eq('s.id', $search['sprint']->getId()));
        }
        
        if (isset($search['from']) && isset($search['to'])) {
            $qb->andWhere($qb->expr()->gte('t.updated', '\''.$search['from']->format('Y-m-d').'\''));
            $qb->andWhere($qb->expr()->lte('t.updated', '\''.$search['to']->format('Y-m-d').'\''));
        }
    
        $qb->orderBy('t.id', 'DESC');

        return $qb->getQuery();
    }

    public function findTasksForStatistics($search)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t');
        $qb->join('t.sprint', 's');
        $qb->andWhere($qb->expr()->like('t.state', '\''."DONE".'\''));
        $qb->andWhere($qb->expr()->eq('s.company', $search->getCompany()));
        $qb->andWhere($qb->expr()->eq('s.finalized', true));
        if ($search->getFrom()) {
            $qb->andWhere($qb->expr()->gte('s.dateFrom', '\''.$search->getFrom()->format('Y-m-d').'\''));
        }
        if ($search->getTo()) {
            $qb->andWhere($qb->expr()->lte('s.dateTo', '\''.$search->getTo()->format('Y-m-d').'\''));
        }
        if ($search->getProject()) {
            $qb->andWhere($qb->expr()->eq('s.project', $search->getProject()->getId()));
        }

        return $qb->getQuery()->getResult();
    }

    public function findTasksDoneByUser($user)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t');
        $qb->join('t.sprint', 's');
        $qb->join('t.listHours', 'h', Query\Expr\Join::WITH, "h.user = ".$user->getId());
        $qb->andWhere($qb->expr()->like('t.state', '\''."DONE".'\''));
        $qb->andWhere($qb->expr()->eq('s.company', $user->getCompany()->getId()));
        $qb->andWhere($qb->expr()->eq('s.finalized', true));

        return $qb->getQuery()->getResult();
    }

}
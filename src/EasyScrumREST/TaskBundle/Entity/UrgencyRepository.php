<?php
namespace EasyScrumREST\TaskBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UrgencyRepository extends EntityRepository
{

    public function findUrgenciesForStatistics($search)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u');
        $qb->join('u.project', 'p');
        $qb->join('u.sprint', 's');
        $qb->andWhere($qb->expr()->like('u.state', '\''."DONE".'\''));
        $qb->andWhere($qb->expr()->eq('p.company', $search->getCompany()));
        if ($search->getFrom()) {
            $qb->andWhere($qb->expr()->gte('s.dateFrom', '\''.$search->getFrom()->format('Y-m-d').'\''));
        }
        if ($search->getTo()) {
            $qb->andWhere($qb->expr()->lte('s.dateTo', '\''.$search->getTo()->format('Y-m-d').'\''));
        }
        if ($search->getProject()) {
            $qb->andWhere($qb->expr()->eq('p.id', $search->getProject()->getId()));
        }

        return $qb->getQuery()->getResult();
    }

    /*public function findOwnerBySearch($search = null, $orderby = null, $limit, $offset)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t');
        if (isset($search['name'])) {
            $qb->orWhere($qb->expr()->like('t.name', "'%".$search['name']."%'"));
        }

        if (isset($search['sprint'])) {
            $qb->andWhere($qb->expr()->eq('t.sprint', $search['sprint']));
        }

        $qb->andWhere($qb->expr()->isNull('t.deleted'));
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
    }*/

}
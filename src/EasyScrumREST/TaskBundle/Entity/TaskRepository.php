<?php
namespace EasyScrumREST\TaskBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class TaskRepository extends EntityRepository
{

    public function findOwnerBySearch($search = null, $orderby = null, $limit, $offset)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t');
        if (isset($search['name'])) {
            $qb->orWhere($qb->expr()->like('t.name', "'%".$search['name']."%'"));
        }

        if (isset($search['sprint'])) {
            $qb->andWhere($qb->expr()->eq('t.sprint', $search['sprint']));
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

}
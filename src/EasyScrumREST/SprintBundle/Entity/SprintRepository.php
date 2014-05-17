<?php
namespace EasyScrumREST\SprintBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class SprintRepository extends EntityRepository {

    public function findSprintBySearch($search = null, $orderby = null, $limit, $offset)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->select('l');
        if (isset($search['name'])) {
            $qb->orWhere($qb->expr()->like('l.name',"'%".$search['name']."%'"));
        }

        if (isset($search['user'])) {
            $qb->andWhere($qb->expr()->eq('l.user', $search['user']));
        }

        $qb->andWhere($qb->expr()->isNull('l.deleted'));
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);
        $qb->orderBy('l.id', 'DESC');

        return $qb->getQuery()->getResult();
    }
    
    public function findNotInEntities($entities = array(), $user)
    {
        $qb = $this->createQueryBuilder('l');

        $qb->select('l');
        $qb->andWhere($qb->expr()->eq('l.user', $user));
        if (count($entities)>0) {
            $subqb = $this->createQueryBuilder('log');
            $subqb->select('log.id');
            foreach ($entities as $entity) {
                $subqb->orWhere($subqb->expr()->eq('log.id', $entity));
            }
            $qb->andWhere($qb->expr()->notIn('l.id', $subqb->getDQL()));
        }
        $qb->andWhere($qb->expr()->isNull('l.deleted'));

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

}
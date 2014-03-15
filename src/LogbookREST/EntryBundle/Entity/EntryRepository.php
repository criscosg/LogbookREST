<?php
namespace LogbookREST\EntryBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class EntryRepository extends EntityRepository {

    public function findOwnerBySearch($search = null, $orderby = null, $limit, $offset)
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('e');
        if (isset($search['name'])) {
            $qb->orWhere($qb->expr()->like('e.name',"'%".$search['name']."%'"));
        }

        if (isset($search['log'])) {
            $qb->andWhere($qb->expr()->eq('e.log', $search['log']));
        }

        $qb->andWhere($qb->expr()->isNull('e.deleted'));
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);
        $qb->orderBy('e.id', 'DESC');

        return $qb->getQuery()->getResult();
    }
    
    public function findNotInEntities($entities = array(), $user)
    {
        $qb = $this->createQueryBuilder('e');

        $qb->select('e, l.salt as log_salt');
        $qb->join('e.log', 'l');
        $qb->andWhere($qb->expr()->eq('l.user', $user));
        if (count($entities)>0) {
            $subqb = $this->createQueryBuilder('entry');
            $subqb->select('entry.id');
            foreach ($entities as $entity) {
                $subqb->orWhere($subqb->expr()->eq('entry.id', $entity));
            }
            $qb->andWhere($qb->expr()->notIn('e.id', $subqb->getDQL()));
        }
        $qb->andWhere($qb->expr()->isNull('e.deleted'));

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

}
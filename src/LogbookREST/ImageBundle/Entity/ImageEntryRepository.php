<?php

namespace LogbookREST\ImageBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query;

class ImageEntryRepository extends EntityRepository {
   
    public function findNotInEntities($entities = array(), $user)
    {
        $qb = $this->getQBSynchronize($entities, $user);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
    
    public function findCountNotInEntities($entities = array(), $user)
    {
        $qb = $this->getQBSynchronize($entities, $user);
    
        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }
    
    private function getQBSynchronize($entities = array(), $user)
    {
        $qb = $this->createQueryBuilder('i');
        
        $qb->select('i, e.salt as entry_salt');
        $qb->join('i.entry', 'e');
        $qb->andWhere($qb->expr()->eq('e.user', $user));
        if (count($entities)>0) {
            $subqb = $this->createQueryBuilder('image');
            $subqb->select('image.id');
            foreach ($entities as $entity) {
                $subqb->orWhere($subqb->expr()->eq('image.id', $entity));
            }
            $qb->andWhere($qb->expr()->notIn('i.id', $subqb->getDQL()));
        }
        
        return $qb;
    }
}
<?php

namespace EasyScrumREST\ActionBundle\Entity;

use EasyScrumREST\UserBundle\Entity\ApiUser;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ActionRepository extends EntityRepository
{
    public function lastActions(ApiUser $user)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a');
        $qb->join('a.user', 'u');
        $qb->join('u.company', 'c');
        $qb->andWhere($qb->expr()->eq('u.company', $user->getCompany()->getId()));
        $qb->setMaxResults(6);
        $qb->orderBy('a.id', 'DESC');
        
        return $qb->getQuery()->getResult();
    }
    
    public function companyActions(ApiUser $user)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a');
        $qb->join('a.user', 'u');
        $qb->join('u.company', 'c');
        $qb->andWhere($qb->expr()->eq('u.company', $user->getCompany()->getId()));
        $qb->orderBy('a.id', 'DESC');
    
        return $qb->getQuery()->getResult();
    }
}
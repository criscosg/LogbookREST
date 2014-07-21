<?php
namespace EasyScrumREST\MessageBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class MessageRepository extends EntityRepository
{

    public function findMessageBySearch($limit, $offset, $company= null, $date=null)
    {
        $qb = $this->createQueryBuilder('m');
        $qb->select('m');
        $qb->join('m.user', 'u');
        $qb->join('u.company', 'c');

        if (isset($company)) {
            $qb->andWhere($qb->expr()->eq('u.company', $company));
        }

        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);
        $qb->orderBy('m.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

}

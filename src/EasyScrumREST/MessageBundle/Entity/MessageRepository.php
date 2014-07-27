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
    
    public function findMessageInLastSeconds($company)
    {
        $time = new \DateTime('now');
        $time->sub(new \DateInterval('PT30S'));
        $qb = $this->createQueryBuilder('m');
        $qb->select('m');
        $qb->join('m.user', 'u');
        $qb->join('u.company', 'c');
        $qb->andWhere($qb->expr()->eq('u.company', $company));
        $qb->andWhere($qb->expr()->gte('m.created', '\''.$time->format('Y-m-d H:i:s').'\''));
        
        $qb->orderBy('m.id', 'DESC');
    
        return $qb->getQuery()->getResult();
    }

    public function findLastMessages($user)
    {
        $qb = $this->createQueryBuilder('m');
        $qb->select('m');
        $qb->join('m.user', 'u');
        $qb->join('u.company', 'c');
        $qb->andWhere($qb->expr()->eq('u.company', $user->getCompany()->getId()));
        $qb->andWhere($qb->expr()->neq('m.user', $user->getId()));
        $qb->orderBy('m.id', 'DESC');
        $qb->setMaxResults(3);

        return $qb->getQuery()->getResult();
    }
}

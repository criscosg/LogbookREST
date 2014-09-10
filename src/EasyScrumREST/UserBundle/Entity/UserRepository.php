<?php
namespace EasyScrumREST\UserBundle\Entity;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query;
use Doctrine\DBAL\Query\QueryBuilder;
use DoctrineExtensions\Types\Date;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findCompanyUsers($company)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u');
        $qb->andWhere($qb->expr()->eq('u.company', $company));

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
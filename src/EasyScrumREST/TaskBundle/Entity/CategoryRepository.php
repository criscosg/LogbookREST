<?php
namespace EasyScrumREST\TaskBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class CategoryRepository extends EntityRepository
{

    public function findNotInEntities($user, $entities = array())
    {
        $qb = $this->createQueryBuilder('c');

        $qb->select('c');
        if (count($entities)>0) {
            $subqb = $this->createQueryBuilder('cat');
            $subqb->select('cat.id');
            foreach ($entities as $entity) {
                $subqb->orWhere($subqb->expr()->eq('cat.id', $entity));
            }
            $qb->andWhere($qb->expr()->notIn('c.id', $subqb->getDQL()));
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

}
<?php
namespace EasyScrumREST\ProjectBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class BacklogRepository extends EntityRepository
{

    public function findNotInEntities($company, $entities = array())
    {
        $qb = $this->createQueryBuilder('b');

        $qb->select('b');
        $qb->join('b.project', 'p');

        $qb->andWhere($qb->expr()->eq('p.company', $company));
        if (count($entities)>0) {
            $subqb = $this->createQueryBuilder('backlog');
            $subqb->select('backlog.id');
            foreach ($entities as $entity) {
                $subqb->orWhere($subqb->expr()->eq('backlog.id', $entity));
            }
            $qb->andWhere($qb->expr()->notIn('b.id', $subqb->getDQL()));
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

}
<?php
namespace EasyScrumREST\SprintBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class SprintRepository extends EntityRepository
{

    public function findSprintBySearch($limit, $offset, $search = null, $orderby = null)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s');
        if (isset($search['name'])) {
            $qb->orWhere($qb->expr()->like('s.name', "'%".$search['name']."%'"));
        }

        if (isset($search['company'])) {
            $qb->andWhere($qb->expr()->eq('s.company', $search['company']));
        }

        $qb->andWhere($qb->expr()->isNull('s.deleted'));
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);
        $qb->orderBy('s.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findNotInEntities($company, $entities = array())
    {
        $qb = $this->createQueryBuilder('s');

        $qb->select('s');
        $qb->join('s.company', 'c');

        $qb->andWhere($qb->expr()->eq('s.company', $company));
        if (count($entities)>0) {
            $subqb = $this->createQueryBuilder('sprint');
            $subqb->select('sprint.id');
            foreach ($entities as $entity) {
                $subqb->orWhere($subqb->expr()->eq('sprint.id', $entity));
            }
            $qb->andWhere($qb->expr()->notIn('s.id', $subqb->getDQL()));
        }
        $qb->andWhere($qb->expr()->isNull('s.deleted'));

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

}
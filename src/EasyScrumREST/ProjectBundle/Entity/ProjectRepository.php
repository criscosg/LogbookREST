<?php
namespace EasyScrumREST\ProjectBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ProjectRepository extends EntityRepository
{

    public function findProjectBySearch($limit, $offset, $search = null, $orderby = null)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s');
        if (isset($search['title'])) {
            $qb->orWhere($qb->expr()->like('s.title', "'%".$search['title']."%'"));
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
        $qb = $this->createQueryBuilder('p');

        $qb->select('p, o.salt as owner_salt');
        $qb->join('p.company', 'c');
        $qb->leftJoin('p.owner', 'o');

        $qb->andWhere($qb->expr()->eq('p.company', $company));
        if (count($entities)>0) {
            $subqb = $this->createQueryBuilder('project');
            $subqb->select('project.id');
            foreach ($entities as $entity) {
                $subqb->orWhere($subqb->expr()->eq('project.id', $entity));
            }
            $qb->andWhere($qb->expr()->notIn('p.id', $subqb->getDQL()));
        }
        $qb->andWhere($qb->expr()->isNull('p.deleted'));

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

}
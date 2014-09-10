<?php
namespace EasyScrumREST\SprintBundle\Entity;
use EasyScrumREST\UserBundle\Entity\ApiUser;
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

        if (isset($search['project'])) {
            $qb->andWhere($qb->expr()->eq('s.project', $search['project']));
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

        $qb->select('s, p.salt as project_salt');
        $qb->join('s.project', 'p');
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
    
    public function findActiveSprints($company)
    {
        $date=new \DateTime('today');
        $qb = $this->createQueryBuilder('s');
        $qb->select('s');
        $qb->join('s.company', 'c');
        $qb->andWhere($qb->expr()->eq('s.company', $company));
        $qb->andWhere($qb->expr()->isNull('s.finalized'));
        $qb->andWhere($qb->expr()->lte('s.fromDate', '\''.$date->format('Y-m-d').'\''));
        $qb->andWhere($qb->expr()->gte('s.toDate', '\''.$date->format('Y-m-d').'\''));

        return $qb->getQuery()->getResult();
    }

}
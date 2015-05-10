<?php
namespace EasyScrumREST\TaskBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UrgencyRepository extends EntityRepository
{

    public function findAllUrgenciesCompany($company, $project = null)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u');
        $qb->join('u.project', 'p');
        $qb->andWhere($qb->expr()->like('u.state', '\''."DONE".'\''));
        $qb->andWhere($qb->expr()->eq('p.company', $company));
        if($project) {
            $qb->andWhere($qb->expr()->eq('p.id', $project));
        }

        return $qb->getQuery()->getResult();
    }

    /*public function findOwnerBySearch($search = null, $orderby = null, $limit, $offset)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t');
        if (isset($search['name'])) {
            $qb->orWhere($qb->expr()->like('t.name', "'%".$search['name']."%'"));
        }

        if (isset($search['sprint'])) {
            $qb->andWhere($qb->expr()->eq('t.sprint', $search['sprint']));
        }

        $qb->andWhere($qb->expr()->isNull('t.deleted'));
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);
        $qb->orderBy('t.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findNotInEntities($user, $entities = array())
    {
        $qb = $this->createQueryBuilder('t');

        $qb->select('t, s.salt as sprint_salt');
        $qb->join('t.sprint', 's');
        $qb->andWhere($qb->expr()->eq('s.company', $user));
        if (count($entities)>0) {
            $subqb = $this->createQueryBuilder('task');
            $subqb->select('task.id');
            foreach ($entities as $entity) {
                $subqb->orWhere($subqb->expr()->eq('task.id', $entity));
            }
            $qb->andWhere($qb->expr()->notIn('t.id', $subqb->getDQL()));
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }*/

}
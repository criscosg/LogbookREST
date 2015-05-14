<?php
namespace EasyScrumREST\SprintBundle\Entity;
use EasyScrumREST\FrontendBundle\Util\StatisticSearchHelper;
use EasyScrumREST\UserBundle\Entity\ApiUser;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class SprintRepository extends EntityRepository
{

    public function findSprintBySearch($limit, $offset, $search = array(), $orderby = null)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s');
        if (isset($search['name'])) {
            $qb->orWhere($qb->expr()->like('s.title', "'%".$search['name']."%'"));
        }

        if (isset($search['company'])) {
            $qb->andWhere($qb->expr()->eq('s.company', $search['company']));
        }

        if (isset($search['project'])) {
            $qb->andWhere($qb->expr()->eq('s.project', $search['project']));
        }

        if (isset($search['project_salt'])) {
            $qb->join('s.project', 'p');
            $qb->andWhere($qb->expr()->eq('p.salt', "'".$search['project_salt']."'"));
        }

        $qb->andWhere($qb->expr()->isNull('s.deleted'));
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);
        $qb->orderBy('s.id', 'DESC');

        return $qb->getQuery()->getResult();
    }
    
    public function findSearch($search)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s');
        $qb->andWhere($qb->expr()->eq('s.company', $search['company']));
        if (isset($search['name'])) {
            $qb->andWhere($qb->expr()->like('s.title', "'%".$search['name']."%'"));
        }
        if (isset($search['project'])) {
            $qb->andWhere($qb->expr()->eq('s.project', $search['project']->getId()));
        }
        if (isset($search['active']) && $search['active']) {
            $qb->andWhere($qb->expr()->isNull('s.finalized'));
        }

        $qb->andWhere($qb->expr()->isNull('s.deleted'));
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
    
    public function findAllActiveSprints()
    {
        $date=new \DateTime('today');
        $qb = $this->createQueryBuilder('s');
        $qb->select('s');
        $qb->andWhere($qb->expr()->isNull('s.finalized'));
        $qb->andWhere($qb->expr()->isNotNull('s.title'));
        $qb->andWhere($qb->expr()->lte('s.dateFrom', '\''.$date->format('Y-m-d').'\''));

        return $qb->getQuery()->getResult();
    }
    
    public function findActiveSprints($company)
    {
        $date=new \DateTime('today');
        $qb = $this->createQueryBuilder('s');
        $qb->select('s');
        $qb->join('s.company', 'c');
        $qb->andWhere($qb->expr()->eq('s.company', $company));
        $qb->andWhere($qb->expr()->isNull('s.finalized'));
        $qb->andWhere($qb->expr()->isNotNull('s.title'));
        $qb->andWhere($qb->expr()->lte('s.dateFrom', '\''.$date->format('Y-m-d').'\''));
        //$qb->andWhere($qb->expr()->gte('s.dateTo', '\''.$date->format('Y-m-d').'\''));

        return $qb->getQuery()->getResult();
    }

    public function findGroupedSprints($search)
    {
        $date=new \DateTime('today');
        $qb = $this->createQueryBuilder('s');
        $qb->select('s');
        $qb->join('s.company', 'c');
        $qb->andWhere($qb->expr()->eq('s.company', $search['company']));
        $qb->andWhere($qb->expr()->isNull('s.finalized'));
        $qb->andWhere($qb->expr()->isNotNull('s.title'));
        $qb->andWhere($qb->expr()->isNotNull('s.dateTo'));
        $qb->andWhere($qb->expr()->gte('s.dateTo', '\''.$date->format('Y-m-d').'\''));
        if (isset($search['from'])) {
            $qb->andWhere($qb->expr()->lte('s.dateFrom', '\''.$search['from']->format('Y-m-d').'\''));
        }
        if (isset($search['to'])) {
            $qb->andWhere($qb->expr()->gte('s.dateTo', '\''.$search['to']->format('Y-m-d').'\''));
        }


        return $qb->getQuery()->getResult();
    }

    public function findSprintsForStatistics(StatisticSearchHelper $search)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s');
        $qb->join('s.company', 'c');
        $qb->andWhere($qb->expr()->eq('s.company', $search->getCompany()));
        $qb->andWhere($qb->expr()->isNotNull('s.title'));
        $qb->andWhere($qb->expr()->isNotNull('s.dateTo'));
        $qb->andWhere($qb->expr()->eq('s.finalized', true));
        if ($search->getFrom()) {
            $qb->andWhere($qb->expr()->gte('s.dateFrom', '\''.$search->getFrom()->format('Y-m-d').'\''));
        }
        if ($search->getTo()) {
            $qb->andWhere($qb->expr()->lte('s.dateTo', '\''.$search->getTo()->format('Y-m-d').'\''));
        }
        if ($search->getProject()) {
            $qb->andWhere($qb->expr()->eq('s.project', $search->getProject()->getId()));
        }

        return $qb->getQuery()->getResult();
    }
}
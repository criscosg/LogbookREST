<?php
namespace EasyScrumREST\SynchronizeBundle\Synchronize;

use EasyScrumREST\ProjectBundle\Entity\Backlog;
use Doctrine\ORM\EntityManager;
use Symfony\Component\BrowserKit\Request;
use EasyScrumREST\SprintBundle\Entity\Sprint;
use EasyScrumREST\UserBundle\Entity\ApiUser;
use EasyScrumREST\ProjectBundle\Entity\Project;
use EasyScrumREST\TaskBundle\Entity\Task;
use EasyScrumREST\TaskBundle\Entity\Category;
use EasyScrumREST\SynchronizeBundle\Util\ArrayHelper;

class Synchronize
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function synchronize($mobileDB, ApiUser $user)
    {
        if (isset($mobileDB['projects'])) {
            $projects=$this->compareProjects($mobileDB['projects'], $user);
        } else {
            $projects=$this->compareProjects(array(), $user);
        }
        if (isset($mobileDB['backlog'])) {
            $backlogs=$this->compareBacklog($mobileDB['backlogs'], $user);
        } else {
            $backlogs=$this->compareBacklog(array(), $user);
        }
        if (isset($mobileDB['sprints'])) {
            $sprints=$this->compareSprints($mobileDB['sprints'], $user);
        } else {
            $sprints=$this->compareSprints(array(), $user);
        }
        if (isset($mobileDB['categories'])) {
            $tasks=$this->compareCategories($mobileDB['categories'], $user);
        } else {
            $tasks=$this->compareCategories(array(), $user);
        }
        if (isset($mobileDB['tasks'])) {
            $tasks=$this->compareTasks($mobileDB['tasks'], $user);
        } else {
            $tasks=$this->compareTasks(array(), $user);
        }

        return array('users'=>$this->em->getRepository('UserBundle:ApiUser')->findCompanyUsers($user->getCompany()->getId()),
                 'projects'=>ArrayHelper::flattMultilevelEntityArray($projects), 'backlog'=>ArrayHelper::flattMultilevelEntityArray($backlogs), 'sprints'=>ArrayHelper::flattMultilevelEntityArray($sprints), 'tasks'=>ArrayHelper::flattMultilevelEntityArray($tasks));
    }
    
    private function compareProjects($projects, ApiUser $user)
    {
        $entities = array();
        foreach ($projects as $projectMobile) {
            $projectDB=$this->em->getRepository('ProjectBundle:Project')->findOneBySalt($projectMobile['salt']);
            if (!$projectDB) {
                $projectDB = new Project();
                $projectDB->setCompany($user->getCompany());
                $this->saveProject($projectMobile, $projectDB);
                $entities[] = $projectDB->getId();
            } else {
                $value=str_replace("/", "-", $projectMobile['updated']);
                $date=new \DateTime($value);
                if ($projectDB->getUpdated() < $date) {
                    $this->saveProject($projectMobile, $projectDB);
                    $entities[] = $projectDB->getId();
                } elseif ($projectDB->getUpdated() == $date) {
                    $entities[] = $projectDB->getId();
                }
            }
        }

        return $this->em->getRepository('ProjectBundle:Project')->findNotInEntities($user->getCompany()->getId(), $entities);
    }

    private function compareBacklog($tasks, ApiUser $user)
    {
        $entities = array();
        foreach ($tasks as $taskMobile) {
            $taskDB=$this->em->getRepository('ProjectBundle:Backlog')->findOneBySalt($taskMobile['salt']);
            if (!$taskDB) {
                $taskDB = new Backlog();
                $this->saveBacklog($taskMobile, $taskDB);
                $entities[] = $taskDB->getId();
            } else {
                $value=str_replace("/", "-", $projectMobile['updated']);
                $date=new \DateTime($value);
                if ($taskDB->getUpdated() < $date) {
                    $this->saveTask($taskMobile, $taskDB);
                    $entities[] = $taskDB->getId();
                } elseif ($taskDB->getUpdated() == $date) {
                    $entities[] = $taskDB->getId();
                }
            }
        }

        return $this->em->getRepository('ProjectBundle:Backlog')->findNotInEntities($user->getCompany()->getId(), $entities);
    }

    private function compareSprints($sprints, ApiUser $user)
    {
        $entities = array();
        foreach ($sprints as $sprintMobile) {
            $sprintDB=$this->em->getRepository('SprintBundle:Sprint')->findOneBySalt($sprintMobile['salt']);
            if (!$sprintDB) {
                $sprintDB = new Sprint();
                $sprintDB->setCompany($user->getCompany());
                $this->saveSprint($sprintMobile, $sprintDB);
                $entities[] = $sprintDB->getId();
            } else {
                $value=str_replace("/", "-", $projectMobile['updated']);
                $date=new \DateTime($value);
                if ($sprintDB->getUpdated() < $date) {
                    $this->saveSprint($sprintMobile, $sprintDB);
                    $entities[] = $sprintDB->getId();
                } elseif ($sprintDB->getUpdated() == $date) {
                    $entities[] = $sprintDB->getId();
                }
            }
        }

        return $this->em->getRepository('SprintBundle:Sprint')->findNotInEntities($user->getCompany()->getId(), $entities);
    }

    private function compareTasks($tasks, ApiUser $user)
    {
        $entities = array();
        foreach ($tasks as $taskMobile) {
            $taskDB=$this->em->getRepository('TaskBundle:Task')->findOneBySalt($taskMobile['salt']);
            if (!$taskDB) {
                $taskDB = new Task();
                $this->saveTask($taskMobile, $taskDB);
                $entities[] = $taskDB->getId();
            } else {
                $value=str_replace("/", "-", $projectMobile['updated']);
                $date=new \DateTime($value);
                if ($taskDB->getUpdated() < $date) {
                    $this->saveTask($taskMobile, $taskDB);
                    $entities[] = $taskDB->getId();
                } elseif ($taskDB->getUpdated() == $date) {
                    $entities[] = $taskDB->getId();
                }
            }
        }

        return $this->em->getRepository('TaskBundle:Task')->findNotInEntities($user->getCompany()->getId(), $entities);
    }

    private function compareCategories($categories, ApiUser $user)
    {
        $entities = array();
        foreach ($categories as $categoryMobile) {
            $categoryDB=$this->em->getRepository('TaskBundle:Category')->findOneByName($categoryMobile['name']);
            if (!$categoryDB) {
                $categoryDB = new Category();
                $this->saveCategory($categoryMobile, $categoryDB);
                $entities[] = $categoryDB->getId();
            } else {
                $entities[] = $categoryDB->getId();
            }
        }

        return $this->em->getRepository('TaskBundle:Category')->findNotInEntities($entities);
    }
    
    private function saveProject($projectMobile, $projectDB)
    {
        foreach ($projectMobile as $property => $value) {
            if ($property=='owner_salt') {
                $projectDB->setOwner($this->em->getRepository('UserBundle:ApiUser')->findOneBySalt($value));
            } elseif ($property != 'updated' && $property != 'created' && $property != 'dateFrom' && $property != 'dateTo') {
                $method = sprintf('set%s', ucwords($property));
                if (method_exists($projectDB, $method)) {
                    $projectDB->$method($value);
                }
            } else {
                if ($value) {
                    $value=str_replace("/", "-", $value);
                    $date=new \DateTime($value);
                    $method = sprintf('set%s', ucwords($property));
                    if (method_exists($projectDB, $method)) {
                        $projectDB->$method($date);
                    }
                }
            }
        }
        $this->em->persist($projectDB);
        $this->em->flush();
    }
    
    private function saveBacklog($taskMobile, $taskDB)
    {
        foreach ($taskMobile as $property => $value) {
            if ($property=='project_salt') {
                $taskDB->setProject($this->em->getRepository('ProjectBundle:Project')->findOneBySalt($value));
            } elseif ($property != 'updated' && $property != 'created' && $property != 'birthdate' && $property != 'deleted') {
                $method = sprintf('set%s', ucwords($property));
                if (method_exists($taskDB, $method)) {
                    $taskDB->$method($value);
                }
            } else {
                if ($value) {
                    $value=str_replace("/", "-", $value);
                    $date=new \DateTime($value);
                    $method = sprintf('set%s', ucwords($property));
                    if (method_exists($taskDB, $method)) {
                        $taskDB->$method($date);
                    }
                }
            }
        }
        $this->em->persist($taskDB);
        $this->em->flush();
    }
    
    private function saveSprint($sprintMobile, $sprintDB)
    {
        foreach ($sprintMobile as $property => $value) {
            if ($property=='project_salt') {
                $sprintDB->setProject($this->em->getRepository('ProjectBundle:Project')->findOneBySalt($value));
            } else if ($property != 'updated' && $property != 'created' && $property != 'dateFrom' && $property != 'dateTo') {
                $method = sprintf('set%s', ucwords($property));
                if (method_exists($sprintDB, $method)) {
                    $sprintDB->$method($value);
                }
            } else {
                if ($value) {
                    $value=str_replace("/", "-", $value);
                    $date=new \DateTime($value);
                    $method = sprintf('set%s', ucwords($property));
                    if (method_exists($sprintDB, $method)) {
                        $sprintDB->$method($date);
                    }
                }
            }
        }
        $this->em->persist($sprintDB);
        $this->em->flush();
    }

    private function saveTask($taskMobile, $taskDB)
    {
        foreach ($taskMobile as $property => $value) {
            if ($property=='sprint_salt') {
                $taskDB->setSprint($this->em->getRepository('SprintBundle:Sprint')->findOneBySalt($value));
            } elseif ($property=='category_name') {
                $taskDB->setCategory($this->em->getRepository('TaskBundle:Category')->findOneByName($value));
            } elseif ($property != 'updated' && $property != 'created' && $property != 'birthdate' && $property != 'deleted') {
                $method = sprintf('set%s', ucwords($property));
                if (method_exists($taskDB, $method)) {
                    $taskDB->$method($value);
                }
            } else {
                if ($value) {
                    $value=str_replace("/", "-", $value);
                    $date=new \DateTime($value);
                    $method = sprintf('set%s', ucwords($property));
                    if (method_exists($taskDB, $method)) {
                        $taskDB->$method($date);
                    }
                }
            }
        }
        $this->em->persist($taskDB);
        $this->em->flush();
    }

    private function saveCategory($categoryMobile, $categoryDB)
    {
        foreach ($categoryMobile as $property => $value) {
            if ($property != 'updated' && $property != 'created' && $property != 'deleted') {
                $method = sprintf('set%s', ucwords($property));
                if (method_exists($categoryDB, $method)) {
                    $categoryDB->$method($value);
                }
            } else {
                if ($value) {
                    $date=new \DateTime($value);
                    $method = sprintf('set%s', ucwords($property));
                    if (method_exists($categoryDB, $method)) {
                        $categoryDB->$method($date);
                    }
                }
            }
        }
        $this->em->persist($categoryDB);
        $this->em->flush();
    }

}

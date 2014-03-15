<?php
namespace LogbookREST\UserBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use LogbookREST\LogBundle\Entity\Log;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use LogbookREST\UserBundle\Entity\User;
use JMS\Serializer\Annotation\Expose;
/**
 * @ORM\Entity()
 */

class ApiUser extends User
{
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="LogbookREST\LogBundle\Entity\Log", mappedBy="user", cascade={"persist", "merge", "remove"})
     * @Expose
     */
    private $logs;
    
    public function __construct()
    {
        $this->logs = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getEntries()
    {
        return $this->logs;
    }
    
    public function setEntries(ArrayCollection $logs)
    {
        $this->logs = $logs;
    }
    
    public function addLog(Log $log)
    {
        $this->logs->add($log);
    }
}

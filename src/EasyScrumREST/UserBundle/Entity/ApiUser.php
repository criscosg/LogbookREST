<?php
namespace EasyScrumREST\UserBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use EasyScrumREST\SprintBundle\Entity\Log;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use EasyScrumREST\UserBundle\Entity\User;
use JMS\Serializer\Annotation\Expose;
/**
 * @ORM\Entity()
 */

class ApiUser extends User
{
    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="users")
     * @Expose
     */
    private $company;
    
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }
    
    public function getCompany()
    {
        return $this->company;
    }
}

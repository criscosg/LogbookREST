<?php

namespace EasyScrumREST\ImageBundle\Entity;

use EasyScrumREST\UserBundle\Entity\ApiUser;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use EasyScrumREST\ImageBundle\Util\FileHelper;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use EasyScrumREST\TaskBundle\Entity\Task;

/**
 * @ORM\Table()
 * @ORM\Entity()
 * @ExclusionPolicy("all")
 */
class ImageProfile extends Image
{
    protected $subdirectory = "images/user";
    protected $maxImages = 1;

    /**
     * @var ApiUser $user
     *
     * @ORM\OneToOne(targetEntity="EasyScrumREST\UserBundle\Entity\ApiUser", inversedBy="profileImage")
     */
    protected $user;

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(ApiUser $user)
    {
        $this->user = $user;
    }
}

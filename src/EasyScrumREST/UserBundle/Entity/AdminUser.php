<?php
namespace EasyScrumREST\UserBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 *
 * @ORM\Entity()
 */

class AdminUser extends User
{
    public function getRoles()
    {
        return array('ROLE_ADMIN_USER');
    }
}
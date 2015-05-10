<?php
namespace EasyScrumREST\UserBundle\Entity;
use Doctrine\Tests\DBAL\Types\IntegerTest;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\ExecutionContextInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"admin"="AdminUser", "user"="ApiUser"})
 * @DoctrineAssert\UniqueEntity("email")
 * @UniqueEntity("email")
 * @ExclusionPolicy("all")
 */
abstract class User implements UserInterface, \Serializable, EquatableInterface
{
    const AUTH_SALT = "GykT1K0IJjo";
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @Expose
     * */
    protected $id;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    protected $modified;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Expose
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Expose
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     * @Assert\Email()
     * @Expose
     * */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 6)
     * */
    protected $password;

    /**
     * @var string salt
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     * @Expose
     */
    protected $salt;

    /**
     * @var date $registeredDate
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $created;

    public function getId()
    {
        return $this->id;
    }

    public function getModified()
    {
        return $this->modified;
    }

    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function __toString()
    {
        if($this->getName() && $this->getLastName())
            return $this->getName() . " ". $this->getLastName();
        
        return $this->getEmail();
    }

    public function isEqualTo(
            \Symfony\Component\Security\Core\User\UserInterface $user)
    {
        return $this->getEmail() == $user->getEmail();
    }

    public function eraseCredentials()
    {
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function serialize()
    {
        return serialize(array($this->id, $this->password, $this->email));
    }

    public function unserialize($serialized)
    {
        list($this->id, $this->password, $this->email) = unserialize(
                $serialized);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

}

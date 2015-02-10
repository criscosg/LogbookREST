<?php
namespace EasyScrumREST\ImageBundle\Tests\Entity;

use EasyScrumREST\ImageBundle\Entity\ImageProfile;

use Doctrine\Common\Collections\ArrayCollection as Collection;
use EasyScrumREST\ImageBundle\Entity\ImageThumbnail;
use EasyScrumREST\ImageBundle\Entity\ImageHorse;
use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class ImageTest extends CustomTestCase
{
    protected function setUp()
    {
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/api-user.yml");
    }

    public function testImageHorse()
    {
        list ($user, $imageProfile) = $this->createUserImage();
        $this->assertEquals($user, $imageProfile->getUser());
        $this->assertNotNull(0, $user->getProfileImage());
    }

    private function createUserImage()
    {
        $user = $this->entityManager->getRepository('UserBundle:ApiUser')->findOneById(1);
        $imageProfile = new ImageProfile();
        $imageProfile->setUser($user);
        $imageProfile->setImage('01.jpg');
        $user->setProfileImage($imageProfile);
        $this->entityManager->persist($user);
        $this->entityManager->persist($imageProfile);
        $this->entityManager->flush();

        return array($user, $imageProfile);
    }
}
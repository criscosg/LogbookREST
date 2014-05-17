<?php
namespace EasyScrumREST\ImageBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection as Collection;
use EasyScrumREST\ImageBundle\Entity\ImageThumbnail;
use EasyScrumREST\ImageBundle\Entity\ImageHorse;
use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class ImageTest extends CustomTestCase
{
    protected function setUp()
    {
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/company.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/veterinary.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/owner.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/horse.yml");
    }

    public function testImageHorse()
    {
        list ($horse, $imageHorse) = $this->createHorseImage();
        $this->assertEquals($horse, $imageHorse->getHorse());
        $this->assertNotEquals(0, $horse->getImages()->count());
    }

    private function createHorseImage()
    {
        $horse = $this->entityManager->getRepository('EasyScrumREST:Horse')->findOneById(1);
        $imageHorse = new ImageHorse();
        $imageHorse->setHorse($horse);
        $imageHorse->setImage('01.jpg');
        $horse->addImage($imageHorse);
        $this->entityManager->persist($horse);
        $this->entityManager->persist($imageHorse);
        $this->entityManager->flush();

        return array($horse, $imageHorse);
    }

    public function testNewThumbnail()
    {
        list ($horse, $imageHorse) = $this->createHorseImage();

        $thumb = new ImageThumbnail();
        $thumb->setImage($imageHorse);
        $thumb->setImageName('01-thumb.jpg');
        $imageHorse->setImageThumbnail($thumb);

        $this->assertEquals($imageHorse, $thumb->getImage());
        $this->assertEquals($thumb, $imageHorse->getImageThumbnail());
    }

    public function testsetUniqueImageCopy()
    {
        list ($user, $imageHorse) = $this->createHorseImage();
        $thumb = new ImageThumbnail();
        $thumb->setImage($imageHorse);
        $thumb->setImageName('01-thumb.jpg');
        $imageHorse->setImageThumbnail($thumb);

        $newThumb = new ImageThumbnail();
        $newThumb->setImage($imageHorse);
        $newThumb->setImageName('01-thumb.jpg');
        $imageHorse->setImageThumbnail($newThumb);
        $this->assertEquals($imageHorse, $newThumb->getImage());
        $this->assertEquals($newThumb, $imageHorse->getImageThumbnail());
        $this->assertEquals(1, $imageHorse->getImageCopies()->count());
    }
}
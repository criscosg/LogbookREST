<?php

namespace LogbookREST\ImageBundle\Test\Util;

use LogbookREST\TestBundle\Classes\CustomTestCase;
use Symfony\Tests\Component\HttpFoundation\File\UploadedFileTest;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use LogbookREST\ImageBundle\Util\FileHelper;
use Doctrine\Common\Collections\ArrayCollection as Collection;

class FileHelperTest extends CustomTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    public function testGetDirectoryNameFromId()
    {
        $this->assertTrue("005" === FileHelper::getDirectoryNameFromId(5));
        $this->assertTrue("025" === FileHelper::getDirectoryNameFromId(25));
        $this->assertTrue("125" === FileHelper::getDirectoryNameFromId(54125));
    }

    public function testIntelligentResizeThumbnail()
    {
        $width = 100;
        $height = 100;
        $originalWidth = 1000;
        $originalHeight = 750;
        $fileTester = new FileHelper();
        list($_width, $_height) = $fileTester->intelligentResizeThumbnail($width, $height, $originalWidth, $originalHeight, false);
        
        $this->assertTrue($_width == 100 && $_height == 75);

        $width = 200;
        $height = 150;
        $originalWidth = 1000;
        $originalHeight = 1500;
        $fileTester = new FileHelper();
        list($_width, $_height) = $fileTester->intelligentResizeThumbnail($width, $height, $originalWidth, $originalHeight, false);

        $this->assertTrue($_width == 100 && $_height == 150);
        
        $width = 200;
        $height = 150;
        $originalWidth = 1000;
        $originalHeight = 750;
        $fileTester = new FileHelper();
        list($_width, $_height) = $fileTester->intelligentResizeThumbnail($width, $height, $originalWidth, $originalHeight, false);

        $this->assertTrue($_width == 200 && $_height == 150);

        $width = 200;
        $height = 150;
        $originalWidth = 200;
        $originalHeight = 200;
        $fileTester = new FileHelper();
        list($_width, $_height) = $fileTester->intelligentResizeThumbnail($width, $height, $originalWidth, $originalHeight, false);
        
        $this->assertTrue($_width == 150 && $_height == 150);

        $width = 200;
        $height = 150;
        $originalWidth = 200;
        $originalHeight = 200;
        $fileTester = new FileHelper();
        list($_width, $_height) = $fileTester->intelligentResizeThumbnail($width, $height, $originalWidth, $originalHeight,true);
        
        $this->assertTrue($_width == 200 && $_height == 200);

        $width = 200;
        $height = 150;
        $originalWidth = 1000;
        $originalHeight = 300;
        $fileTester = new FileHelper();
        list($_width, $_height) = $fileTester->intelligentResizeThumbnail($width, $height, $originalWidth, $originalHeight,true);
        $this->assertTrue($_width == 500 && $_height == 150);
    }
    
    public function testResizeAndSaveImageWithCropAndConvert()
    {
        $width = 150;
        $height = 150;
        $image='01.jpg';
        $sufix='test';
        $directory = $this->container->getParameter('kernel.root_dir').'/../src/LogbookREST/ImageBundle/Tests/File';
        FileHelper::resizeAndSaveImage($image, $width, $height, $directory, $sufix);
        $fileNameThumb=$this->container->getParameter('kernel.root_dir').'/../src/LogbookREST/ImageBundle/Tests/File/01-test.jpg';
        list($thumbWidth, $thumbHeight) = getimagesize($fileNameThumb);
        $this->assertTrue($thumbWidth == 150 && $thumbHeight == 150);

        $fileNameThumbconvert=$this->container->getParameter('kernel.root_dir').'/../src/LogbookREST/ImageBundle/Tests/File/01-test-convert.jpg';
        FileHelper::executeConvert($fileNameThumbconvert, $fileNameThumb, 150, 150, 70);
        list($convertWidth, $convertHeight) = getimagesize($fileNameThumbconvert);
        $this->assertTrue($convertWidth == 150 && $convertHeight == 150);
        FileHelper::ImageCrop($fileNameThumbconvert, 100, 100, 0, 0);
        list($convertWidth, $convertHeight) = getimagesize($fileNameThumbconvert);
        $this->assertTrue($convertWidth == 100 && $convertWidth == 100);
        unlink($fileNameThumb);
        unlink($fileNameThumbconvert);
    }
}

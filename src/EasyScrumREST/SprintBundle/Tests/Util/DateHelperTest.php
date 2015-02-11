<?php
namespace EasyScrumREST\SprintBundle\Test\Util;

use EasyScrumREST\SprintBundle\Util\DateHelper;

use EasyScrumREST\TestBundle\Classes\CustomTestCase;
use Symfony\Tests\Component\HttpFoundation\File\UploadedFileTest;
use EasyScrumREST\ImageBundle\Util\FileHelper;
use Doctrine\Common\Collections\ArrayCollection as Collection;

class DateHelperTest extends CustomTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }
    
    public function testLaboralDays()
    {
        $from=new \DateTime('2014-01-01');
        $to=new \DateTime('2014-01-21');
        
        $days=DateHelper::numberLaborableDays($from, $to);
        $this->assertEquals(14, $days);
    }
}
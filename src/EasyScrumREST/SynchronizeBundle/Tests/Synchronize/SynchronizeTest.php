<?php

namespace EasyScrumREST\SynchronizeBundle\Tests\Synchronize;

use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class SynchronizeTest extends CustomTestCase
{
    protected $mobileDB;

    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/api-user.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/project.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/sprint.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/task.yml");
        $this->mobileDB = array('projects'=>array(array('salt'=>'r0th985hj66o4uh04', 'title'=>'Testeo', 'description'=>'Description', 'updated'=>'2014-02-06 13:55:46','created'=>'2014-02-06 13:55:46')),
                                'sprints'=>array(array('salt'=>'k32g4239847', 'title'=>'Test sprint Sync', 'dateFrom'=>'2014-02-06', 'dateTo'=>'2014-03-08', 'hoursAvailable'=>'30', 'focus' => '50', 'description'=>'Desc sprint', 
                                    'project_salt'=>'r0th985hj66o4uh04', 'updated'=>'2014-02-06 13:55:46','created'=>'2014-02-06 13:55:46', 'planified'=>true)));
    }
    
    public function testSynchronize()
    {
        $veterinary=$this->entityManager->getRepository('UserBundle:ApiUser')->findOneById(1);
        $response = $this->container->get('synchronize')->synchronize($this->mobileDB, $veterinary);
        $this->assertGreaterThan(0, count($response));
        $this->assertEquals('Testeo', $this->entityManager->getRepository('ProjectBundle:Project')->findOneById(2)->getTitle());
        $this->assertEquals('Test sprint Sync', $this->entityManager->getRepository('SprintBundle:Sprint')->findOneById(2)->getTitle());
    }
}
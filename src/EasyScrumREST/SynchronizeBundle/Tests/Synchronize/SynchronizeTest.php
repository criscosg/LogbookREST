<?php

namespace EasyScrumREST\SynchronizeBundle\Tests\Synchronize;

use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class SynchronizeTest extends CustomTestCase
{
    protected $mobileDB;

    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/company.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/veterinary.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/owner.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/horse.yml");
        $this->mobileDB = array('owners'=>array(array('salt'=>'r0th985hj66o4uh04', 'name'=>'Testeo', 'lastName'=>'TestOwner', 'address'=>'Calle Test', 
                                    'email'=>'test@test.com', 'phone'=>912345678, 'mobile'=>612345678, 'modified'=>'2014-02-06 13:55:46','created'=>'2014-02-06 13:55:46')),
                                'horses'=>array(array('salt'=>'k32g4239847', 'name'=>'TestHorse', 'sex'=>'Male', 'birthdate'=>'1987-07-08 00:00:00', 'comment'=>'El caballo', 
                                    'owner_salt'=>'r0th985hj66o4uh04', 'modified'=>'2014-02-06 13:55:46','created'=>'2014-02-06 13:55:46')));
    }
    
    public function testSynchronize()
    {
        $veterinary=$this->entityManager->getRepository('VeterinaryBundle:Veterinary')->findOneById(1);
        $response = $this->container->get('synchronize')->synchronize($this->mobileDB, $veterinary);
        $this->assertGreaterThan(0, count($response));
        $this->assertEquals('Testeo', $this->entityManager->getRepository('OwnerBundle:Owner')->findOneById(2)->getName());
        $this->assertEquals('TestHorse', $this->entityManager->getRepository('HorseBundle:Horse')->findOneById(2)->getName());
    }
}
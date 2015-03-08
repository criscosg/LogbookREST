<?php
namespace IHorseREST\RegisterBundle\Test\Controller;

use IHorseREST\TestBundle\Classes\CustomTestCase;

class ChangepaControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/clinic.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/veterinary.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/recoverPassword.yml");
    }

    public function testJsonPostRegisterNoClinicAction()
    {
        $param = array('email' => 'super@test.com');
        $this->client->request('POST', '/changepas', $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 201);
        $this->assertEquals('super@test.com', $this->entityManager->getRepository('RegisterBundle:RecoverPassword')->findOneById(2)->getEmail());
    }
/*
    public function testJsonPutRegisterNoClinicAction()
    {
        $recover = $this->entityManager->getRepository('RegisterBundle:RecoverPassword')->findOneById(1);
        $param = array('user' => array('password' => '123'), 'salt' => $recover->getSalt());
        $this->client->request('PUT', '/changepa', $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 201);
        $this->assertNotEquals('MNVcqP/TlOHsEHQ8snI1MWDAnSleMHHMrJCnnVGBfPVymDAyORxCv3R0JQ3ymQ5OF1jung5MNlPzeTr0Szt1fw==', $this->entityManager->getRepository('UserBundle:User')->findOneById(1)->getPassword());
    }
*/
}
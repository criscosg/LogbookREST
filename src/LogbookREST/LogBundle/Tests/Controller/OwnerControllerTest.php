<?php
namespace LogbookREST\OwnerBundle\Test\Controller;

use LogbookREST\TestBundle\Classes\CustomTestCase;

class OwnerControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/clinic.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/owner.yml");
    }

    public function testGet()
    {
        $this->client->request('GET', '/security/owners/1');
        $response = $this->client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['id']));
    }

    public function testGetAll()
    {
        $this->client->request('GET', '/security/owners');
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded[0]['id']));
    }

    public function testJsonPostOwnerAction()
    {
        $param = array('owner' => array('name' => 'test', 'last_name' => 'test', 'email' => 'tester@tester.com', 'address' => 'Dortmund', 'clinic' => 1));
        $this->client->request('POST', '/security/owners', $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 201);
        $this->assertEquals('test', $this->entityManager->getRepository('OwnerBundle:Owner')->findOneById(2)->getName());
        $this->assertNotEquals(null, $this->entityManager->getRepository('OwnerBundle:Owner')->findOneById(2)->getSalt());
    }

    public function testJsonPutOwnerAction()
    {
        $param = array('owner'=>array('name' => 'test_one', 'last_name' => 'test', 'email' => 'tester@tester.com', 'address' => 'Dortmund'));
        $this->client->request('PUT', '/security/owners/1', $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 200);
        $this->assertEquals('test_one', $this->entityManager->getRepository('OwnerBundle:Owner')->findOneById(1)->getName());
        $this->assertNotEquals(null, $this->entityManager->getRepository('OwnerBundle:Owner')->findOneById(1)->getSalt());
    }

    public function testJsonPatchOwnerAction()
    {
        $param = array('owner'=>array('address' => 'Dortmund'));
        $this->client->request('PATCH', '/security/owners/1', $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 202);
        $this->assertEquals('Dortmund', $this->entityManager->getRepository('OwnerBundle:Owner')->findOneById(1)->getAddress());
        $this->assertNotEquals(null, $this->entityManager->getRepository('OwnerBundle:Owner')->findOneById(1)->getSalt());
    }

    public function testJsonDeleteOwnerAction()
    {
        $this->client->request('DELETE', '/security/owners/1', array(), array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 202);
        $this->assertNotEquals(null, $this->entityManager->getRepository('OwnerBundle:Owner')->findOneById(1)->getDeleted());
    }

}
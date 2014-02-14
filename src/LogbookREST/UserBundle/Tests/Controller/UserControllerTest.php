<?php
namespace LogbookREST\UserBundle\Test\Controller;

use LogbookREST\UserBundle\Entity\AdminUser;
use LogbookREST\TestBundle\Classes\CustomTestCase;

class UserControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/admin-user.yml");
    }

    public function testGet()
    {
        $this->client->request('GET', 'users/1');
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['user']['id']));
    }

    public function testGetAll()
    {
        $this->client->request('GET', 'users');
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['users'][0]['id']));
    }

    public function testJsonPostPageAction()
    {
        $param=array('admin_user'=>array('email' => 'super@test.com', 'password' => 'conscience', 'name' => 'test'));
        $this->client->request('POST', '/users', $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 201);
        $this->assertEquals('test', $this->entityManager->getRepository('UserBundle:AdminUser')->findOneById(2)->getName());
    }

    public function testJsonPutPageAction()
    {
        $param=array('admin_user'=>array('email' => 'super1@test.com', 'name' => 'test_one'));
        $this->client->request('PUT', '/users/1', $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 200);
        $this->assertEquals('test_one', $this->entityManager->getRepository('UserBundle:AdminUser')->findOneById(1)->getName());
    }

    public function testJsonDeletePageAction()
    {
        $this->client->request('DELETE', '/users/1', array(), array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 202);
        $this->assertEquals(null, $this->entityManager->getRepository('UserBundle:AdminUser')->findOneById(1));
    }
}
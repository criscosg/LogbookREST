<?php
namespace EasyScrumREST\UserBundle\Test\Controller;

use EasyScrumREST\UserBundle\Entity\AdminUser;
use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class UserControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/client.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/admin-user.yml");
    }

    public function testGet()
    {
        $token=$this->loginOauth();
        $this->client->request('GET', '/security/users/1?access_token='.$token);
        $response = $this->client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['id']));
    }

    public function testGetAll()
    {
        $token=$this->loginOauth();
        $this->client->request('GET', '/security/users?access_token='.$token);
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded[0]['id']));
    }

    public function testJsonPostUserAction()
    {
        $token=$this->loginOauth();
        $param=array('admin_user'=>array('email' => 'super@test.com', 'password' => 'conscience', 'name' => 'test'));
        $this->client->request('POST', '/security/users?access_token='.$token, $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 201);
        $this->assertEquals('test', $this->entityManager->getRepository('UserBundle:AdminUser')->findOneById(2)->getName());
    }

    public function testJsonPutUserAction()
    {
        $token=$this->loginOauth();
        $param=array('admin_user'=>array('email' => 'jiminy@cricket.com', 'name' => 'test_one'));
        $this->client->request('PUT', '/security/users/1?access_token='.$token, $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 200);
        $this->assertEquals('Pepe', $this->entityManager->getRepository('UserBundle:AdminUser')->findOneById(1)->getName());
    }

    public function testJsonDeleteUserAction()
    {
        $token=$this->loginOauth();
        $this->client->request('DELETE', '/security/users/1?access_token='.$token, array(), array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 202);
        $this->assertEquals(null, $this->entityManager->getRepository('UserBundle:AdminUser')->findOneById(1));
    }
}
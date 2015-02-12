<?php
namespace EasyScrumREST\SprintBundle\Test\Controller;

use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class MessageRestControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/client.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/api-user.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/messages.yml");
    }

    public function testGetAll()
    {
        $token=$this->loginOauth();
        $this->client->request('GET', '/security/messages?access_token='.$token);
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 200);
        $response = $this->client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded[0]['text']));
        $this->assertEquals('Hola que tal', $decoded[0]['text']);
    }
    
    public function testGet()
    {
        $token=$this->loginOauth();
        $this->client->request('GET', '/security/messages/1?access_token='.$token);
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 200);
        $response = $this->client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['text']));
        $this->assertEquals('Hola que tal', $decoded['text']);
    }
    
    public function testJsonPostMessageAction()
    {
        $token=$this->loginOauth();
        $param=array('sprint'=>array('text' => 'Rest test message'));
        $this->client->request('POST', '/security/messages?access_token='.$token, $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 201);
        $this->assertNotEquals(null, $this->entityManager->getRepository('MessageBundle:Message')->findOneById(2));
    }
    
    public function testJsonPutSprintAction()
    {
        $token=$this->loginOauth();
        $param=array('sprint'=>array('text' => 'Rest test put message'));
        $this->client->request('PUT', '/security/messages/1?access_token='.$token, $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 204);
        $this->assertNotEquals(null, $this->entityManager->getRepository('MessageBundle:Message')->findOneById(1));
    }
    
    public function testJsonDeleteSprintAction()
    {
        $token=$this->loginOauth();
        $this->client->request('DELETE', '/security/messages/1?access_token='.$token, array(), array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 202);
        $this->assertEquals(null, $this->entityManager->getRepository('SprintBundle:Sprint')->findOneById(1));
    }
}
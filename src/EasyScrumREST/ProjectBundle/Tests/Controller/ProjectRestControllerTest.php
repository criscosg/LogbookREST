<?php
namespace EasyScrumREST\SprintBundle\Test\Controller;

use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class ProjectRestControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/client.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/api-user.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/project.yml");
    }

    public function testGetAll()
    {
        $token=$this->loginOauth();
        $this->client->request('GET', '/security/projects?access_token='.$token);
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 200);
        $response = $this->client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded[0]['title']));
        $this->assertEquals('Proyecto test', $decoded[0]['title']);
    }
    
    public function testGet()
    {
        $token=$this->loginOauth();
        $this->client->request('GET', '/security/projects/xasdasdf34?access_token='.$token);
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 200);
        $response = $this->client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['title']));
        $this->assertEquals('Proyecto test', $decoded['title']);
    }
    
    public function testJsonPostProjectAction()
    {
        $token=$this->loginOauth();
        $param=array('project'=>array('title' => 'Rest test project', 'description' => 'Rest test description sprint', 'salt'=>'xxxxxxxxx', 'owner'=>'cf902b71f391a2a973cdb32ae3129655'));
        $this->client->request('POST', '/security/projects?access_token='.$token, $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 201);
        $this->assertNotEquals(null, $this->entityManager->getRepository('ProjectBundle:Project')->findOneById(2));
    }
    
    public function testJsonPutProjectAction()
    {
        $token=$this->loginOauth();
        $param=array('project'=>array('title' => 'Rest test project', 'description' => 'Rest test description sprint', 'salt'=>'xxxxxxxxx', 'owner'=>'cf902b71f391a2a973cdb32ae3129655'));        
        $this->client->request('PUT', '/security/projects/xasdasdf34?access_token='.$token, $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 204);
        $this->assertNotEquals(null, $this->entityManager->getRepository('ProjectBundle:Project')->findOneById(1));
    }
    
    public function testJsonDeleteSprintAction()
    {
        $token=$this->loginOauth();
        $this->client->request('DELETE', '/security/projects/xasdasdf34?access_token='.$token, array(), array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 202);
        $this->assertEquals(null, $this->entityManager->getRepository('ProjectBundle:Project')->findOneById(1));
    }
}
<?php
namespace EasyScrumREST\SprintBundle\Test\Controller;

use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class SprintRestControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/client.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/api-user.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/project.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/sprint.yml");
    }

    public function testGetAll()
    {
        $token=$this->loginOauth();
        $this->client->request('GET', '/security/sprints?access_token='.$token);
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 200);
        $response = $this->client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded[0]['title']));
        $this->assertEquals('Sprint test', $decoded[0]['title']);
    }
    
    public function testGet()
    {
        $token=$this->loginOauth();
        $this->client->request('GET', '/security/sprints/1?access_token='.$token);
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 200);
        $response = $this->client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['title']));
        $this->assertEquals('Sprint test', $decoded['title']);
    }
    
    public function testJsonPostSprintAction()
    {
        $token=$this->loginOauth();
        $param=array('sprint'=>array('title' => 'Rest test sprint', 'description' => 'Rest test description sprint', 'dateFrom' => '06/02/2015','dateTo' => '06/03/2015', 'project' => 'xasdasdf34', 'hoursAvailable' => '30', 'focus'=>'60', 
                'tasks'=>array(array('title' => 'Rest test task', 'description' => 'Rest test description task in sprint', 'hours' => '4', 'priority' => 'P0'))));
        $this->client->request('POST', '/security/sprints?access_token='.$token, $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 201);
        $this->assertNotEquals(null, $this->entityManager->getRepository('SprintBundle:Sprint')->findOneById(2));
    }
    
    public function testJsonPutSprintAction()
    {
        $token=$this->loginOauth();
        $param=array('sprint'=>array('title' => 'Rest test sprint', 'description' => 'Rest test description sprint', 'dateFrom' => '06/02/2015','dateTo' => '06/03/2015', 'project' => 'xasdasdf34', 'hoursAvailable' => '30', 'focus'=>'60'));
        $this->client->request('PUT', '/security/sprints/1?access_token='.$token, $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 204);
        $this->assertNotEquals(null, $this->entityManager->getRepository('SprintBundle:Sprint')->findOneById(1));
    }
    
    public function testJsonDeleteSprintAction()
    {
        $token=$this->loginOauth();
        $this->client->request('DELETE', '/security/sprints/1?access_token='.$token, array(), array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 202);
        $this->assertEquals(null, $this->entityManager->getRepository('SprintBundle:Sprint')->findOneById(1));
    }
}
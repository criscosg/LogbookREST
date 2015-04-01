<?php
namespace EasyScrumREST\TaskBundle\Test\Controller;

use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class TaskRestControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/client.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/api-user.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/project.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/sprint.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/task.yml");
    }

    public function testGetAll()
    {
        $token=$this->loginOauth();
        $this->client->request('GET', '/security/tasks?access_token='.$token);
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 200);
        $response = $this->client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded[0]['title']));
        $this->assertEquals('Task test finalized', $decoded[0]['title']);
    }
    
    public function testGet()
    {
        $token=$this->loginOauth();
        $this->client->request('GET', '/security/tasks/hl348724?access_token='.$token);
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 200);
        $response = $this->client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['title']));
        $this->assertEquals('Task test finalized', $decoded['title']);
    }
    
    public function testJsonPostTaskAction()
    {
        $token=$this->loginOauth();
        $param=array('task'=>array('title' => 'Rest test task', 'description' => 'Rest test description task', 'hours' => '4','sprint' => 'lkjadlksjaie', 'priority' => 'P0'));
        $this->client->request('POST', '/security/tasks?access_token='.$token, $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 201);
        $this->assertNotEquals(null, $this->entityManager->getRepository('TaskBundle:Task')->findOneById(2));
    }
    
    public function testJsonPutTaskAction()
    {
        $token=$this->loginOauth();
        $param=array('task'=>array('title' => 'Rest test task', 'description' => 'Rest test description task', 'hours' => '4','sprint' => 'lkjadlksjaie', 'priority' => 'P0'));
        $this->client->request('PUT', '/security/tasks/hl348724?access_token='.$token, $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 202);
        $this->assertNotEquals(null, $this->entityManager->getRepository('TaskBundle:Task')->findOneById(1));
    }
    
    public function testJsonDeleteTaskAction()
    {
        $token=$this->loginOauth();
        $this->client->request('DELETE', '/security/tasks/hl348724?access_token='.$token, array(), array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 202);
        $this->assertEquals(null, $this->entityManager->getRepository('TaskBundle:Task')->findOneById(1));
    }

    public function testJsonHoursTaskAction()
    {
        $token=$this->loginOauth();
        $param = array('hours'=>array("hoursSpent"=>"4","hoursEnd"=>"4"));
        $this->client->request('POST', '/security/task-hours/hl348724?access_token='.$token, $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 202);
        $this->assertNotEquals(null, $this->entityManager->getRepository('TaskBundle:HoursSpent')->findOneById(1));
    }
}
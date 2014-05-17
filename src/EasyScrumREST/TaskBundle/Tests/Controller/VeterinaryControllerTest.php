<?php
namespace EasyScrumREST\TaskBundle\Test\Controller;

use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class TaskControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/company.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/Task.yml");
    }

    public function testGet()
    {
        $this->client->request('GET', 'veterinaries/1');
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['Task']['id']));
    }

    public function testGetAll()
    {
        $this->client->request('GET', 'veterinaries');
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['veterinaries'][0]['id']));
    }

    public function testJsonPostTaskAction()
    {
        $param=array('Task'=>array('email' => 'super@test.com', 'password' => 'conscience', 'name' => 'test', 'company'=>'1'));
        $this->client->request('POST', '/veterinaries', $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 201);
        $this->assertEquals('test', $this->entityManager->getRepository('EasyScrumREST:Task')->findOneById(2)->getName());
    }

    public function testJsonPutTaskAction()
    {
        $param=array('Task'=>array('name' => 'test_one'));
        $this->client->request('PUT', '/veterinaries/1', $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 200);
        $this->assertEquals('test_one', $this->entityManager->getRepository('EasyScrumREST:Task')->findOneById(1)->getName());
    }

    public function testJsonDeleteTaskAction()
    {
        $this->client->request('DELETE', '/veterinaries/1', array(), array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 202);
        $this->assertEquals(null, $this->entityManager->getRepository('EasyScrumREST:Task')->findOneById(1));
    }
}
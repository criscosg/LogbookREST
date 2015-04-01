<?php
namespace EasyScrumREST\TaskBundle\Test\Controller;

use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class TaskControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/api-user.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/project.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/sprint.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/task.yml");
    }

    public function testNewTask()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'task/new/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        
        $form = $crawler->selectButton('Save')
        ->form(array('task[title]' => 'new task test',
                'task[description]' => 'description test',
                'task[priority]' => 'P0',
                'task[hours]'=>'5'));
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        
        $text = utf8_encode("new task test");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

    public function testEditTask()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'task/edit/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    
        $form = $crawler->selectButton('Save')
        ->form(array('task[title]' => 'edit task test',
                    'task[description]' => 'edit description test',
                    'task[priority]' => 'P2',
                    'task[hours]'=>'5'));
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
    
        $text = utf8_encode("edit task test");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

    public function testDelete()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'task/delete/1');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("Task test");
        $this->assertEquals(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

    public function testHoursTask()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'task/hours/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        
        $form = $crawler->selectButton('Save')
        ->form(array('hours[hoursSpent]' => '3',
                'hours[hoursEnd]' => '2'));
        $crawler = $this->client->submit($form);
        $response=json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(isset($response['text']));
    }

    public function testMoveOnProcessTask()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'task/to-on-process/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
    
    public function testMoveDoneTask()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'task/to-done/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
    
    public function testDropTask()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'task/to-undone/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("Task test");
        $this->assertEquals(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
    
    public function testMoveTask()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'task/to-todo/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
    
}

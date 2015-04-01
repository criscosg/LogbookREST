<?php
namespace EasyScrumREST\TaskBundle\Test\Controller;

use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class UrgencyControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/api-user.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/project.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/sprint.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/urgency.yml");
    }

    public function testNewUrgency()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'urgency/new/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        
        $form = $crawler->selectButton('Save')
        ->form(array('urgency[title]' => 'new urgency test',
                'urgency[description]' => 'description urgency test'));
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        
        $text = utf8_encode("new urgency test");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

    public function testEditUrgency()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'urgency/edit/1/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')
        ->form(array('urgency[title]' => 'edit urgency test',
                    'urgency[description]' => 'edit urgency description test'));
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
    
        $text = utf8_encode("edit urgency test");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

    public function testDelete()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'urgency/delete/1');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("Urgency test");
        $this->assertEquals(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

    public function testHoursTask()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'urgency/hours/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        
        $form = $crawler->selectButton('Save')
        ->form(array('hours[hoursSpent]' => '3',
                'hours[hoursEnd]' => '2'));
        $crawler = $this->client->submit($form);
        $response=json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(isset($response['text']));
    }

    public function testMoveOnProcessUrgency()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'urgency/to-on-process/1/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
    
    public function testMoveDoneUrgency()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'urgency/to-done/1/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
    
    public function testDropUrgency()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'urgency/to-undone/1/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("Urgency test");
        $this->assertEquals(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
    
    public function testMoveUrgency()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'urgency/to-todo/1/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
    
}

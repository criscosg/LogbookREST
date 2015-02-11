<?php
namespace EasyScrumREST\SprintBundle\Test\Controller;

use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class SprintControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/api-user.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/project.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/sprint.yml");
    }

    public function testListSprints()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'sprint/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("Sprint test");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

    public function testNewSprint()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'sprint/first-step');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('next step')
        ->form(array('sprint_first[project]' => '1',
                'sprint_first[dateFrom]' => '06/02/2015',
                'sprint_first[dateTo]' => '06/03/2015',
                'sprint_first[hoursAvailable]'=>'40',
                'sprint_first[focus]'=>'50'));
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $form = $crawler->selectButton('End planification')
        ->form(array('sprint[title]' => 'Create sprint test',
                'sprint[description]'=>'Description test in sprints'));
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $text = utf8_encode("Create sprint test");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

    public function testEditSprint()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'sprint/edit-sprint/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save sprint')
        ->form(array('sprint_edit[title]' => 'edit sprint test',
                'sprint_edit[description]'=>'Description test in sprints',
                'sprint_edit[dateFrom]' => '06/02/2015',
                'sprint_edit[dateTo]' => '06/03/2015',
                'sprint_edit[hoursAvailable]'=>'40',
                'sprint_edit[focus]'=>'50'));
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $text = utf8_encode("edit sprint test");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
    
    public function testDelete()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'sprint/delete/1');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("Sprint test");
        $this->assertEquals(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
    
    public function testFinalizeSprint()
    {
        parent::loadFixture(__DIR__ . "/../Fixtures/task.yml");
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'sprint/finalize/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("Sprint test");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
    
    public function testSaveHoursSprint()
    {
        parent::loadFixture(__DIR__ . "/../Fixtures/task.yml");
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'sprint/hours/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        
        $form = $crawler->selectButton('Save')
        ->form(array('sprint_hours[hours]' => '4',
                'sprint_hours[date]' => '07/02/2015'));
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        
        $hours = $this->entityManager->getRepository('SprintBundle:HoursSprint')->findOneBy(array('sprint'=>'1'));
        $this->assertNotEquals(null, $hours);
    }

}
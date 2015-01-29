<?php
namespace EasyScrumREST\ProjectBundle\Test\Controller;

use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class ProjectControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/api-user.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/project.yml");
    }

    public function testListProjects()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'project/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("Proyecto test");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
    
    public function testSeeProject()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'project/see/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("Proyecto test");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

    public function testNewProject()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'project/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')
        ->form(array('project[title]' => 'creacion de proyectos',
                'project[description]' => 'Esta es la descripción'));
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $text = utf8_encode("creacion de proyectos");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

    public function testEditProject()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'project/edit/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')
        ->form(array('project[title]' => 'creacion de proyectos',
                'project[description]' => 'Esta es la descripción'));

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $text = utf8_encode("creacion de proyectos");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

    public function testDelete()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'project/delete/1');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("Proyecto test");
        $this->assertEquals(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

    public function testNewBacklog()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'project/backlog-task/create/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    
        $form = $crawler->selectButton('Save')
        ->form(array('backlog[title]' => 'nueva tarea',
                'backlog[description]' => 'Esta es la nueva tarea', 'backlog[priority]'=>10 ));
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $text = utf8_encode("nueva tarea");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
    
    public function testEditBacklog()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'project/backlog-task/edit/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    
        $form = $crawler->selectButton('Save')
        ->form(array('backlog[title]' => 'nueva tarea',
                'backlog[description]' => 'Esta es la nueva tarea', 'backlog[priority]'=>10 ));
        $crawler = $this->client->submit($form);
        $text = utf8_encode("nueva tarea");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

    public function testFinalizeBacklog()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'project/backlog-task/finalize/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("Tarea backlog test");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

    public function testDeleteBacklog()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'project/backlog-task/delete/1');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("Tarea backlog test");
        $this->assertEquals(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
    
    public function testNewIssue()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'project/issue/create/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    
        $form = $crawler->selectButton('Save')
        ->form(array('issue[title]' => 'nueva incidencia',
                'issue[description]' => 'Esta es la nueva incidencia', 'issue[priority]'=>10 ));
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $text = utf8_encode("nueva incidencia");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
    
    public function testEditIssue()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'project/issue/edit/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    
        $form = $crawler->selectButton('Save')
        ->form(array('issue[title]' => 'nueva incidencia modificada',
                'issue[description]' => 'Esta es la nueva incidencia', 'issue[priority]'=>10 ));
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $text = utf8_encode('nueva incidencia modificada');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
    
    public function testFinalizeIssue()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'project/issue/finalize/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("Tarea issue test");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
}
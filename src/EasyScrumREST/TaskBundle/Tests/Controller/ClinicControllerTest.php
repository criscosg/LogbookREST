<?php
namespace EasyScrumREST\TaskBundle\Test\Controller;

use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class CompanyControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/company.yml");
    }

    public function testGet()
    {
        $this->client->request('GET', 'companys/1');
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['company']['id']));
    }

    public function testGetAll()
    {
        $this->client->request('GET', 'companys');
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['companys'][0]['id']));
    }

    public function testJsonPostCompanyAction()
    {
        $param=array('company'=>array('name' => 'test'));
        $this->client->request('POST', '/companys', $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 201);
        $this->assertEquals('test', $this->entityManager->getRepository('EasyScrumREST:Company')->findOneById(2)->getName());
    }

    public function testJsonPutCompanyAction()
    {
        $param=array('company'=>array('name' => 'test_one'));
        $this->client->request('PUT', '/companys/1', $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 204);
        $this->assertEquals('test_one', $this->entityManager->getRepository('EasyScrumREST:Company')->findOneById(1)->getName());
    }

    public function testJsonDeleteCompanyAction()
    {
        $this->client->request('DELETE', '/companys/1', array(), array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 202);
        $this->assertEquals(null, $this->entityManager->getRepository('EasyScrumREST:Company')->findOneById(1));
    }
}
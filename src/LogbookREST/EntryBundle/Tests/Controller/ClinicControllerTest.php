<?php
namespace LogbookREST\EntryBundle\Test\Controller;

use LogbookREST\TestBundle\Classes\CustomTestCase;

class ClinicControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/clinic.yml");
    }

    public function testGet()
    {
        $this->client->request('GET', 'clinics/1');
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['clinic']['id']));
    }

    public function testGetAll()
    {
        $this->client->request('GET', 'clinics');
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['clinics'][0]['id']));
    }

    public function testJsonPostClinicAction()
    {
        $param=array('clinic'=>array('name' => 'test'));
        $this->client->request('POST', '/clinics', $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 201);
        $this->assertEquals('test', $this->entityManager->getRepository('EntryBundle:Clinic')->findOneById(2)->getName());
    }

    public function testJsonPutClinicAction()
    {
        $param=array('clinic'=>array('name' => 'test_one'));
        $this->client->request('PUT', '/clinics/1', $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 204);
        $this->assertEquals('test_one', $this->entityManager->getRepository('EntryBundle:Clinic')->findOneById(1)->getName());
    }

    public function testJsonDeleteClinicAction()
    {
        $this->client->request('DELETE', '/clinics/1', array(), array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 202);
        $this->assertEquals(null, $this->entityManager->getRepository('EntryBundle:Clinic')->findOneById(1));
    }
}
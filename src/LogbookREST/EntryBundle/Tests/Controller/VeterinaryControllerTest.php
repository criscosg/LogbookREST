<?php
namespace LogbookREST\EntryBundle\Test\Controller;

use LogbookREST\TestBundle\Classes\CustomTestCase;

class EntryControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/clinic.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/Entry.yml");
    }

    public function testGet()
    {
        $this->client->request('GET', 'veterinaries/1');
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['Entry']['id']));
    }

    public function testGetAll()
    {
        $this->client->request('GET', 'veterinaries');
        $response = $this->client->getResponse();
        $content = $response->getContent();

        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['veterinaries'][0]['id']));
    }

    public function testJsonPostEntryAction()
    {
        $param=array('Entry'=>array('email' => 'super@test.com', 'password' => 'conscience', 'name' => 'test', 'clinic'=>'1'));
        $this->client->request('POST', '/veterinaries', $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 201);
        $this->assertEquals('test', $this->entityManager->getRepository('EntryBundle:Entry')->findOneById(2)->getName());
    }

    public function testJsonPutEntryAction()
    {
        $param=array('Entry'=>array('name' => 'test_one'));
        $this->client->request('PUT', '/veterinaries/1', $param, array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 200);
        $this->assertEquals('test_one', $this->entityManager->getRepository('EntryBundle:Entry')->findOneById(1)->getName());
    }

    public function testJsonDeleteEntryAction()
    {
        $this->client->request('DELETE', '/veterinaries/1', array(), array(), array('CONTENT_TYPE' => 'application/json'));
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 202);
        $this->assertEquals(null, $this->entityManager->getRepository('EntryBundle:Entry')->findOneById(1));
    }
}
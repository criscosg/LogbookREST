<?php
namespace EasyScrumREST\MessageBundle\Test\Controller;

use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class MessageControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/api-user.yml");
        parent::loadFixture(__DIR__ . "/../Fixtures/messages.yml");
    }

    public function testMessages()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'message/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("Hola que tal");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

    public function testSendMessage()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'message/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Escribir mensaje')
        ->form(array('message[text]' => 'creacion de mensajes a prueba'));
        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertNotEquals(false, strpos($decoded['message'], 'creacion de mensajes a prueba'));
    }

    public function testMessagesAsync()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'message/async');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("Hola que tal");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }

}
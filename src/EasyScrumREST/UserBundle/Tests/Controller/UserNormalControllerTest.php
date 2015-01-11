<?php
namespace EasyScrumREST\UserBundle\Test\Controller;

use EasyScrumREST\UserBundle\Entity\AdminUser;
use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class UserNormalControllerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/api-user.yml");
    }
    
    public function testRegister()
    {
        $crawler = $this->client->request('GET', 'register');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Register')
        ->form(array('api_user[email]' => 'test@iventiajobs.com',
                     'api_user[company][name]' => 'test',
                     'api_user[password][first]'=>'123456',
                     'api_user[password][second]'=>'123456'));

        $crawler = $this->client->submit($form);
        $user = $this->entityManager->getRepository('UserBundle:ApiUser')->findOneBy(array('email'=>'test@iventiajobs.com'));
        $this->assertNotEquals(null, $user);
    }

    public function testLogin()
    {
        $crawler = $this->login();
        $text = utf8_encode("Home dashboard");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
    
    public function testListUser()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'users/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("jiminy@cricket.com");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
    
    public function testNewApiUser()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'users/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        
        $form = $crawler->selectButton('Save')
        ->form(array('api_user[email]' => 'team-member@iventiajobs.com',
                'api_user[name]' => 'test',
                'api_user[lastName]' => 'member',
                'api_user[password]'=>'123456',
                'api_user[roles]'=>'ROLE_TEAM'));
        
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $text = utf8_encode("team-member@iventiajobs.com");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
    
    public function testEditApiUser()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'users/edit/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    
        $form = $crawler->selectButton('Save')
        ->form(array('api_user[email]' => 'jiminy@cricket.com',
                'api_user[name]' => 'test',
                'api_user[lastName]' => 'master',
                'api_user[password]'=>'123456',
                'api_user[roles]'=>'ROLE_SCRUM_MASTER'));
    
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $text = utf8_encode("master");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
    
    public function testEditProfile()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'users/profile');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    
        $form = $crawler->selectButton('Save')
        ->form(array('profile[email]' => 'jiminy@cricket.com',
                'profile[name]' => 'test',
                'profile[lastName]' => 'master',
                'profile[password]' => '123456',
                'profile[color]'=>'#ff0000',
                'profile[profileImage][file]'=>''));
    
        $crawler = $this->client->submit($form);
        $text = utf8_encode("master");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
    
    public function testSettings()
    {
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'users/settings');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    
        $form = $crawler->selectButton('Save')
        ->form(array('settings[name]' => 'test-riscos',
                'settings[pricePerHour]' => '20',
                'settings[hoursPerDay]'=>'8'));
    
        $crawler = $this->client->submit($form);
        $settings = $this->entityManager->getRepository('UserBundle:Company')->findOneBy(array('name'=>'test-riscos'));
        $this->assertNotEquals(null, $settings);
    }
    
    public function testDelete()
    {
        parent::loadFixture(__DIR__ . "/../Fixtures/api-team-member.yml");
        $crawler = $this->login();
        $crawler = $this->client->request('GET', 'users/delete/2');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $text = utf8_encode("member@cricket.com");
        $this->assertEquals(0, $crawler->filter('html:contains("' . $text . '")')->count());
    }
}
<?php

namespace EasyScrumREST\UserBundle\Test\Handler;

use EasyScrumREST\UserBundle\Entity\AdminUser;
use EasyScrumREST\TestBundle\Classes\CustomTestCase;

class UserHandlerTest extends CustomTestCase
{
    protected function setUp(){
        parent::setUp();
        parent::loadFixture(__DIR__ . "/../Fixtures/admin-user.yml");
    }

    public function testGet()
    {
        $user=$this->container->get('user.handler')->get(1);
        $this->assertEquals('Pepe', $user->getName());
    }

}
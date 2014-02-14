<?php

namespace LogbookREST\UserBundle\Test\Handler;

use LogbookREST\UserBundle\Entity\AdminUser;
use LogbookREST\TestBundle\Classes\CustomTestCase;

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
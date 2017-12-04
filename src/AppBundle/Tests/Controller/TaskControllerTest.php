<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $this->client->request('GET', '/api/tasks.json');

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $this->logIn();

        $this->client->request('GET', '/api/tasks.json');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testStore()
    {
        $this->client->request('POST', '/api/tasks.json');

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $this->logIn();

        $this->client->request('POST', '/api/tasks.json');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdate()
    {
        $this->client->request('PUT', '/api/tasks/1.json');

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $this->logIn();

        $this->client->request('PUT', '/api/tasks/1.json');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testShow()
    {
        $this->client->request('GET', '/api/tasks/1.json');

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $this->logIn();

        $this->client->request('GET', '/api/tasks/1.json');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testDestroy()
    {
        $this->client->request('DELETE', '/api/tasks/1.json');

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $this->logIn();

        $this->client->request('DELETE', '/api/tasks/1.json');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}

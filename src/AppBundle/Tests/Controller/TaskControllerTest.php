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
        $i = 1;
        $successResponse = '/\{"response":\{"id":(\d+)\}\}/';

        $this->client->request('POST', '/api/tasks.json');
        $this->assertEquals(401, $this->client->getResponse()
            ->getStatusCode());

        $this->logIn();

        $this->client->request('POST', '/api/tasks.json', ['title' => 'Test #'.($i++),]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp($successResponse, $this->client->getResponse()->getContent());

        preg_match($successResponse, $this->client->getResponse()->getContent(), $matches);
        $this->client->request('POST', '/api/tasks.json', ['title' => 'Test #'.($i++), 'parent' => $matches[1],]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/\{"response":\{"id":\d+\}\}/', $this->client->getResponse()->getContent());

        $this->client->request('POST', '/api/tasks.json', ['parent' => $matches[1],]);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());

        $this->client->request('POST', '/api/tasks.json', ['title' => 'Test #'.($i++), 'parent' => 1e6,]);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
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

<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Tests\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    protected $task_number = 1;

    public function testIndex()
    {
        $this->client->request('GET', '/api/tasks.json');
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $this->logIn();

        $this->client->request('GET', '/api/tasks.json');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = json_decode($this->client->getResponse()->getContent());
        $n = count($response->response) - 1;
        $this->client->request('GET', '/api/tasks.json', ['limit' => $n]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals($n, count($response->response));

        $second_id = $response->response[1]->id;
        $this->client->request('GET', '/api/tasks.json', ['offset' => 1, 'limit' => $n]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals($second_id, $response->response[0]->id);
    }

    public function testStore()
    {
        $i = 1;
        $successResponse = '/\{"response":\{"id":(\d+)\}\}/';

        $this->client->request('POST', '/api/tasks.json');
        $this->assertEquals(401, $this->client->getResponse()
            ->getStatusCode());

        $this->logIn();

        $this->client->request('POST', '/api/tasks.json', ['title' => 'Test #'.($i++)]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp($successResponse, $this->client->getResponse()->getContent());

        preg_match($successResponse, $this->client->getResponse()->getContent(), $matches);
        $this->client->request('POST', '/api/tasks.json', ['title' => 'Test #'.($i++), 'parent' => $matches[1]]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/\{"response":\{"id":\d+\}\}/', $this->client->getResponse()->getContent());

        $this->client->request('POST', '/api/tasks.json', ['parent' => $matches[1]]);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());

        $this->client->request('POST', '/api/tasks.json', ['title' => 'Test #'.($i++), 'parent' => 1e6]);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdate()
    {
        $this->client->request('PUT', '/api/tasks/1.json');
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $this->logIn();

        $task_1 = $this->createTask();
        $task_2 = $this->createTask($task_1->getId());

        $this->client->request('PUT', '/api/tasks/'.$task_2.'.json', [
            'title' => $task_2->getTitle(),
            'parent' => null,
        ]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertNull($response->response->parent_id);

        $this->client->request('PUT', '/api/tasks/'.$task_2.'.json', [
            'title' => $task_2->getTitle(),
            'parent' => $task_1->getId(),
        ]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertNotNull($response->response->parent_id);
    }

    public function testShow()
    {
        $this->client->request('GET', '/api/tasks/1.json');
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $this->logIn();

        $tast = $this->createTask();

        $this->client->request('GET', '/api/tasks/'.$tast.'.json');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/api/tasks/'.(1e6).'.json');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testDestroy()
    {
        $this->client->request('DELETE', '/api/tasks/1.json');
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $this->logIn();

        $task = $this->createTask();

        $this->client->request('DELETE', '/api/tasks/'.$task.'.json');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('DELETE', '/api/tasks/'.(1e6).'.json');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());

        $manager = $this->container->get('doctrine')->getManager();

        $username = 'other_username';
        $user = $manager->getRepository(User::class)
            ->findOneByUsername($username);

        if (is_null($user)) {
            $user = new User();
            $user->setUsername($username);
        }

        $user->setPassword('other_password');
        $manager->persist($user);
        $manager->flush();

        $task = new Task();
        $task->setTitle('Test #'.($this->task_number++));
        $task->setUser($user);
        $manager->persist($task);
        $manager->flush();

        $this->client->request('DELETE', '/api/tasks/'.$task.'.json');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function createTask($parent_id = null)
    {
        $manager = $this->container->get('doctrine')->getManager();
        $this->logIn();

        $params = [
            'title' => 'Test #'.($this->task_number++),
            'parent' => $parent_id,
        ];

        $this->client->request('POST', '/api/tasks.json', $params);
        $response = json_decode($this->client->getResponse()->getContent());

        return $manager->getRepository(Task::class)->find($response->response->id);
    }
}

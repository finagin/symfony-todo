<?php

namespace AppBundle\Tests;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as WebTestCaseBase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class WebTestCase extends WebTestCaseBase
{
    protected $client = null;
    protected $container = null;

    public function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
        $this->client = static::createClient();
    }

    private function getUser()
    {
        $manager = $this->container->get('doctrine')->getManager();

        return $manager->getRepository(User::class)->findOneByUsername('username');
    }

    protected function logIn()
    {
        $firewallName = 'api_area';
        $session = $this->client->getContainer()->get('session');
        $securityContext = $this->client->getContainer()->get('security.context');

        $token = new UsernamePasswordToken($this->getUser(), null, $firewallName, ['ROLE_USER']);
        $securityContext->setToken($token);

        $session->set('_security_'.$firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()
            ->set($cookie);
    }
}

<?php

namespace AppBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as WebTestCaseBase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class WebTestCase extends WebTestCaseBase
{
    protected $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    protected function logIn()
    {
        $session = $this->client->getContainer()
            ->get('session');

        // the firewall context defaults to the firewall name
        $firewallContext = 'api_area';

        $token = new UsernamePasswordToken('username', null, $firewallContext);
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()
            ->set($cookie);
    }
}

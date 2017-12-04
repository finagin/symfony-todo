<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateUserCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new CreateUserCommand());

        $command = $application->find('uip-test:todo:user');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'login' => 'username',
            'password' => 'pa$$word',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains('Success!', $output);
    }
}

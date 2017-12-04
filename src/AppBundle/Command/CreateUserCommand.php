<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\User;

class CreateUserCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('uip-test:todo:user')
            ->addArgument('login', InputArgument::REQUIRED, 'Логин пользователя')
            ->addArgument('password', InputArgument::REQUIRED, 'Пароль пользователя')
            ->setDescription('Создание/обновление пользователя')
            ->setHelp('Команда для создания/обновления пользователя');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $login = $input->getArgument('login');
        $password = $input->getArgument('password');

        $manager = $this->getContainer()
            ->get('doctrine')
            ->getManager();

        $user = $manager->getRepository(User::class)
            ->findOneByUsername($login);

        if (is_null($user)) {
            $user = new User();
            $user->setUsername($login);
        }

        $user->setPassword($password);
        $manager->persist($user);
        $manager->flush();

        $output->writeln('<info>Success!</info>');
    }
}

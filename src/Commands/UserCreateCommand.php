<?php

namespace Hi\Commands;

use Exception;
use Hi\Auth\UserInterface;
use Hi\Auth\UserProviderInterface;
use Hi\Storage\EntityStorageInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class UserCreateCommand extends Command
{
    private ContainerInterface $container;

    public function withContainer(ContainerInterface $container):static
    {
        $this->container = $container;
        return $this;
    }

    protected function configure(): void
    {
        $this->setName('user:create')
            ->setDescription('Create user')
            ->setHelp("Create a new user");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = new QuestionHelper();

        $question = new Question('Please enter the email address: ');
        $email = $helper->ask($input, $output, $question);

        $question = new Question('Please enter the password: ');
        $question->setHidden(true);
        $password = $helper->ask($input, $output, $question);

        if (empty($password)) {
            throw new Exception("Email and password are required");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.\n");
        }

        /** @var UserProviderInterface $userProvider */
        $userProvider = $this->container->get(UserProviderInterface::class);

        $user = $userProvider->create();
        $user->setClientIdentifier($email);
        $user->setClientSecret($password);
        $userProvider->persist($user);

        return 0;
    }

}

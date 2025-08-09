<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\InstagramPostMessage;

class InstagramPostCommand extends Command
{
    protected static $defaultName = 'app:instagram-post';
    //run example: php bin/console app:instagram-post
    public function __construct(private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:instagram-post')
            ->setDescription('Creates Instagram post creation message');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bus->dispatch(new InstagramPostMessage(1, 'Title', 'image.jpg'));
        $output->writeln('Created instagram post creation message');
        return Command::SUCCESS;
    }
}

<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\InstagramQuoteGenerator;

class PostGenerateCommand extends Command
{
    protected static $defaultName = 'app:post-generate';
    //run example: php bin/console app:instagram-post
    public function __construct(private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:post-generate')
            ->setDescription('Generates post images');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $generator = new InstagramQuoteGenerator();
        // Simple - random quote and gradient
        $imagePath = $generator->createQuoteImage(
            null,
            null,
            'public/images/instagram_post_' . time() . '.png',
            [
                'font_size' => 72,
                'font_path' => 'public/fonts/RobotoSlab.ttf',
                'border_width' => 25
            ]
        );

        $output->writeln('Generated post image:' . $imagePath);
        return Command::SUCCESS;
    }
}

<?php

namespace App\MessageHandler;

use App\Message\InstagramPostMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Doctrine\DBAL\Connection;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class InstagramLogHandler
{
    public function __construct(private Connection $connection, private MailerInterface $mailer, private KernelInterface $kernel, private LoggerInterface $logger)
    {
    }

    public function __invoke(InstagramPostMessage $message)
    {
        $this->logger->info("LogHandler post ID: " . $message->getId() . " title:" . $message->getTitle() . " url:" . $message->getImageFilename());
    }
}
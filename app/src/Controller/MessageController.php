<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Connection;
use Exception;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Envelope;
use App\Message\InstagramPostMessage;

final class MessageController extends AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/messages', name: 'app_messages')]
    public function messages(Connection $connection): Response
    {
        $sql = 'SELECT * FROM messenger_messages ORDER BY created_at DESC';
        $result = $connection->executeQuery($sql);
        $messages = $result->fetchAllAssociative();

        $processedMessages = array_map(function ($message) {
            return $this->processMessage($message);
        }, $messages);

        return new JsonResponse($processedMessages);
    }

    #[Route('/monitor', name: 'app_monitor')]
    public function monitor(Connection $connection): Response
    {
        return $this->render('api-data-table.html.twig');
    }

    private function processMessage(array $encodedEnvelope): array
    {
        try {
            // Deserialize the body into the correct message class
            $envelope = $this->serializer->decode($encodedEnvelope);
        } catch (\Throwable $throwable) {
            throw new Exception($throwable->getMessage(), 0, $throwable);
        }

        $message = $envelope->getMessage();

        if (!$message instanceof InstagramPostMessage) {
            return ['error' => 'Not an InstagramPostMessage'];
        }

        // If your message class has getter methods, use them
        try {
            return [
                'id' => method_exists($message, 'getId') ? $message->getId() : null,
                'title' => method_exists($message, 'getTitle') ? $message->getTitle() : null,
                'image_filename' => method_exists($message, 'getImageFilename') ? $message->getImageFilename() : null,
                'queue' => $encodedEnvelope['queue_name'],
                'created_at' => $encodedEnvelope['created_at'],
            ];
        } catch (Exception $e) {
            return ['error' => 'error message'];
        }
    }

}

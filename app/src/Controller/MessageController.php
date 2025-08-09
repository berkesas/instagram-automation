<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Connection;

final class MessageController extends AbstractController
{
    #[Route('/messages', name: 'app_messages')]
    public function messages(Connection $connection): Response
    {
        $sql = 'SELECT id, queue_name, created_at, delivered_at FROM messenger_messages ORDER BY created_at DESC';
        $result = $connection->executeQuery($sql);
        $messages = $result->fetchAllAssociative();

        return new JsonResponse($messages);
    }

    #[Route('/monitor', name: 'app_monitor')]
    public function monitor(Connection $connection): Response
    {
        return $this->render('api-data-table.html.twig');
    }
}

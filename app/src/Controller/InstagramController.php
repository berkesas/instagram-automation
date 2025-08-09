<?php

/*
This is a helper InstagramController which includes useful function for testing and subscribing to Instagram API
*/

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Message\SystemEventMessage;
use App\Enum\EventType;
use App\Enum\EntityType;
use Symfony\Bundle\SecurityBundle\Security;

class InstagramController extends AbstractController
{
    public function __construct(private MessageBusInterface $bus, private EntityManagerInterface $entityManager, private Security $security)
    {
    }

    #[Route('/api/instagram/verify', name: 'app_api_instagram_verify', methods: ['GET'])]
    public function verify(Request $request): Response
    {
        $hub_mode = $request->query->get('hub_mode');
        $hub_verify_token = $request->query->get('hub_verify_token');
        $hub_challenge = $request->query->get('hub_challenge');

        $result = null;

        $receivedData = [
            'mode' => $hub_mode,
            'hub_verify_token' => $hub_verify_token,
            'hub_challenge' => $hub_challenge
        ];

        if ($hub_mode == 'subscribe' && $hub_verify_token == '1') {
            $result = new Response($hub_challenge);
        } else {
            $result = new Response(null, Response::HTTP_BAD_REQUEST);
        }

        return $result;
    }

    #[Route('/api/instagram/update', name: 'app_api_instagram_update', methods: ['POST'])]
    public function update(Request $request): Response
    {
        $payload = $request->getContent();
        // Example usage
        // echo $payload;
        // // $payload = file_get_contents('php://input'); // Capture raw POST data
        // $appSecret = 'your_app_secret'; // Replace with your App Secret
        // $headerSignature = $_SERVER['HTTP_X_HUB_SIGNATURE_256']; // Get header from request

        // if (!$this->validateInstagramXHubSignature($payload, $appSecret, $headerSignature)) {
        //     return new Response("Invalid request!", Response::HTTP_BAD_REQUEST);
        // }
        //process updates here
        $result = "ok";
        return new Response($result, Response::HTTP_OK);
    }

    function validateInstagramXHubSignature($payload, $appSecret, $headerSignature)
    {
        // Generate the SHA256 hash using the app secret and payload
        $expectedSignature = hash_hmac('sha256', $payload, $appSecret);
        echo $expectedSignature;

        // Extract the actual signature from the header (everything after 'sha256=')
        $receivedSignature = str_replace('sha256=', '', $headerSignature);

        // Compare the generated signature with the received signature
        return hash_equals($expectedSignature, $receivedSignature);
    }

    #[Route('/api/instagram/subscribe', name: 'app_api_instagram_subscribe', methods: ['GET'])]
    public function subscribeToApps()
    {
        $client = HttpClient::create();
        $url = "https://" . $_ENV['INSTAGRAM_API_HOST'] . "/" . $_ENV['INSTAGRAM_ACCOUNT_ID'] . "/subscribed_apps";

        $response = $client->request('POST', $url, [
            'query' => [
                'subscribed_fields' => 'comments,live_comments,',
                'access_token' => $_ENV['INSTAGRAM_ACCESS_TOKEN']
            ]
        ]);

        return new Response($response->getContent());
    }

    /* This is a mock function to test if other parts of your app work */

    #[Route('/api/instagram/media', name: 'app_api_instagram_media', methods: ['POST'])]
    public function createContainerMock()
    {
        $result = [
            "id" => "1234654654654"
        ];

        return new JsonResponse($result);
    }

    /* This is a mock function to test if other parts of your app work */

    #[Route('/api/instagram/media_publish', name: 'app_api_instagram_publish_media', methods: ['POST'])]
    public function publishMediaFake()
    {
        $result = [
            "id" => "654654654654"
        ];

        return new JsonResponse($result);
    }
}
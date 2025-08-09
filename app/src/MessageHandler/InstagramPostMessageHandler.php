<?php

namespace App\MessageHandler;

use App\Message\InstagramPostMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Doctrine\DBAL\Connection;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use \Exception;

#[AsMessageHandler]
class InstagramPostMessageHandler
{
    public function __construct(private Connection $connection, private MailerInterface $mailer, private KernelInterface $kernel, private LoggerInterface $logger)
    {
    }

    public function __invoke(InstagramPostMessage $message)
    {
        $this->logger->info("Sending post ID: " . $message->getId() . " title:" . $message->getTitle() . " url:" . $message->getImageUrl());
        // $this->createPost();
    }

    private function sendEmailNotification(array $data)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('mailer@scinforma.com', 'Scinforma'))
            ->to($_ENV['REPORT_RECIPIENT_EMAIL'])
            ->subject('My Instagram Post')
            ->htmlTemplate('reports/en.instagram_post_email.html.twig')
            ->context([
                'data' => $data
            ]);

        $this->mailer->send($email);
    }

    public function getTodayFiles(string $directory): array
    {
        $today = date("Y-m-d"); // Get today's date in "YYYY-MM-DD" format
        $finder = new Finder();
        $finder->files()->in($directory)->name("{$today}*.png");

        $fileNames = [];
        foreach ($finder as $file) {
            $fileNames[] = $file->getFilename();
        }

        return $fileNames;
    }

    private function createPost(array $files)
    {
        $client = HttpClient::create();
        //https://graph.instagram.com/v23.0/17841474741088400/media
        $container_url = $_ENV['BACKEND_HOST'] . "/api/instagram/media";
        $publish_url = $_ENV['BACKEND_HOST'] . "/api/instagram/media_publish";

        // YOU CAN USE THIS PART when you feel your other functions are working properly
        // $container_url = "https://" . $_ENV['INSTAGRAM_API_HOST'] . "/v23.0/" . $_ENV['INSTAGRAM_ACCOUNT_ID'] . "/media";
        // $publish_url = "https://" . $_ENV['INSTAGRAM_API_HOST'] . "/v23.0/" . $_ENV['INSTAGRAM_ACCOUNT_ID'] . "/media_publish";
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $_ENV['INSTAGRAM_ACCESS_TOKEN']
        ];
        $image_url = $_ENV['BACKEND_HOST'] . "/public/images/" . $files[0];

        $today = date("Y-m-d");
        $caption = "My comments\n\n #good #nice";

        $this->logger->info($container_url);
        $this->logger->info($files[0]);

        $container_json = [
            "caption" => $caption,
            "image_url" => $image_url
        ];

        $this->logger->info(implode(",", $container_json));

        $response = $client->request('POST', $container_url, [
            'headers' => $headers,
            'json' => $container_json
        ]);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            $this->logger->info("Created container for: " . $files[0]);
            $data = $response->toArray();
            if (isset($data["id"])) {
                $media_id = $data["id"];
                $response = $client->request('POST', $publish_url, [
                    'headers' => $headers,
                    'json' => [
                        "creation_id" => $media_id
                    ]
                ]);
                if ($response->getStatusCode() === Response::HTTP_OK) {
                    $this->logger->info("Published post id: " . $media_id);
                    $data = [
                        "date" => $today,
                        "url" => $image_url
                    ];
                    // $this->sendEmailNotification($data);
                } else {
                    $this->logger->error("Could not publish: " . $media_id);
                    throw new Exception("Could not publish: " . $media_id);
                }
            }
        } else {
            $this->logger->error("Container creation failed: " . $files[0]);
            throw new Exception("Container creation failed: " . $files[0]);
        }
    }
}
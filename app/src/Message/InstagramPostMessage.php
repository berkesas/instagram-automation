<?php

namespace App\Message;

class InstagramPostMessage
{
    public function __construct(private int $id, private string $title, private string $image_url)
    {

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getImageUrl(): string
    {
        return $this->image_url;
    }
}
<?php

namespace App\Dto;

use App\Kafka\PostEventType;

class PostEventPayloadDTO
{
    public ?string $id;
    public ?string $username;
    public ?string $imageUrl;
    public ?string $caption;
    public PostEventType $eventType;
    public ?string $lastModifiedBy;
    public ?string $createdAt;
    public ?string $updatedAt;

    /**
     * @param string|null $id
     * @param string|null $username
     * @param string|null $imageUrl
     * @param string|null $caption
     * @param PostEventType $eventType
     * @param string|null $lastModifiedBy
     * @param string|null $createdAt
     * @param string|null $updatedAt
     */
    public function __construct(
        ?string $id, ?string $username,
        ?string $imageUrl, ?string $caption,
        PostEventType $eventType, ?string $lastModifiedBy,
        ?string $createdAt, ?string $updatedAt
    )
    {
        $this->id = $id;
        $this->username = $username;
        $this->imageUrl = $imageUrl;
        $this->caption = $caption;
        $this->eventType = $eventType;
        $this->lastModifiedBy = $lastModifiedBy;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }


}

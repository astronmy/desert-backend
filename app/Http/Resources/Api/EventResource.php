<?php

namespace App\Http\Resources\Api;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Event
 */
class EventResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Event $event */
        $event = $this->resource;

        if (! $event->relationLoaded('images')) {
            $event->load('images');
        }

        return [
            'id' => $event->id,
            'name' => $event->name,
            'event_date' => $event->end_date->toDateString(),
            'init_date' => $event->init_date->toDateString(),
            'end_date' => $event->end_date->toDateString(),
            'type' => $event->type->value,
            'description' => $event->description,
            'short_description' => $event->short_description,
            'host' => $event->host,
            'image_url' => $event->imageUrl(),
            'mobile_image_url' => $event->mobileImageUrl(),
            'gallery' => $event->galleryUrls(),
        ];
    }
}

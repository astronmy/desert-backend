<?php

namespace App\Services\Events;

use App\Models\Event;
use App\Models\EventImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PersistEventMediaService
{
    /**
     * @param  array{description?: string|null, short_description?: string|null, host?: string|null, name: string, init_date: string, end_date: string, type: string}  $attributes
     * @param  array{image?: UploadedFile|null, mobile_image?: UploadedFile|null, gallery?: list<UploadedFile>|null}  $files
     */
    public function create(array $attributes, array $files = []): Event
    {
        return DB::transaction(function () use ($attributes, $files) {
            $event = Event::create($attributes);

            $this->storeMainImages($event, $files['image'] ?? null, $files['mobile_image'] ?? null);
            $this->appendGallery($event, $files['gallery'] ?? []);

            return $event->refresh()->load('images');
        });
    }

    /**
     * @param  array{description?: string|null, short_description?: string|null, host?: string|null, name: string, init_date: string, end_date: string, type: string}  $attributes
     * @param  array{
     *     image?: UploadedFile|null,
     *     mobile_image?: UploadedFile|null,
     *     gallery?: list<UploadedFile>|null,
     *     remove_image?: bool,
     *     remove_mobile_image?: bool,
     *     delete_gallery?: list<int>|null
     * }  $files
     */
    public function update(Event $event, array $attributes, array $files = []): Event
    {
        return DB::transaction(function () use ($event, $attributes, $files) {
            $event->update($attributes);

            if (! empty($files['remove_image'])) {
                $this->deletePath($event->image_path);
                $event->update(['image_path' => null]);
            }

            if (! empty($files['remove_mobile_image'])) {
                $this->deletePath($event->mobile_image_path);
                $event->update(['mobile_image_path' => null]);
            }

            $this->storeMainImages(
                $event,
                $files['image'] ?? null,
                $files['mobile_image'] ?? null
            );

            $this->deleteGalleryImages($event, $files['delete_gallery'] ?? []);
            $this->appendGallery($event, $files['gallery'] ?? []);

            return $event->refresh()->load('images');
        });
    }

    public function delete(Event $event): void
    {
        DB::transaction(function () use ($event) {
            $event->load('images');
            $event->deleteStoredFiles();
            $event->delete();
        });
    }

    private function storeMainImages(Event $event, ?UploadedFile $image, ?UploadedFile $mobileImage): void
    {
        $updates = [];

        if ($image) {
            $this->deletePath($event->image_path);
            $updates['image_path'] = $image->store('events/'.$event->id, 'public');
        }

        if ($mobileImage) {
            $this->deletePath($event->mobile_image_path);
            $updates['mobile_image_path'] = $mobileImage->store('events/'.$event->id, 'public');
        }

        if ($updates !== []) {
            $event->update($updates);
        }
    }

    /**
     * @param  list<UploadedFile>|null  $files
     */
    private function appendGallery(Event $event, ?array $files): void
    {
        if ($files === null || $files === []) {
            return;
        }

        $currentCount = $event->images()->count();
        $remaining = max(0, 12 - $currentCount);

        if ($remaining === 0) {
            return;
        }

        $sortOrder = (int) $event->images()->max('sort_order');

        foreach (array_slice($files, 0, $remaining) as $file) {
            $sortOrder++;
            EventImage::create([
                'event_id' => $event->id,
                'path' => $file->store('events/'.$event->id.'/gallery', 'public'),
                'sort_order' => $sortOrder,
            ]);
        }
    }

    /**
     * @param  list<int>|null  $ids
     */
    private function deleteGalleryImages(Event $event, ?array $ids): void
    {
        if ($ids === null || $ids === []) {
            return;
        }

        $images = $event->images()->whereIn('id', $ids)->get();

        foreach ($images as $image) {
            $this->deletePath($image->path);
            $image->delete();
        }
    }

    private function deletePath(?string $path): void
    {
        if (! $path) {
            return;
        }

        $disk = Storage::disk('public');
        if ($disk->exists($path)) {
            $disk->delete($path);
        }
    }
}

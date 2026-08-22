<?php

namespace App\Models;

use App\Enums\EventType;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'init_date',
    'end_date',
    'type',
    'description',
    'short_description',
    'host',
    'image_path',
    'mobile_image_path',
])]
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'init_date' => 'date',
            'end_date' => 'date',
            'type' => EventType::class,
        ];
    }

    public function clients(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function accesses(): HasMany
    {
        return $this->hasMany(Access::class);
    }

    public function registrationLinks(): HasMany
    {
        return $this->hasMany(EventRegistrationLink::class);
    }

    public function registrationLinkHits(): HasMany
    {
        return $this->hasMany(RegistrationLinkHit::class);
    }

    public function activeRegistrationLink(): ?EventRegistrationLink
    {
        return $this->registrationLinks()->active()->latest('id')->first();
    }

    public function images(): HasMany
    {
        return $this->hasMany(EventImage::class)->orderBy('sort_order');
    }

    public function guests(): BelongsToMany
    {
        return $this->belongsToMany(Guest::class, 'invitations')
            ->withPivot(['id', 'code', 'status', 'selfie_path', 'confirmed_at'])
            ->withTimestamps();
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return Storage::disk('public')->url($this->image_path);
    }

    public function mobileImageUrl(): ?string
    {
        if (! $this->mobile_image_path) {
            return null;
        }

        return Storage::disk('public')->url($this->mobile_image_path);
    }

    /**
     * @return list<string>
     */
    public function galleryUrls(): array
    {
        return $this->images
            ->map(fn (EventImage $image) => $image->url())
            ->filter()
            ->values()
            ->all();
    }

    public function deleteStoredFiles(): void
    {
        $disk = Storage::disk('public');

        foreach ([$this->image_path, $this->mobile_image_path] as $path) {
            if ($path && $disk->exists($path)) {
                $disk->delete($path);
            }
        }

        foreach ($this->images as $image) {
            if ($image->path && $disk->exists($image->path)) {
                $disk->delete($image->path);
            }
        }

        $directory = 'events/'.$this->id;
        if ($disk->exists($directory)) {
            $disk->deleteDirectory($directory);
        }
    }
}

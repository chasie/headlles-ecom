<?php

namespace HeadlessEcom\Base\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasMedia
{
    use InteractsWithMedia;

    /**
     * Relationship for thumbnail.
     */
    public function thumbnail(): MorphOne
    {
        return $this->morphOne(config('media-library.media_model'), 'model')
            ->where('custom_properties->primary', true);
    }

    public function registerMediaCollections(): void
    {
        $fallbackUrl = config('headless-ecom.media.fallback.url');
        $fallbackPath = config('headless-ecom.media.fallback.path');

        $collection = $this->addMediaCollection('images');

        if ($fallbackUrl) {
            $collection = $collection->useFallbackUrl($fallbackUrl);
        }

        if ($fallbackPath) {
            $collection = $collection->useFallbackPath($fallbackPath);
        }
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $conversionClasses = config('headless-ecom.media.conversions', []);

        foreach ($conversionClasses as $classname) {
            app($classname)->apply($this);
        }

        // Add a conversion that the hub uses...
        $this->addMediaConversion('small')
            ->fit(Fit::Fill, 300, 300)
            ->sharpen(10)
            ->keepOriginalImageFormat();
    }
}

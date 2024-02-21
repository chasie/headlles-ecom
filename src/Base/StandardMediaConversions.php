<?php

namespace HeadlessEcom\Base;

use Spatie\Image\Enums\Fit;

class StandardMediaConversions
{
    public function apply(BaseModel $model)
    {
        $conversions = [
            'zoom' => [
                'width' => 500,
                'height' => 500,
            ],
            'large' => [
                'width' => 800,
                'height' => 800,
            ],
            'medium' => [
                'width' => 500,
                'height' => 500,
            ],
        ];

        foreach ($conversions as $key => $conversion) {
            $model->addMediaConversion($key)
                ->fit(
                    Fit::Fill,
                    $conversion['width'],
                    $conversion['height']
                )->keepOriginalImageFormat();
        }
    }
}

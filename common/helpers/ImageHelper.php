<?php
namespace common\helpers;

use Yii;

class ImageHelper
{
    public static function getFileThumbnailUrl($filePath, $options = [])
    {
        $defaultOptions = [
            'thumbnail' => [
                'width' => 200,
                'height' => 200,
            ],
            'placeholder' => [
                'width' => 200,
                'height' => 200
            ]
        ];
        $options = ArrayHelper::merge($defaultOptions, $options);
        $thumb = Yii::$app->thumbnail->url($filePath, $options);
        return $thumb;
    }
}
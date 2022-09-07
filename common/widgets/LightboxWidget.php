<?php
namespace common\widgets;

use yii\bootstrap\Html;
use common\helpers\ArrayHelper;
use yeesoft\lightbox\Lightbox;

class LightboxWidget extends Lightbox
{
    public function run() {
        $content = '';

        foreach ($this->items as $item) {
            if (!isset($item['thumb']) || !isset($item['image'])) {
                continue;
            }
            $linkOptions['data-title'] = isset($item['title']) ? $item['title'] : '';
            if (isset($item['group'])) {
                $linkOptions['data-lightbox'] = $item['group'];
            } else {
                $linkOptions['data-lightbox'] = 'image-' . uniqid();
            }

            $linkOptions = ArrayHelper::merge($linkOptions, $this->linkOptions);

            if (isset($item['options']))
            {
                $linkOptions = ArrayHelper::merge($linkOptions, $item['options']);
            }
            $image = Html::img($item['thumb'], $this->imageOptions);
            $content .= Html::a($image, $item['image'], $linkOptions);

        }

        return $content;
    }


}
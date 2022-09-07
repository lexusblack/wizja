<?php
namespace common\widgets;

use yii\bootstrap\Html;
use yii\base\Widget;
use Yii;

class PageSizeWidget extends Widget
{
    public $options = [];

    public function run()
    {
        parent::run();
        $selection = \Yii::$app->request->get('per-page', 20);

        $content = '';
        $content = Html::beginTag('div', $this->options);
        $content .= Html::dropDownList('per-page', $selection, [
                2=>2,
                5=>5,
                10=>10,
                15=>15,
                20=>20,
                50=>50,
                100=>100,
            ], [
                'class'=>'grid-filters form-control'
        ]);

//        $content .= Html::label(Yii::t('app', 'Ilość na stronę'));
        $content .= Html::endTag('div');
        return $content;

    }
}
<?php
namespace common\assets;

class JuiAsset extends \yii\jui\JuiAsset
{
    public function init()
    {
        parent::init();
        $this->js[] = \Yii::getAlias('@web/../js/conflict.js');
    }
}
<?php
namespace common\widgets;

use common\assets\PannellumAsset;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\web\NotFoundHttpException;

class PannellumWidget extends Widget
{
    public $width = '100%';
    public $height = '600px';
    public $cssClass = 'pannellum-viewer';

    public $imageFileUrl;

    public function init()
    {
        parent::init();
        if ($this->imageFileUrl === null)
        {
            throw new NotFoundHttpException(Yii::t('app', 'Błąd konfiguracji panoramy.'));
        }
        PannellumAsset::register($this->view);
    }
    
    public function run()
    {
        parent::run();
        $this->view->registerJs('
pannellum.viewer("'.$this->id.'", {
    type: "equirectangular",
    panorama: "'.$this->imageFileUrl.'",
    autoLoad: true,
});
');
        return Html::tag('div', '', [
            'id'=>$this->id,
            'class' => $this->cssClass,
            'style' => 'width: '.$this->width.'; height: '.$this->height.';',
        ]);
    }


}

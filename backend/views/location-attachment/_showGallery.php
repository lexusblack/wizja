<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yeesoft\lightbox\Lightbox;

/* @var $this yii\web\View */
/* @var $model common\models\LocationAttachment */

$gallery = $model->location->getGalleryAttachments();
$items = [];
foreach ($gallery as $image)
{
    /* @var $image \common\models\LocationAttachment */
    $items[] = [
        'group'=>'gal-'.$image->location_id,
        'thumb' => $image->getFileThumbUrl(),
        'image' => $image->getFileUrl(),
        'title' => $image->filename,
        'options'=>[
            'data-id'=>$image->id,
            ]
    ];
}
?>

<?php
echo \common\widgets\LightboxWidget::widget([
    'options' => [
        'fadeDuration' => '2000',
        'albumLabel' => Yii::t('app', "Zdjęcie %1 z %2"),
    ],
    'linkOptions' => ['class' => 'pull-left', 'style'=>'width:300px; display:block'],
    'imageOptions' => ['class' => 'thumbnail img-responsive'],
    'items' => $items,

]);
?>


<?php
$this->registerJs('
    $("a[data-id=\"'.$model->id.'\"]").trigger("click");
');
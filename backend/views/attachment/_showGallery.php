<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yeesoft\lightbox\Lightbox;

/* @var $this yii\web\View */
/* @var $model common\models\Attachment */

$gallery = $model->event->getGalleryAttachments();
$items = [];
foreach ($gallery as $image)
{
    /* @var $image \common\models\LocationAttachment */
    $items[] = [
        'group'=>'gal-'.$image->event_id,
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
        'albumLabel' => "Image %1 of %2",
    ],
    'linkOptions' => ['class' => 'pull-left', 'style'=>'width:300px; display:block'],
    'imageOptions' => ['class' => 'thumbnail img-responsive'],
    'items' => $items,

]);
?>


</div>

<?php
$this->registerJs('
    $("a[data-id=\"'.$model->id.'\"]").trigger("click");
');
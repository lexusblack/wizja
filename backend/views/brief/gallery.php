<?php

use yii\bootstrap\Html;
use yii\widgets\DetailView;
use yeesoft\lightbox\Lightbox;
use yii\imagine\Image;


/* @var $this yii\web\View */
/* @var $image common\models\Attachment */
/* @var $model \common\models\Event */

$items = [];
foreach ($models as $image)
{
//    Image::thumbnail($image->getFilePath(), 100, 100)->show('jpg');

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

$this->title = Yii::t('app', 'Galeria').' '.$model->name;
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['event/view', 'id'=>$model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div>
<?php echo Html::a(Html::icon('arrow-left').' '.Yii::t('app', 'Wydarzenie').': '.$model->name, ['event/view', 'id'=>$model->id, '#'=>'tab-attachment'], ['class'=>'btn btn-warning']); ?>
</div>

<?php
echo \common\widgets\LightboxWidget::widget([
    'options' => [
        'fadeDuration' => '2000',
        'albumLabel' => Yii::t('app', 'Obraz %1 z %2'),
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
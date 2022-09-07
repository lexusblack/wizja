<?php

use yii\bootstrap\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LocationAttachment */

$this->title = Yii::t('app', 'Podgląd');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Miejsce').': '.$model->location->name, 'url' => ['location/view', 'id'=>$model->location_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-attachment-view">

    <p>
        <?= Html::a(Html::icon('arrow-left').' '.  Yii::t('app', 'Cofnij'), ['location/view', 'id'=>$model->location_id], ['class' => 'btn btn-default']) ?>
        <?php if ($showTools == true): ?>
            <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [

                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>

    <?php echo $this->render($view, ['model'=>$model]); ?>

</div>

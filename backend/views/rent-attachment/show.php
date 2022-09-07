<?php

use yii\bootstrap\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Attachment */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Załączniki'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-attachment-view">

    <p>
        <?= Html::a( Html::icon('arrow-left') . ' '.Yii::t('app', 'Wydarzenie').': '.$model->event->name, ['event/view', 'id'=>$model->event_id, '#'=>'tab-attachment'], ['class' => 'btn btn-default']) ?>
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

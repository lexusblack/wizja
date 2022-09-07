<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GearAttachment */

$this->title = Yii::t('app', 'Dodaj Załącznik');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Model sprzętu'), 'url' => ['gear-model/view?id='.$model->gear_model_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-attachment-create">

    <?= $this->render('_batchForm', [
        'model' => $model,
    ]) ?>

</div>

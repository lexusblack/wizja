<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GearAttachment */

$this->title = Yii::t('app', 'Edycja załącznika').': ' . $model->filename;
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="gear-attachment-update">
<h3><?=$this->title?></h3>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

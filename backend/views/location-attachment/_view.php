<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LocationAttachment */

?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'typeLabel',
            'location.displayLabel:text:'.Yii::t('app', 'Miejsce'),
        ],
    ]) ?>


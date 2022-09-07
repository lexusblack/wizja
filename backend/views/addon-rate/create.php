<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AddonRate */

$this->title = Yii::t('app', 'Dodaj stawkę');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Dodatki'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="addon-rate-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

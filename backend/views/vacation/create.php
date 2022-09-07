<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Vacation */

$this->title = Yii::t('app', 'Dodaj urlop');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Urlopy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vacation-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Schedule */

$this->title = Yii::t('app', 'Dodaj Harmonogram');
$this->params['breadcrumbs'][] = ['label' => 'Harmonogramy', 'url' => ['all']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schedule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_type', [
        'model' => $model,
    ]) ?>

</div>

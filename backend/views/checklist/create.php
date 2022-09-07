<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Checklist */

$this->title = Yii::t('app', 'Dodaj');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Checklista'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="checklist-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

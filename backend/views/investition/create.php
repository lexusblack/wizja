<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Investition */

$this->title = Yii::t('app', 'Dodaj inwestycję');
$this->params['breadcrumbs'][] = ['label' =>Yii::t('app', 'Inwestycje'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="investition-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

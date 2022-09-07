<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ExpenseAttachment */

$this->title = Yii::t('app', 'Stwórz załącznik wydatku');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Załęcznik wydatku'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expense-attachment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

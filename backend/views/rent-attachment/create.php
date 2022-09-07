<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Attachment */

$this->title = Yii::t('app', 'Dodaj załącznik');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Załączniki'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attachment-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

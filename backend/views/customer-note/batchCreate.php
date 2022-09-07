<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LocationAttachment */

$this->title = Yii::t('app', 'Dodaj załącznik');
$this->params['breadcrumbs'][] = ['label' => $note->customer->name, 'url' => ['/customer/view', 'id'=>$note->customer->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-note-attachment-create">

    <?= $this->render('_batchForm', [
        'model' => $model,
        'note' => $note
    ]) ?>

</div>

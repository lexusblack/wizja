<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Attachment */

$this->title = Yii::t('app', 'Dodaj Umowę');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-attachment-create">

    <?= $this->render('_batchForm', [
        'model' => $model,
    ]) ?>

</div>

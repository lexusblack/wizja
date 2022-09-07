<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GearAttachment */

$this->title = Yii::t('app', 'Dodaj załącznik');

?>
<div class="gear-attachment-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
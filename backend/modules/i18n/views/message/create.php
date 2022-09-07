<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Message */

$this->title = Yii::t('app', 'Dodaj wiadomość');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wiadomości'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

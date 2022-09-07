<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Contact */

$this->title = Yii::t('app', 'Dodaj kontakt');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Kontakty'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

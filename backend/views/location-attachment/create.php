<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LocationAttachment */

$this->title = Yii::t('app', 'Dodaj załącnzik lokacji');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Załącznik lokacji'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-attachment-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

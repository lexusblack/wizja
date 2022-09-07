<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\OuterGear */

$this->title = Yii::t('app', 'Dodaj sprzęt');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Magazyn zewnętrzny'), 'url' => ['/outer-warehouse/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

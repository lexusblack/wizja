<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Rent */

$this->title =  Yii::t('app', 'Dodaj');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wypożyczenie'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rent-create">

    <?= $this->render('_formc', [
        'model' => $model,
        'schema_change_possible' => true
    ]) ?>

</div>

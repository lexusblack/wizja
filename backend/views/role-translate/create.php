<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GearTranslate */

$this->title = Yii::t('app', 'Dodaj tłumaczenie');
$this->params['breadcrumbs'][] = ['label' => $gear->name, 'url' => ['/gear/view', 'id'=>$gear->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-translate-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

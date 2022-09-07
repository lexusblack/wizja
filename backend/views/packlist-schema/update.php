<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PacklistSchema */

$this->title = Yii::t('app', 'Edytuj schemat');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Schemat grup sprzętowych'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="packlist-schema-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Gear */

$this->title = Yii::t('app', 'Usuwanie ').$model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'modele'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-create">

    <?= $this->render('_form3', [
        'model' => $model
    ]) ?>

</div>

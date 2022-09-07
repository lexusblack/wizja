<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Skill */

$this->title = Yii::t('app', 'Dodaj Umiejętność');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Umiejętności'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="skill-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

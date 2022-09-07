<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LocationNote */

$this->title = Yii::t('app', 'Stwórz notatkę');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-note-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

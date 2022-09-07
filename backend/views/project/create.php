<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Project */

$this->title = Yii::t('app', 'Stwórz projekt');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Projekty'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
                'schema_change_possible'=>$schema_change_possible
    ]) ?>

</div>

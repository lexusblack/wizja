<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Gear */

$this->title = Yii::t('app', 'Usuwanie ').$model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'modele'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-create">
<?php if ($items>0) { ?>
<div class="alert alert-warning">
<?=Yii::t('app', 'Ten sprzęt posiada egzemplarze. Usuwając model usuniesz również je.')?>
</div>
<?php } ?>
    <?= $this->render('_form2', [
        'model' => $model
    ]) ?>

</div>

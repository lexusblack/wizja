<?php
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $model \common\models\GearItem */
?>

<div class="item">
    <?php echo Html::a($model->gear->name, ['gear/view', 'id'=>$model->gear_id]); ?>
    <?php echo Html::a($model->name, ['gear-item/view', 'id'=>$model->id]); ?>
</div>
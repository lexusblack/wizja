<?php
/* @var $this yii\web\View */
use common\widgets\PlanboardWidget;


$this->title =  Yii::t('app', 'Plan Timeline');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="ibox">
<div class="ibox-content">
    <?php echo PlanboardWidget::widget([
        'userId'=>\Yii::$app->user->id
    ]); ?>
</div>
</div>

<?php



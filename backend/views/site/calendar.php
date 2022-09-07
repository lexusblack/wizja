<?php
/* @var $this yii\web\View */
use common\widgets\CalendarWidget;


$this->title = Yii::t('app', 'Kalendarz');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="ibox">
<div class="ibox-content">
    <?php echo CalendarWidget::widget([
        'userId'=>\Yii::$app->user->id,
        'height'=>500,
        'defaultDate' => $year.'-'.$month.'-01',
        'nextLink' => $nextLink,
        'prevLink' => $prevLink,
        'month' => $month,
        'year' => $year,
    ]); ?>
</div>
</div>
<?php



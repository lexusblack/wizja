<?php
use yii\bootstrap\Modal;
//$content .= ' '.Html::a($btnText, Url::to(['warehouse/custom-dates','itemId'=>$model->id, 'type'=>$type, 'id'=>$event->id]), ['class'=>'btn btn-success btn-xs custom-dates', 'data'=>['item'=>$model->id]]);
Modal::begin([
    'header' => Yii::t('app', 'Daty'),
    'toggleButton' => [
        'label' => $btnText,
        'id'=>'custom_date_range-button-'.$item->id,
        'class'=>'btn btn-xs btn-success',
    ],
]);

echo $this->render('_customDateRange', ['warehouse'=>$warehouse, 'item'=>$item, 'owner'=>$owner]);

Modal::end();
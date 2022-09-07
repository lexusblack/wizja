<?php
/* @var $this \yii\web\View */
use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
?>

<?php
if ($model->id != $activeGroup)
{
    return false;
}
//                if ($model->getGearItems()->count() == 0)
//                {
//                    return false;
//                }
echo GridView::widget([
    'dataProvider' => $gearGroupItemDataProvider,
    'options'=>[
        'class'=>'grid-view grid-view-group-items',
    ],
    'filterModel' => null,
    'columns' => [
        'id',
        'name',
        'number',
        'code',
        'serial',
//                        'status',
        'location',
//                        'test_date',
        'tester',
        'test_status',
//                        'service:ntext',
        'lamp_hours',
        'info:ntext',
        [
            'header'=>'Najbliższe działania',
        ],
        'purchase_price',
        'refund_amount',
        [
            'class' => 'yii\grid\ActionColumn',
            'urlCreator' =>  function($action, $model, $key, $index)
            {
                $params = is_array($key) ? $key : ['id' => (string) $key];
                $params[0] = 'gear-item/' . $action;

                return Url::toRoute($params);
            }
        ],
    ],
]);
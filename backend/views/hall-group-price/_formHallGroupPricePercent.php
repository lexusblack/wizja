<div class="form-group" id="add-hall-group-price-percent">
<?php
use kartik\grid\GridView;
use kartik\builder\TabularForm;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\widgets\Pjax;

$dataProvider = new ArrayDataProvider([
    'allModels' => $row,
    'pagination' => [
        'pageSize' => -1
    ]
]);
echo TabularForm::widget([
    'dataProvider' => $dataProvider,
    'formName' => 'HallGroupPricePercent',
    'checkboxColumn' => false,
    'actionColumn' => false,
    'attributeDefaults' => [
        'type' => TabularForm::INPUT_TEXT,
    ],
    'attributes' => [
        "id" => ['type' => TabularForm::INPUT_HIDDEN, 'columnOptions' => ['hidden'=>true]],
        'day' => ['type' => TabularForm::INPUT_TEXT, 'label' => 'Dzień eventu',],
        'value' => ['type' => TabularForm::INPUT_TEXT, 'label' => 'Procent dnia pierwszego',],
        'del' => [
            'type' => 'raw',
            'label' => '',
            'value' => function($model, $key) {
                return
                    Html::hiddenInput('Children[' . $key . '][id]', (!empty($model['id'])) ? $model['id'] : "") .
                    Html::a('<i class="glyphicon glyphicon-trash"></i>', '#', ['title' =>  'Delete', 'onClick' => 'delRowHallGroupPricePercent(' . $key . '); return false;', 'id' => 'hall-group-price-percent-del-btn']);
            },
        ],
    ],
    'gridSettings' => [
        'panel' => [
            'heading' => false,
            'type' => GridView::TYPE_DEFAULT,
            'before' => false,
            'footer' => false,
            'after' => Html::button(Yii::t('app', 'Dodaj % dnia pierwszego'), ['type' => 'button', 'class' => 'btn btn-success kv-batch-create', 'onClick' => 'addRowHallGroupPricePercent()']),
        ]
    ]
]);
echo  "    </div>\n\n";
?>


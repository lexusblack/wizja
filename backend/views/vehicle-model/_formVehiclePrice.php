<div class="form-group" id="add-vehicle-price">
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
    'formName' => 'VehiclePrice',
    'checkboxColumn' => false,
    'actionColumn' => false,
    'attributeDefaults' => [
        'type' => TabularForm::INPUT_TEXT,
    ],
    'attributes' => [
        "id" => ['type' => TabularForm::INPUT_HIDDEN, 'columnOptions' => ['hidden'=>true]],
        'name' => ['type' => TabularForm::INPUT_TEXT, 'label'=>Yii::t('app', 'Nazwa')],
        'price' => [
            'type' => TabularForm::INPUT_WIDGET,
            'label'=>Yii::t('app', 'Cena'),
            'widgetClass' => \yii\widgets\MaskedInput::className(),
            'options' => [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ],
            ],
        'cost' => [            'type' => TabularForm::INPUT_WIDGET,
            'label'=>Yii::t('app', 'Koszt'),
            'widgetClass' => \yii\widgets\MaskedInput::className(),
            'options' => [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ],
            ],
            'currency' => [
            'type' => TabularForm::INPUT_WIDGET,
            'label'=>Yii::t('app', 'Waluta'),
            'widgetClass' => \kartik\widgets\Select2::className(),
            'options' => [
                'data' => \backend\models\SettingsForm::getCurrencyListAll(),
                'options' => ['placeholder' => Yii::t('app', 'wybierz')],
            
        ],
        ],
        'unit' => ['type' => TabularForm::INPUT_TEXT, 'label'=>Yii::t('app', 'Jednostka')],
        'default' => [
            'type' => TabularForm::INPUT_WIDGET,
            'label'=>Yii::t('app', 'Domyślna'),
            'widgetClass' => \kartik\widgets\Select2::className(),
            'options' => [
                'data' => [0=>Yii::t('app', 'Nie'), 1=>Yii::t('app', 'Tak')],
                'options' => ['placeholder' => Yii::t('app', 'wybierz')],
            
        ],
        ],
        
        'del' => [
            'type' => 'raw',
            'label' => '',
            'value' => function($model, $key) {
                return
                    Html::hiddenInput('Children[' . $key . '][id]', (!empty($model['id'])) ? $model['id'] : "") .
                    Html::a('<i class="glyphicon glyphicon-trash"></i>', '#', ['title' =>  'Delete', 'onClick' => 'delRowVehiclePrice(' . $key . '); return false;', 'id' => 'vehicle-price-del-btn']);
            },
        ],
    ],
    'gridSettings' => [
        'panel' => [
            'heading' => false,
            'type' => GridView::TYPE_DEFAULT,
            'before' => false,
            'footer' => false,
            'after' => Html::button('<i class="glyphicon glyphicon-plus"></i>' . Yii::t('app', 'Dodaj stawkę'), ['type' => 'button', 'class' => 'btn btn-success kv-batch-create', 'onClick' => 'addRowVehiclePrice()']),
        ]
    ]
]);
echo  "    </div>\n\n";
?>


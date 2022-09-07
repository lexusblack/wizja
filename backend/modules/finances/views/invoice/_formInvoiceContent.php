<div class="form-group" id="add-invoice-content">
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
    'formName' => 'InvoiceContent',
    'checkboxColumn' => false,
    'actionColumn' => false,
    'attributeDefaults' => [
        'type' => TabularForm::INPUT_TEXT,
    ],
    'attributes' => [
        "id" => ['type' => TabularForm::INPUT_HIDDEN],
        'external_id' => ['type' => TabularForm::INPUT_TEXT],
        'cassification' => ['type' => TabularForm::INPUT_TEXT],
        'unit' => ['type' => TabularForm::INPUT_TEXT],
        'count' => ['type' => TabularForm::INPUT_TEXT],
        'price' => ['type' => TabularForm::INPUT_TEXT],
        'discount' => ['type' => TabularForm::INPUT_TEXT],
        'discount_percent' => ['type' => TabularForm::INPUT_TEXT],
        'netto' => ['type' => TabularForm::INPUT_TEXT],
        'brutto' => ['type' => TabularForm::INPUT_TEXT],
        'vat' => ['type' => TabularForm::INPUT_TEXT],
        'lumpcode' => ['type' => TabularForm::INPUT_TEXT],
        'create_time' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => Yii::t('app', 'Wybierz czas'),
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'update_time' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => Yii::t('app', 'Wybierz czas'),
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'del' => [
            'type' => 'raw',
            'label' => '',
            'value' => function($model, $key) {
                return Html::a('<i class="glyphicon glyphicon-trash"></i>', '#', ['title' =>  Yii::t('app', 'Usuń'), 'onClick' => 'delRowInvoiceContent(' . $key . '); return false;', 'id' => 'invoice-content-del-btn']);
            },
        ],
    ],
    'gridSettings' => [
        'panel' => [
            'heading' => false,
            'type' => GridView::TYPE_DEFAULT,
            'before' => false,
            'footer' => false,
            'after' => Html::button('<i class="glyphicon glyphicon-plus"></i>' . Yii::t('app', 'Dodaj zawartość przychodu'), ['type' => 'button', 'class' => 'btn btn-success kv-batch-create', 'onClick' => 'addRowInvoiceContent()']),
        ]
    ]
]);
echo  "    </div>\n\n";
?>


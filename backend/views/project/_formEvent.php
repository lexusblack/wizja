<div class="form-group" id="add-event">
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
    'formName' => 'Event',
    'checkboxColumn' => false,
    'actionColumn' => false,
    'attributeDefaults' => [
        'type' => TabularForm::INPUT_TEXT,
    ],
    'attributes' => [
        "id" => ['type' => TabularForm::INPUT_HIDDEN, 'columnOptions' => ['hidden'=>true]],
        'name' => ['type' => TabularForm::INPUT_TEXT],
        'location_id' => ['type' => TabularForm::INPUT_TEXT],
        'customer_id' => [
            'label' => 'Customer',
            'type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\widgets\Select2::className(),
            'options' => [
                'data' => \yii\helpers\ArrayHelper::map(\common\models\Customer::find()->orderBy('name')->asArray()->all(), 'id', 'name'),
                'options' => ['placeholder' => 'Choose Customer'],
            ],
            'columnOptions' => ['width' => '200px']
        ],
        'contact_id' => [
            'label' => 'Contact',
            'type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\widgets\Select2::className(),
            'options' => [
                'data' => \yii\helpers\ArrayHelper::map(\common\models\Contact::find()->orderBy('id')->asArray()->all(), 'id', 'id'),
                'options' => ['placeholder' => 'Choose Contact'],
            ],
            'columnOptions' => ['width' => '200px']
        ],
        'manager_id' => [
            'label' => 'User',
            'type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\widgets\Select2::className(),
            'options' => [
                'data' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->orderBy('username')->asArray()->all(), 'id', 'username'),
                'options' => ['placeholder' => 'Choose User'],
            ],
            'columnOptions' => ['width' => '200px']
        ],
        'info' => ['type' => TabularForm::INPUT_TEXTAREA],
        'description' => ['type' => TabularForm::INPUT_TEXTAREA],
        'code' => ['type' => TabularForm::INPUT_TEXT],
        'event_start' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Event Start',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'event_end' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Event End',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'status' => ['type' => TabularForm::INPUT_TEXT],
        'type' => ['type' => TabularForm::INPUT_TEXT],
        'create_time' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Create Time',
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
                        'placeholder' => 'Choose Update Time',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'packing_start' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Packing Start',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'packing_end' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Packing End',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'montage_start' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Montage Start',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'montage_end' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Montage End',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'readiness_start' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Readiness Start',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'readiness_end' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Readiness End',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'practice_start' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Practice Start',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'practice_end' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Practice End',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'disassembly_start' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Disassembly Start',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'disassembly_end' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Disassembly End',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'packing_type' => ['type' => TabularForm::INPUT_TEXT],
        'montage_type' => ['type' => TabularForm::INPUT_TEXT],
        'readiness_type' => ['type' => TabularForm::INPUT_TEXT],
        'practice_type' => ['type' => TabularForm::INPUT_TEXT],
        'disassembly_type' => ['type' => TabularForm::INPUT_TEXT],
        'level' => ['type' => TabularForm::INPUT_TEXT],
        'route_start' => ['type' => TabularForm::INPUT_TEXT],
        'route_end' => ['type' => TabularForm::INPUT_TEXT],
        'provision' => ['type' => TabularForm::INPUT_TEXT],
        'project_done' => ['type' => TabularForm::INPUT_TEXT],
        'invoice_issued' => ['type' => TabularForm::INPUT_TEXT],
        'invoice_sent' => ['type' => TabularForm::INPUT_TEXT],
        'transfer_booked' => ['type' => TabularForm::INPUT_TEXT],
        'invoice_number' => ['type' => TabularForm::INPUT_TEXT],
        'creator_id' => [
            'label' => 'User',
            'type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\widgets\Select2::className(),
            'options' => [
                'data' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->orderBy('username')->asArray()->all(), 'id', 'username'),
                'options' => ['placeholder' => 'Choose User'],
            ],
            'columnOptions' => ['width' => '200px']
        ],
        'provision_type' => ['type' => TabularForm::INPUT_TEXT],
        'finance_info' => ['type' => TabularForm::INPUT_TEXTAREA],
        'invoice_id' => [
            'label' => 'Invoice',
            'type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\widgets\Select2::className(),
            'options' => [
                'data' => \yii\helpers\ArrayHelper::map(\common\models\Invoice::find()->orderBy('id')->asArray()->all(), 'id', 'id'),
                'options' => ['placeholder' => 'Choose Invoice'],
            ],
            'columnOptions' => ['width' => '200px']
        ],
        'offer_prepared' => ['type' => TabularForm::INPUT_TEXT],
        'expense_status' => ['type' => TabularForm::INPUT_TEXT],
        'project_paid' => ['type' => TabularForm::INPUT_TEXT],
        'expenses_paid' => ['type' => TabularForm::INPUT_TEXT],
        'offer_accepted' => ['type' => TabularForm::INPUT_TEXT],
        'offer_sent' => ['type' => TabularForm::INPUT_TEXT],
        'offer_sent_user_id' => ['type' => TabularForm::INPUT_TEXT],
        'ready_to_invoice' => ['type' => TabularForm::INPUT_TEXT],
        'ready_to_invoice_user_id' => ['type' => TabularForm::INPUT_TEXT],
        'expense_entered' => ['type' => TabularForm::INPUT_TEXT],
        'expense_entered_user_id' => ['type' => TabularForm::INPUT_TEXT],
        'invoice_status' => ['type' => TabularForm::INPUT_TEXT],
        'project_settled' => ['type' => TabularForm::INPUT_TEXT],
        'offer_sent_date' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Offer Sent Date',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'expense_entered_date' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Expense Entered Date',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'ready_to_invoice_date' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\datecontrol\DateControl::classname(),
            'options' => [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => 'Choose Ready To Invoice Date',
                        'autoclose' => true,
                    ]
                ],
            ]
        ],
        'crew_working_time_changed' => ['type' => TabularForm::INPUT_TEXT],
        'tasks_schema_id' => ['type' => TabularForm::INPUT_TEXT],
        'address' => ['type' => TabularForm::INPUT_TEXT],
        'del' => [
            'type' => 'raw',
            'label' => '',
            'value' => function($model, $key) {
                return
                    Html::hiddenInput('Children[' . $key . '][id]', (!empty($model['id'])) ? $model['id'] : "") .
                    Html::a('<i class="glyphicon glyphicon-trash"></i>', '#', ['title' =>  'Delete', 'onClick' => 'delRowEvent(' . $key . '); return false;', 'id' => 'event-del-btn']);
            },
        ],
    ],
    'gridSettings' => [
        'panel' => [
            'heading' => false,
            'type' => GridView::TYPE_DEFAULT,
            'before' => false,
            'footer' => false,
            'after' => Html::button('<i class="glyphicon glyphicon-plus"></i>' . 'Add Event', ['type' => 'button', 'class' => 'btn btn-success kv-batch-create', 'onClick' => 'addRowEvent()']),
        ]
    ]
]);
echo  "    </div>\n\n";
?>


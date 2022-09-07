<div class="form-group" id="add-event-additional-statut-name">
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
    'formName' => 'EventAdditionalStatutName',
    'checkboxColumn' => false,
    'actionColumn' => false,
    'attributeDefaults' => [
        'type' => TabularForm::INPUT_TEXT,
    ],
    'attributes' => [
        "id" => ['type' => TabularForm::INPUT_HIDDEN, 'columnOptions' => ['hidden'=>true]],
        'name' => ['type' => TabularForm::INPUT_TEXT],
        'icon' => ['type' => TabularForm::INPUT_TEXT],
        'reminder_mail' => ['type' => TabularForm::INPUT_TEXT],
        'reminder_sms' => ['type' => TabularForm::INPUT_TEXT],
        'reminder_pm' => ['type' => TabularForm::INPUT_TEXT],
        'reminder_users' => ['type' => TabularForm::INPUT_TEXT],
        'reminder_teams' => ['type' => TabularForm::INPUT_TEXT],
        'active' => ['type' => TabularForm::INPUT_TEXT],
        'position' => ['type' => TabularForm::INPUT_TEXT],
        'del' => [
            'type' => 'raw',
            'label' => '',
            'value' => function($model, $key) {
                return
                    Html::hiddenInput('Children[' . $key . '][id]', (!empty($model['id'])) ? $model['id'] : "") .
                    Html::a('<i class="glyphicon glyphicon-trash"></i>', '#', ['title' =>  'Delete', 'onClick' => 'delRowEventAdditionalStatutName(' . $key . '); return false;', 'id' => 'event-additional-statut-name-del-btn']);
            },
        ],
    ],
    'gridSettings' => [
        'panel' => [
            'heading' => false,
            'type' => GridView::TYPE_DEFAULT,
            'before' => false,
            'footer' => false,
            'after' => Html::button('<i class="glyphicon glyphicon-plus"></i>' . 'Add Event Additional Statut Name', ['type' => 'button', 'class' => 'btn btn-success kv-batch-create', 'onClick' => 'addRowEventAdditionalStatutName()']),
        ]
    ]
]);
echo  "    </div>\n\n";
?>


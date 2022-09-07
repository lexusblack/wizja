<div class="form-group" id="add-gear-model">
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
    'formName' => 'GearModel',
    'checkboxColumn' => false,
    'actionColumn' => false,
    'attributeDefaults' => [
        'type' => TabularForm::INPUT_TEXT,
    ],
    'attributes' => [
        "id" => ['type' => TabularForm::INPUT_HIDDEN, 'visible' => false],
        'name' => ['type' => TabularForm::INPUT_TEXT],
        'brightness' => ['type' => TabularForm::INPUT_TEXT],
        'power_consumption' => ['type' => TabularForm::INPUT_TEXT],
        'type' => ['type' => TabularForm::INPUT_TEXT],
        'category_id' => [
            'label' => Yii::t('app', 'Kategoria'),
            'type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\widgets\Select2::className(),
            'options' => [
                'data' => \yii\helpers\ArrayHelper::map(\common\models\GearCategory::find()->orderBy('name')->asArray()->all(), 'id', 'name'),
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kategorię')],
            ],
            'columnOptions' => ['width' => '200px']
        ],
        'width' => ['type' => TabularForm::INPUT_TEXT],
        'height' => ['type' => TabularForm::INPUT_TEXT],
        'volume' => ['type' => TabularForm::INPUT_TEXT],
        'depth' => ['type' => TabularForm::INPUT_TEXT],
        'weight' => ['type' => TabularForm::INPUT_TEXT],
        'weight_case' => ['type' => TabularForm::INPUT_TEXT],
        'info' => ['type' => TabularForm::INPUT_TEXTAREA],
        'photo' => ['type' => TabularForm::INPUT_TEXT],
        'del' => [
            'type' => 'raw',
            'label' => '',
            'value' => function($model, $key) {
                return
                    Html::hiddenInput('Children[' . $key . '][id]', (!empty($model['id'])) ? $model['id'] : "") .
                    Html::a('<i class="glyphicon glyphicon-trash"></i>', '#', ['title' =>  Yii::t('app', 'Usuń'), 'onClick' => 'delRowGearModel(' . $key . '); return false;', 'id' => 'gear-model-del-btn']);
            },
        ],
    ],
    'gridSettings' => [
        'panel' => [
            'heading' => false,
            'type' => GridView::TYPE_DEFAULT,
            'before' => false,
            'footer' => false,
            'after' => Html::button('<i class="glyphicon glyphicon-plus"></i>' . Yii::t('app', 'Dodaj sprzęt'), ['type' => 'button', 'class' => 'btn btn-success kv-batch-create', 'onClick' => 'addRowGearModel()']),
        ]
    ]
]);
echo  "    </div>\n\n";
?>


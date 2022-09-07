<div class="form-group" id="add-location">
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
    'formName' => 'Location',
    'checkboxColumn' => false,
    'actionColumn' => false,
    'attributeDefaults' => [
        'type' => TabularForm::INPUT_TEXT,
    ],
    'attributes' => [
        "id" => ['type' => TabularForm::INPUT_HIDDEN, 'visible' => false],
        'name' => ['type' => TabularForm::INPUT_TEXT],
        'address' => ['type' => TabularForm::INPUT_TEXT],
        'city' => ['type' => TabularForm::INPUT_TEXT],
        'zip' => ['type' => TabularForm::INPUT_TEXT],
        'country' => ['type' => TabularForm::INPUT_TEXT],
        'info' => ['type' => TabularForm::INPUT_TEXTAREA],
        'latitude' => ['type' => TabularForm::INPUT_TEXT],
        'longitude' => ['type' => TabularForm::INPUT_TEXT],
        'type' => ['type' => TabularForm::INPUT_TEXT],
        'status' => ['type' => TabularForm::INPUT_TEXT],
        'travel_time' => ['type' => TabularForm::INPUT_TEXT],
        'manager_phone' => ['type' => TabularForm::INPUT_TEXT],
        'electrician_phone' => ['type' => TabularForm::INPUT_TEXT],
        'distance' => ['type' => TabularForm::INPUT_TEXT],
        'photo' => ['type' => TabularForm::INPUT_TEXT],
        'rent_price' => ['type' => TabularForm::INPUT_TEXT],
        'owner_id' => ['type' => TabularForm::INPUT_TEXT],
        'video' => ['type' => TabularForm::INPUT_TEXT],
        'description' => ['type' => TabularForm::INPUT_TEXTAREA],
        'stars' => ['type' => TabularForm::INPUT_TEXT],
        'province_id' => [
            'label' => 'Województwo',
            'type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\widgets\Select2::className(),
            'options' => [
                'data' => \yii\helpers\ArrayHelper::map(\common\models\Province::find()->orderBy('name')->asArray()->all(), 'id', 'name'),
                'options' => ['placeholder' => Yii::t('app', 'Wybierz województwo')],
            ],
            'columnOptions' => ['width' => '200px']
        ],
        'beds' => ['type' => TabularForm::INPUT_TEXT],
        'website' => ['type' => TabularForm::INPUT_TEXT],
        'biggest_room' => ['type' => TabularForm::INPUT_TEXT],
        'email' => ['type' => TabularForm::INPUT_TEXT],
        'del' => [
            'type' => 'raw',
            'label' => '',
            'value' => function($model, $key) {
                return
                    Html::hiddenInput('Children[' . $key . '][id]', (!empty($model['id'])) ? $model['id'] : "") .
                    Html::a('<i class="glyphicon glyphicon-trash"></i>', '#', ['title' =>  Yii::t('app', 'Usuń'), 'onClick' => 'delRowLocation(' . $key . '); return false;', 'id' => 'location-del-btn']);
            },
        ],
    ],
    'gridSettings' => [
        'panel' => [
            'heading' => false,
            'type' => GridView::TYPE_DEFAULT,
            'before' => false,
            'footer' => false,
            'after' => Html::button('<i class="glyphicon glyphicon-plus"></i>' . Yii::t('app', 'Dodaj miejsce'), ['type' => 'button', 'class' => 'btn btn-success kv-batch-create', 'onClick' => 'addRowLocation()']),
        ]
    ]
]);
echo  "    </div>\n\n";
?>


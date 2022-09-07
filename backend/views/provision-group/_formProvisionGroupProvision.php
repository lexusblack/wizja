<div class="form-group" id="add-provision-group-provision">
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
$sectionList = [Yii::t('app', 'Transport')=>Yii::t('app', 'Transport'), Yii::t('app', 'Obsługa')=>Yii::t('app', 'Obsługa')];
        foreach (\common\models\EventExpense::getSectionList() as $s)
        {
            $sectionList[$s] = $s;
        }
echo TabularForm::widget([
    'dataProvider' => $dataProvider,
    'formName' => 'ProvisionGroupProvision',
    'checkboxColumn' => false,
    'actionColumn' => false,
    'attributeDefaults' => [
        'type' => TabularForm::INPUT_TEXT,
    ],
    'attributes' => [
        "id" => ['type' => TabularForm::INPUT_HIDDEN, 'columnOptions' => ['hidden'=>true]],
        'section' => [
            'label' => Yii::t('app', 'Sekcja'),
            'type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\widgets\Select2::className(),
            'options' => [
                'data' => $sectionList,
                'options' => ['placeholder' => 'Wybierz sekcję'],
            ],
            'columnOptions' => ['width' => '200px']
        ],
        'value' => ['type' => TabularForm::INPUT_TEXT, 'label' => Yii::t('app', 'Prowizja %')],
        'type' => [
            'label' => Yii::t('app', 'Typ'),
            'type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \kartik\widgets\Select2::className(),
            'options' => [
                'data' => \common\models\ProvisionGroup::getTypes(),
                'options' => ['placeholder' => 'Wybierz typ'],
            ],
            'columnOptions' => ['width' => '200px']
        ],
        'del' => [
            'type' => 'raw',
            'label' => '',
            'value' => function($model, $key) {
                return
                    Html::hiddenInput('Children[' . $key . '][id]', (!empty($model['id'])) ? $model['id'] : "") .
                    Html::a('<i class="glyphicon glyphicon-trash"></i>', '#', ['title' =>  'Delete', 'onClick' => 'delRowProvisionGroupProvision(' . $key . '); return false;', 'id' => 'provision-group-provision-del-btn']);
            },
        ],
    ],
    'gridSettings' => [
        'panel' => [
            'heading' => false,
            'type' => GridView::TYPE_DEFAULT,
            'before' => false,
            'footer' => false,
            'after' => Html::button('<i class="glyphicon glyphicon-plus"></i>' . Yii::t('app', 'Dodaj'), ['type' => 'button', 'class' => 'btn btn-success kv-batch-create', 'onClick' => 'addRowProvisionGroupProvision()']),
        ]
    ]
]);
echo  "    </div>\n\n";
?>


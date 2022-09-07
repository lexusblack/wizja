<div class="form-group" id="add-gears-price-percent">
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
    'formName' => 'GearsPricePercent',
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
                    Html::a('<i class="glyphicon glyphicon-trash"></i>', '#', ['title' =>  'Delete', 'onClick' => 'delRowGearsPricePercent(' . $key . '); return false;', 'id' => 'gear-set-item-del-btn']);
            },
        ],
    ],
    'gridSettings' => [
        'panel' => [
            'heading' => false,
            'type' => GridView::TYPE_DEFAULT,
            'before' => false,
            'footer' => false,
            'after' => Html::button('<i class="glyphicon glyphicon-plus"></i>' . Yii::t('app', 'Dodaj kolejny poziom'), ['type' => 'button', 'class' => 'btn btn-success kv-batch-create', 'onClick' => 'addRowGearsPricePercent()']),
        ]
    ]
]);
echo  "    </div>\n\n";
?>

<?php
use yii\helpers\Url;
$relID = 'gears-price-percent';
?>
<script>
    function addRowGearsPricePercent() {
        var data = $('#add-<?= $relID?> :input').serializeArray();
        data.push({name: '_action', value : 'add'});
        $.ajax({
            type: 'POST',
            url: '<?php echo Url::to(['add-'.$relID]); ?>',
            data: data,
            success: function (data) {
                $('#add-<?= $relID?>').html(data);
            }
        });
    }
    function delRowGearsPricePercent(id) {
        $('#add-<?= $relID?> tr[data-key=' + id + ']').remove();
    }
</script>
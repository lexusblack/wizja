<?php
/* @var $this \yii\web\View; */
use yii\bootstrap\ButtonDropdown;
use yii\bootstrap\Html;
use yii\bootstrap\Dropdown;

use kartik\form\ActiveForm;
use backend\modules\finances\widgets\SearchWidget;
?>

<?php $form = ActiveForm::begin([
    'method' => 'get',
    'type' => ActiveForm::TYPE_HORIZONTAL,
]); ?>
<?php /*
$multiselect = $form->field($model, 'qOptions')->multiselect(SearchWidget::getOptionList());
echo $form->field($model, 'q', [
    'options'=>[
        'class'=>'col-lg-2',
    ],
    'addon' => [
        'append' => [
            'content' => \yii\bootstrap\ButtonDropdown::widget([
                'label' => 'wybierz...',
                'dropdown' => [
                    'items'=>[
                        ['label' => $multiselect, 'encode'=>false],
                    ],
                ],
                'options' => ['class'=>'btn-default btn']
            ]),
            'asButton' => true
        ]
    ]
])->textInput()->label(false); */
?>
<div class="col-sm-10">
    <?php echo $form->field($model, 'q')->label('')->textInput(['placeholder' => Yii::t('app', 'Wpisz frazę')]);?>
</div>
<div class="col-sm-2">
    <?php
    echo Html::submitButton(Yii::t('app', 'Szukaj'), ['class'=>'btn btn-success']);
?>
</div>


<?php ActiveForm::end(); ?>

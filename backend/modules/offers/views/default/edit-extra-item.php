<?php

use backend\modules\offers\models\OfferExtraItem;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\offers\models\OfferExtraItem */
/* @var $form yii\widgets\ActiveForm */

?>

    <div class="offer-extra-item-form">

        <?php $form = ActiveForm::begin(['id' => 'active-form']); ?>

        <?= $form->field($model, 'type')->dropDownList($model->getTypes(), ['id'=>'type_dropdown']) ?>

        <?= $form->field($model, 'offer_id')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'category_id', ['labelOptions' => ['id' => 'label_category']])->dropDownList($categories, ['prompt' => 'Wybierz', 'id' => 'input_category']) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'quantity')->textInput() ?>

        <?= $form->field($model, 'price', ['labelOptions' => ['id' => 'label_price']])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'discount', ['labelOptions' => ['id' => 'label_discount']])->textInput(['maxlength' => true, 'id' => 'input_discount']) ?>

        <?= $form->field($model, 'duration', ['labelOptions' => ['id' => 'label_duration']])->textInput() ?>
        <?= $form->field($model, 'time_type', ['labelOptions' => ['id' => 'label_time']])->dropDownList(\common\models\OfferRole::getTimeType(), ['prompt' => 'Wybierz', 'id' => 'input_time']) ?>
                    


<?php
            echo $form->field($model, 'cost')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>

<?php
            echo $form->field($model, 'weight')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>
<?php
            echo $form->field($model, 'volume')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>


        <div class="form-group">
            <?= Html::submitButton('Zapisz', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<script type="text/javascript">
    function typeChange(val)
    {
        if (val == 1) {
            label_price.html("Cena");
            label_duration.html("Dni pracy");
            input_category.slideDown();
            label_category.slideDown();
            input_discount.slideDown();
            label_discount.slideDown();
            $(".field-input_time").hide();
        }
        
        if (val == 2) {
            label_price.html("Cena / km");
            label_duration.html("Km");
            input_category.slideUp();
            label_category.slideUp();
            input_discount.slideUp();
            label_discount.slideUp();
            $(".field-input_time").show();
        }
        
        if (val == 3) {
            label_price.html("Cena");
            label_duration.html("Dni pracy");
            input_category.slideUp();
            label_category.slideUp();
            input_discount.slideUp();
            label_discount.slideUp();
            $(".field-input_time").show();
        }
    }
</script>
<?php

$this->registerJs('
    var label_price = $("#label_price");
    var input_category = $("#input_category");
    var label_category = $("#label_category");
    var label_duration = $("#label_duration");
    var input_discount = $("#input_discount");
    var label_discount = $("#label_discount");
    $(".field-input_time").hide();
    $("#type_dropdown").change(function(){
        
        typeChange($(this).val());
        
    });
    typeChange($("#type_dropdown").val());

');


$this->registerCss('

.offer-extra-item-form {
    padding: 50px;
}

');

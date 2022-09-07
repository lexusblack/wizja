<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gear-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'category_id')->widget(\kartik\tree\TreeViewInput::className(), [
                // single query fetch to render the tree
                // use the Product model you have in the previous step
                'query' => \common\models\GearCategory::find()->where(['active'=>1])->addOrderBy('root, lft'),
                'headingOptions'=>['label'=>'Categories'],
                'asDropdown' => true,   // will render the tree input widget as a dropdown.
                'multiple' => false,     // set to false if you do not need multiple selection
                'fontAwesome' => false,  // render font awesome icons
                //'options'=>['disabled' => true],
            ])
            ?>
            <?php //if (!$model->isNewRecord){ echo $form->field($model, 'sort_order')->textInput(['maxlength' => true, 'autocomplete'=>"off"]); } ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>

            <?php echo $form->field($model, 'type')->dropDownList(\common\models\Gear::getTypeList()) ?>


            <?php echo $form->field($model, 'no_items')->checkbox(); ?>

            <?php 
                $c = \common\models\Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
                if ($c->own_ean)
                {
                     echo $form->field($model, 'code')->textInput(['maxlength' => true, 'autocomplete'=>"off"]);
                }
            ?>


            <?php //echo $form->field($model, 'quantity')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
            <?php echo $form->field($model, 'packing')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
            <?php echo $form->field($model, 'min_quantity')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
            <?php echo $form->field($model, 'max_quantity')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
            <?php echo $form->field($model, 'unit')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
            <?php echo $form->field($model, 'warehouse')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
            <?php echo $form->field($model, 'location')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
            <?php echo $form->field($model, 'info2')->textArea(['maxlength' => true]) ?>
            <?php echo $form->field($model, 'visible_in_offer')->dropDownList(\common\models\User::getVisibleStatusList()) ?>
            <?php echo $form->field($model, 'visible_in_warehouse')->dropDownList(\common\models\User::getVisibleStatusList()) ?>
            <?php echo $form->field($model, 'conflict_outcome')->dropDownList([0=>'Nie', 1=>'Tak'])->label("Wydawanie pomimo konfliktów"); ?>

            <?php
            echo $form->field($model, 'brightness')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>

            <?php
            echo $form->field($model, 'power_consumption')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>

            <?= $form->field($model, 'offer_description')->widget(\yii\redactor\widgets\Redactor::className(), [
                'clientOptions' => [
                    'buttons' => ['html','formatting', 'bold', 'italic', 'deleted',
                        'unorderedlist', 'orderedlist','outdent', 'indent', 'alignment', 'link', 'horizontalrule'],
                ]
            ]);?>

            <?= $form->field($model, 'info')->widget(\yii\redactor\widgets\Redactor::className(), [
                'clientOptions' => [
                    'buttons' => ['html','formatting', 'bold', 'italic', 'deleted',
                        'unorderedlist', 'orderedlist','outdent', 'indent', 'alignment', 'link', 'horizontalrule'],
                ]
            ]);?>

        </div>
        <div class="col-md-6">
        <h2><?=Yii::t('app', 'Wymiar urządzenia netto (bez case)')?></h2>
            <?php
            echo $form->field($model, 'width')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>

            <?php
            echo $form->field($model, 'height')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>


            <?php
            echo $form->field($model, 'depth')->widget(\yii\widgets\MaskedInput::className(), [
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

            <?php
            echo $form->field($model, 'weight')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>
        <h2 class="case-name"><?=Yii::t('app', 'Wymiar case (jeśli pakowany)')?></h2>
            <?php
            echo $form->field($model, 'width_case')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>

            <?php
            echo $form->field($model, 'height_case')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>


            <?php
            echo $form->field($model, 'depth_case')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>
            <?php
            echo $form->field($model, 'volume_case')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>

            <?php
            echo $form->field($model, 'weight_case')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>
            <?php
            /*echo $form->field($model, 'price')->widget(\yii\widgets\MaskedInput::className(), [
                    'clientOptions'=> [
                        'alias'=>'decimal',
                        'rightAlign'=>false,
                        'digits'=>2,
                    ]
            ]);
            */
            echo $form->field($model, 'value')->widget(\yii\widgets\MaskedInput::className(), [
                    'clientOptions'=> [
                        'alias'=>'decimal',
                        'rightAlign'=>false,
                        'digits'=>2,
                    ]
            ]);
            if ($rfids) { ?>
                <span class="rfid-click btn btn-primary" style="margin-bottom: 20px;">Kliknij, aby rozwinąć kody rfid</span>
                <div class="rfid-scope" style="display: none;">
                <?php
                    foreach ($rfids as $i => $rfid) {
                        echo $form->field($rfid, "[" . $i . "]rfid_code")->label(Yii::t('app', 'Kod rfid nr').' ' . ($i + 1));
                    } ?>
                </div>

                <?php
            }

            ?>
            <div>
            <?php echo Html::activeHiddenInput($model, 'photo'); ?>
                <?php echo Html::activeLabel($model, 'photo'); ?>
<input name="file" type="file" id="gear-file" />
</div>

    <div class="doka-container">
        <div></div>
    </div>
<div class="photo-preview" style="padding-bottom:10px; text-align:center;">
</div>
            <div class="form-group">
                <?php // echo Html::activeHiddenInput($model, 'photo'); ?>
                <?php //echo Html::activeLabel($model, 'photo'); ?>
                <?php /*echo devgroup\dropzone\DropZone::widget([
                    'url'=>\common\helpers\Url::to(['upload']),
                    'name'=>'file',
                    'options'=>[
                        'maxFiles' => 1,
                    ],
                    'eventHandlers' => [
                        'success' => 'function(file, response) {
               $("#gear-photo").val(response.filename);

            }',
                    ]
                ]); ?>
                <?php echo Html::error($model, 'photo'); */ ?>
            </div>

        </div>
    </div>





    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success save-gear']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs('

$("#'.Html::getInputId($model, 'one_in_case').':checkbox").change(function(e){
    var checked = $(e.target).prop("checked");
    var t = $("#caseSize");
    if (checked == true)
    {
        t.show();
    }
    else 
    {
        t.hide();
    }
}).trigger("change");

$("#'.Html::getInputId($model, 'height').'").change(function(e){
    volume = countVolume();
    $("#'.Html::getInputId($model, 'volume').'").val(volume);
});
$("#'.Html::getInputId($model, 'width').'").change(function(e){
    volume = countVolume();
    $("#'.Html::getInputId($model, 'volume').'").val(volume);
});
$("#'.Html::getInputId($model, 'depth').'").change(function(e){
    volume = countVolume();
    $("#'.Html::getInputId($model, 'volume').'").val(volume);
});

$("#'.Html::getInputId($model, 'height_case').'").change(function(e){
    volume = countVolume2();
    $("#'.Html::getInputId($model, 'volume_case').'").val(volume);
});
$("#'.Html::getInputId($model, 'width_case').'").change(function(e){
    volume = countVolume2();
    $("#'.Html::getInputId($model, 'volume_case').'").val(volume);
});
$("#'.Html::getInputId($model, 'depth_case').'").change(function(e){
    volume = countVolume2();
    $("#'.Html::getInputId($model, 'volume_case').'").val(volume);
});

function countVolume()
{
    height = $("#'.Html::getInputId($model, 'height').'").val();
    width = $("#'.Html::getInputId($model, 'width').'").val();
    depth = $("#'.Html::getInputId($model, 'depth').'").val();
    return height*width*depth/1000000;
}
function countVolume2()
{
    height = $("#'.Html::getInputId($model, 'height_case').'").val();
    width = $("#'.Html::getInputId($model, 'width_case').'").val();
    depth = $("#'.Html::getInputId($model, 'depth_case').'").val();
    return height*width*depth/1000000;
}
');
$this->registerJs('
$("#'.Html::getInputId($model, 'no_items').':checkbox").change(function(e){
    var checked = $(e.target).prop("checked");
    var t = $(".field-'.Html::getInputId($model, 'code').'");
    var r = $(".field-'.Html::getInputId($model, 'warehouse').'");
    var x = $(".field-'.Html::getInputId($model, 'info2').'");
    var x1 = $(".field-'.Html::getInputId($model, 'packing').'");
    var x2 = $(".field-'.Html::getInputId($model, 'height_case').'");
    var x3 = $(".field-'.Html::getInputId($model, 'width_case').'");
    var x4 = $(".field-'.Html::getInputId($model, 'depth_case').'");
    var x5 = $(".field-'.Html::getInputId($model, 'volume_case').'");
    var x6 = $(".field-'.Html::getInputId($model, 'weight_case').'");
    if (checked == true)
    {
        t.show();
        r.show();
        x.show();
        t.find("input").prop("disabled",false);
        r.find("input").prop("disabled",false);
        x1.show();
        x2.show();
        x3.show();
        x4.show();
        x5.show();
        x6.show();
        x1.find("input").prop("disabled",false);
        x2.find("input").prop("disabled",false);
        x3.find("input").prop("disabled",false);
        x4.find("input").prop("disabled",false);
        x5.find("input").prop("disabled",false);
        x6.find("input").prop("disabled",false);
        $(".case-name").show();
    }
    else 
    {
        t.hide();
        r.hide();
        x.hide();
        t.find("input").prop("disabled",true);
        r.find("input").prop("disabled",true);
        x1.hide();
        x2.hide();
        x3.hide();
        x4.hide();
        x5.hide();
        x6.hide();
        x1.find("input").prop("disabled",true);
        x2.find("input").prop("disabled",true);
        x3.find("input").prop("disabled",true);
        x4.find("input").prop("disabled",true);
        x5.find("input").prop("disabled",true);
        x6.find("input").prop("disabled",true);
        $(".case-name").hide();
    }
}).trigger("change");

$(".rfid-click").click(function(){
    $(this).hide();
    $(".rfid-scope").show();
});


$("#'.Html::getInputId($model, 'type').'").change(function(e){
    type = $(this).val();
    if (type==1)
    {
        $(".form-group").show();
        $(".gear-form h2").show();
    }
    if (type == 2)
    {
        $(".form-group").show();
        $(".field-'.Html::getInputId($model, 'no_items').'").hide();
        $(".field-'.Html::getInputId($model, 'brightness').'").hide();
        $(".field-'.Html::getInputId($model, 'power_consumption').'").hide();
        $(".field-'.Html::getInputId($model, 'value').'").hide();
        $(".field-'.Html::getInputId($model, 'code').'").hide();
        $(".field-'.Html::getInputId($model, 'min_quantity').'").hide();
        $(".field-'.Html::getInputId($model, 'max_quantity').'").hide();
        $(".field-'.Html::getInputId($model, 'warehouse').'").hide();
        $(".field-'.Html::getInputId($model, 'location').'").hide();
        $(".field-'.Html::getInputId($model, 'width').'").hide();
        $(".field-'.Html::getInputId($model, 'height').'").hide();
        $(".field-'.Html::getInputId($model, 'depth').'").hide();
        $(".field-'.Html::getInputId($model, 'weight').'").hide();
        $(".field-'.Html::getInputId($model, 'volume').'").hide();
        $(".field-'.Html::getInputId($model, 'photo').'").hide();

        $(".gear-form h2").hide();
        $("#'.Html::getInputId($model, 'no_items').':checkbox").prop("checked", true);
        $(".field-'.Html::getInputId($model, 'code').'").find("input").prop("disabled",false);
        $(".field-'.Html::getInputId($model, 'warehouse').'").find("input").prop("disabled",false);
        $(".field-'.Html::getInputId($model, 'location').'").find("input").prop("disabled",false);
        $(".field-'.Html::getInputId($model, 'info2').'").find("input").prop("disabled",false);

    }
    if (type ==3)
    {
        $(".form-group").show();
        $(".gear-form h2").show();
        $("#'.Html::getInputId($model, 'no_items').':checkbox").prop("checked", true);
        $(".field-'.Html::getInputId($model, 'no_items').'").hide();
        $(".field-'.Html::getInputId($model, 'brightness').'").hide();
        $(".field-'.Html::getInputId($model, 'power_consumption').'").hide();
        $(".field-'.Html::getInputId($model, 'value').'").hide();
        $(".field-'.Html::getInputId($model, 'code').'").find("input").prop("disabled",false);
        $(".field-'.Html::getInputId($model, 'warehouse').'").find("input").prop("disabled",false);
        $(".field-'.Html::getInputId($model, 'location').'").find("input").prop("disabled",false);
        $(".field-'.Html::getInputId($model, 'info2').'").find("input").prop("disabled",false);
    }
    });
');
$this->registerCss('
.rfid-click {cursor: pointer;}
');


$this->registerCss("

    img {
        max-width: 100%;
    }

    .doka-container {
        width: 500px;
        height: 500px;
    }
");
$this->registerJs("
    var current_photo = null;

        var doka = $('.doka-container > div').doka({

            // enable utils
            utils: ['crop', 'filter', 'color', 'markup', 'sticker'],

            // clear editor when cancelled
            oncancel: function() {
                doka.doka('clear');
            },

            // handles the confirm button press
            onconfirm: function(output) {

                // No output received
                if (!output) {
                    doka.doka('clear');
                    return;
                };

                // Create preview image and append to body element
                var image = new Image();
                image.src = URL.createObjectURL(output.file);
                current_photo = output.file;
                $('.doka-container').hide();
                $('.photo-preview').show();
                $('.photo-preview').html(image);
                var fd = new FormData();
                fd.append('file',current_photo);
                var fileInput = document.getElementById('gear-file');   
                var filename = fileInput.files[0].name;
                fd.append('file_name', filename);
                $.ajax({
                      url: '/admin/gear/upload',
                      type: 'post',
                      data: fd,
                      contentType: false,
                      processData: false,
                      success: function(response){
                        $('#gear-photo').val(response.filename);
                      },
                   });
            },

            // The list of crop aspect ratios we want to offer the user
            cropAspectRatio: 1,
            outputWidth:500,
            outputWidth:500,
            outputData:true,
            cropAspectRatioOptions: [

                {
                    label: 'Square',
                    value: 1
                }
            ]
        });

        $('input[type=\"file\"]').on('change', function(e) {
            doka.doka('open', e.target.files[0]);
            $('.doka-container').show();
            $('.photo-preview').hide();
        })
        ");

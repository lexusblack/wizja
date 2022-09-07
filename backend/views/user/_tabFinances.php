<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">

<div class="row">
    <div class="col-md-4">
    <h3><?php echo Yii::t('app', 'Finanse'); ?></h3>
        <div class="panel_mid_blocks">
            <div class="panel_block">
            <?php $form = ActiveForm::begin(['action' => ['user/save-finance', 'id'=>$model->id]]); ?>
            <?php echo $form->field($model, 'rate_type')->dropDownList(\common\models\User::getRateList()) ?>

            <?php
            echo $form->field($model, 'rate_amount')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>

            <div id="month-rate-details">
                <?php
                echo $form->field($model, 'base_hours')->widget(\yii\widgets\MaskedInput::className(), [
                    'clientOptions'=> [
                        'alias'=>'integer',
                        'rightAlign'=>false,
                        'digits'=>2,
                    ]
                ]);
                ?>

            <?php
            echo $form->field($model, 'overtime_amount')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>

            </div>
            <?php
            echo $form->field($model, 'zus_rate')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>
            <?php
            echo $form->field($model, 'nfz_rate')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>
            <?php
            echo $form->field($model, 'tax_rate')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>
            <?php
            echo $form->field($model, 'vat_rate')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>
            <?= $form->field($model, 'vacation_days')->textInput(['autocomplete'=>"off"]) ?>
            <?php
            echo $form->field($model, 'vacation_rate')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

        <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    <?php if ($model->role==30) { ?>
    <div class="col-md-8">
    <h3><?=Yii::t('app', 'Prowizje PM')?></h3>
    <?php foreach ($sections as $s)
    {
        ?>
        <div class="row">
        <?php $form = ActiveForm::begin(['action' => ['user/save-section', 'id'=>$model->id, 'section'=>$s->section], 'options' => [
                'class' => 'form-inline form-sections'
             ]]); ?>
             <?php echo  $form->field($s, 'section')->hiddenInput()->label(false); ?>
        <?php
            echo $form->field($s, 'value')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                    
                ],
                'options'=>[
                'style'=>'width:70px',
                'class'=>'form-control'
                ]

            ])->label($s->section);
            ?>
        <?php echo $form->field($s, 'type')->dropDownList([1=>Yii::t('app', 'Od zysku'), 2=>Yii::t('app', 'Od wartości')])->label(false); ?>
        <?php echo $form->field($s, 'event_type')->dropDownList([1=>Yii::t('app', 'Od zarządzanych'), 2=>Yii::t('app', 'Od wszystkich')])->label(false); ?>
        <?php ActiveForm::end(); ?>
        </div>
    <?php    }?>
    <?= Html::a(Yii::t('app', 'Zapisz'), '#', ['class' =>'btn btn-primary btn-sm submit-all']); ?>
    <div>
    </div>
    </div>
    <?php } ?>
</div>
</div>

<?php
$this->registerJs('
    toggleRateType();
    
    $("#user-rate_type").on("change", toggleRateType);
    
    function toggleRateType()
    {
        var $el = $("#month-rate-details");
        if ($("#user-rate_type").val() == '.(\common\models\User::RATE_MONTH).')
        {
            $el.show();   
        }
        else
        {
            $el.hide();
        }
    }

    $(".submit-all").click(function(e){
        e.preventDefault();
        $(".form-sections").each(function(){
            data = $(this).serialize();
            url = $(this).attr("action");
            $.post({
                  url: url,
                  data:data,
                  success: function(response){
                    
                  }
                });
        });
        toastr.success("'.Yii::t('app', 'Zapisano!').'");
    });
');
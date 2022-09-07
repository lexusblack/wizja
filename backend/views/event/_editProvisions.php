<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">

<div class="row">
    <div class="col-md-12">
    <h3><?=Yii::t('app', 'Prowizje PM')?></h3>
            <div class="row" style="margin-bottom:20px;">
            <input type="text" id="copy-to-all"/>
            <a href="#" id="copy-to-all-button" class="btn btn-primary btn-xs"><?=Yii::t('app', 'Kopiuj do wszystkich')?></a>
        </div>
    <?php foreach ($sections as $s)
    {
        ?>
        <div class="row">
        <?php $form = ActiveForm::begin(['action' => ['event/save-section', 'id'=>$s->event_id, 'section'=>$s->section], 'options' => [
                'class' => 'form-inline form-sections'
             ]]); ?>
             <?php echo  $form->field($s, 'section')->hiddenInput()->label(false); ?>
             <?php echo  $form->field($s, 'event_id')->hiddenInput()->label(false); ?>
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
        <?php ActiveForm::end(); ?>
        </div>
    <?php    }?>
    <?= Html::a(Yii::t('app', 'Zapisz'), '#', ['class' =>'btn btn-primary btn-sm submit-all']); ?>
</div>
</div>
</div>

<?php
$this->registerJs('
    $("#copy-to-all-button").click(function(e){
        e.preventDefault();
        $(".field-eventprovision-value input").each(function(){
            $(this).val($("#copy-to-all").val());
        });
    });
    var k = '.count($sections).';
    var i = 0;
    $(".submit-all").click(function(e){
        e.preventDefault();
        $(".form-sections").each(function(){
            data = $(this).serialize();
            url = $(this).attr("action");
            $.post({
                  url: url,
                  data:data,
                  success: function(response){
                    i++;
                    if (i>=k)
                    {
                        window.location.reload();
                    }
                  }
                });
        });
        
        
    });
');
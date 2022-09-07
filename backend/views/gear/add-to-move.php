<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */
/* @var $form yii\widgets\ActiveForm */
$return = true;
$title = Yii::t('app', 'Przenieś egzemplarze');
?>

<div class="gear-form">

    <?php $form = ActiveForm::begin(['id'=>'movement-form']); ?>

    <div class="row">
        <div class="col-md-12">
                

        <h1><?=$title?></h1>

            <?php  
 echo $form->field($model, 'quantity')->textInput(['maxlength' => true, 'autocomplete'=>"off"])->label(Yii::t('app', 'Liczba sztuk')); 
 ?>

    <div class="form-group">
        <?= Html::a(Yii::t('app', 'Zapisz'),'#', ['class' =>  'btn btn-success save-movement']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>
</div>
<?php 
$moveUrl = Url::to(['gear/add-to-move', 'w'=>$w, 'id'=>$gear->id, 'type'=>$type]);

$this->registerJS('
$(".save-movement").click(function(e){
    e.preventDefault();
    var data = $("#movement-form").serialize();
    $.post("'.$moveUrl.'", data, function(response){
                    if (response.success=="1")
                    {
                        var modal = $("#gear-movement");
                        modal.find(".modalContent").empty();
                        modal.modal("hide");
                        toastr.success("Sprzęt dodany do przeniesienia");
                        $(".gear-movement-label").html(response.total);
                    }else{
                        var modal = $("#gear-movement");
                        modal.find(".modalContent").empty();
                        modal.find(".modalContent").append(response);
                    }
                });
});
    ');
<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model \common\models\Customer; */
$model = new \common\models\Gear;
?>
<div class="warehouse-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'type')->dropDownList([1=>'Kod kreskowy', 2=>'Kod QR']) ?>
    <?= $form->field($model, 'photo')->dropDownList([1=>'Ze zdjęciami', 2=>'Bez zdjęć']) ?>


    <?php ActiveForm::end(); ?>
<?=Html::a(Yii::t('app', 'Generuj'), ['get-codes', 'c'=>$c, 's'=>$s], ['class'=>'btn btn-primary generate-codes-button'])?>
</div>

<?php
$this->registerJs('
$(".generate-codes-button").click(function(e){
        e.preventDefault();
        href = $(this).attr("href")+"&type="+$("#gear-type").val()+"&photos="+$("#gear-photo").val();
        window.open(href, "_blank");
        $("#barcodes-generator").modal("hide");

});
    ');

?>
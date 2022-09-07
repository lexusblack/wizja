<?php
use common\models\Invoice;
use yii\bootstrap\Html;
use kartik\form\ActiveForm;
use kartik\tabs\TabsX;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\widgets\ActiveForm */
/* @var $payment \common\models\InvoicePaymentHistory */

?>

    <?php $form = ActiveForm::begin([
            'id'=>'invoices-form',
    ]);
    ?>
<div class="invoice-form panel-default panel">

<div class="panel-body">



    <div class="row">
        <div class="col-lg-12">
            <?php
                if (in_array($model->type, [Invoice::TYPE_CORRECTION_DATA, Invoice::TYPE_CORRECTION_ITEMS]))
                {
                    echo $form->field($model, 'parent_id')->dropDownList(\common\models\Invoice::getParentList(), ['disabled'=>true]);
                }

                ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <?php echo $form->field($model, 'type')->dropDownList(\common\models\Invoice::getTypeList(), ['disabled'=>false]); ?>
        </div>
        <div class="col-lg-3" id="destination">
            
        </div>
        <div class="col-lg-3">
		    <?php echo $form->field($model, 'owner_type')->dropDownList(\common\models\Invoice::getOwnerTypeList(), ['disabled'=>false]); ?>
        </div>
        <div class="col-lg-3">
            <?php
            $ownerListUrl = \yii\helpers\Url::to(['/finances/default/list-owners']);
            echo $form->field($model, 'owner_id')->widget(Select2::className(), [
	            'initValueText' => $model->getOwnerDisplayLabel(),
	            'pluginOptions' => [
		            'placeholder'=>Yii::t('app', 'Przypisz do'),
		            'allowClear' => true,
		            'multiple' => false,
		            'tags' => false,
		            'ajax' => [
			            'delay' => 50,
			            'url' => new \yii\web\JsExpression('
			            function(){
			                return "'.$ownerListUrl.'?type="+$("#invoice-owner_type").val();
			                }
			            '),
			            'dataType' => 'json',
			            'data' => new \yii\web\JsExpression('function(params) {
                                       return {q:params.term};
                                    }')
		            ],
	            ],
            ]); ?>
        </div>
    </div>

    <?php 

    echo TabsX::widget([
            'items'=> [
                [
                    'label'=>Yii::t('app', 'Informacje podstawowe'),
                    'content'=>$this->render('_form1', ['model'=>$model, 'form'=>$form, 'payment'=>$payment]),
                    'active'=>true
                ],
                
                [
                    'label'=>Yii::t('app', 'Zaawansowane'),
                    'content'=>$this->render('_form2', ['model'=>$model, 'form'=>$form]),
                ],
                [
                    'label'=>Yii::t('app', 'Opis'),
                    'content'=>$this->render('_form3', ['model'=>$model, 'form'=>$form]),
                ],
                [
                    'label'=>Yii::t('app', 'Pliki'),
                    'content'=>$this->render('_form4', ['model'=>$model, 'form'=>$form]),
                ]
            ],
            'enableStickyTabs' => true,
        ]);

        
    ?>

    <?php echo $this->render('_items', ['model'=>$model, 'form'=>$form, 'items'=>$items]); ?>
    <div class="row">
        <div class="col-lg-6 col-lg-offset-6">
            <div class="panel_mid_blocks">
                <div class="panel_block">
            <table class="table">
                <tr>
                    <th><?php echo Yii::t('app', 'Kwota netto'); ?></th>
                    <th><?php echo Yii::t('app', 'Kwota VAT'); ?></th>
                    <th><?php echo Yii::t('app', 'Kwota brutto'); ?></th>
                </tr>
                <tr id="row-summary">
                    <td class="summary-netto"></td>
                    <td class="summary-tax"></td>
                    <td class="summary-brutto"></td>
                </tr>

            </table>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
    <div id="source">
    <?php
            echo $form->field($model, 'customer_id')->widget(\common\widgets\CustomerField::className(), []);
                //->hint('Możesz dodać nową opcję wpisując nazwę i naciskając "Enter"');
            ?>
    </div>
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
        <?php echo Html::a(Yii::t('app', 'Anuluj'), ['index'], ['class'=>'btn btn-danger']); ?>
    </div>

  

</div>

</div>
 <?php ActiveForm::end(); ?> 
<?php
$this->registerJs('
$("#invoice-owner_type").on("change", ownerClassChange);
ownerDisable();
function ownerClassChange(e)
{
    $("#invoice-owner_id").val("").trigger("change");
    ownerDisable();
}
function ownerDisable()
{
    var $el = $("#invoice-owner_type");
    var disabled = true;
    if ($el.val())
    {
        disabled = false;
    }
    $("#invoice-owner_id").prop("disabled", disabled);
}

$("#invoice-type").on("change", function(){
    var url = "/admin/finances/invoice/create?type=" + $(this).val();   
    
    var id = getURLParameter("id");
    if (id !== false) {
        url += "&id=" + id;
    }
    
    var owner = getURLParameter("owner");
    if (owner !== false) {
        url += "&owner=" + owner;
    }

    document.location.href = url;
});
$("#source").appendTo("#destination");

function getURLParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split("&");
    for (var i = 0; i < sURLVariables.length; i++)  {
        var sParameterName = sURLVariables[i].split("=");
        if (sParameterName[0] == sParam) {
            return sParameterName[1];
        }
    }
    return false;
}

');
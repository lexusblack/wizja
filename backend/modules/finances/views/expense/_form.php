<?php

use common\models\Expense;
use kartik\tabs\TabsX;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Expense */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="expense-form panel panel-default">
    <?php $form = ActiveForm::begin([
            'id' => 'expenses-form',
    ]); ?>
    <div class="panel-body">

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'type')->dropDownList(Expense::getTypeList(), ['disabled'=>false]) ?>
        </div>
        <div class="col-lg-3" id="destination">
            
        </div>
        <div class="col-lg-3">
            <?php echo $form->field($model, 'expense_type')->dropDownList(Expense::getExpenseTypeList()) ?>
        </div>
        <div class="col-lg-3">
            <?php
            echo $form->field($model, 'eventIds')->widget(Select2::className(), [
                'data'=>\common\models\Event::getList(),
                'pluginOptions' => [
                    'placeholder'=> Yii::t('app', 'Przypisz do eventu'),
                    'allowClear' => true,
                    'multiple' => true,
                    'tags' => false,
                ],
                'pluginEvents'=>[
                    'change'=>'function(){
                        $("a[href=\"#items-tab\"]").tab("show");
                        loadEventExpenses();
                        
                    }',
                ]
            ]); ?>
        </div>
    </div>
    <?php  
    echo TabsX::widget([
        'items'=> [
            [
                'label'=> Yii::t('app', 'Informacje podstawowe'),
                'content'=>$this->render('_form1', ['model'=>$model, 'form'=>$form, 'payment'=>$payment]),
                'active'=>true,
                'options'=>[
                    'id'=>'default-tab'
                ]
            ],
            [
                'label'=> Yii::t('app', 'Import z zagranicy i inne zaawansowane'),
                'content'=>$this->render('_form2', ['model'=>$model, 'form'=>$form]),
            ],
            
            [
                'label'=> Yii::t('app', 'Stawki'),
                'content'=>$this->render('_items1', ['model'=>$model, 'form'=>$form, 'items'=>$rates]),
            ], 
            [
                'label'=> Yii::t('app', 'Pliki'),
                'content'=>$this->render('_form4', ['model'=>$model, 'form'=>$form]),
            ],
        ],
        'enableStickyTabs' => false,
    ]); 
    ?>

    <div id="items-tab">
        <?php echo $this->render('_items2', ['model'=>$model, 'form'=>$form, 'items'=>$items]); ?>
    </div>
    <div class="form-group">
    <div id="source">
    <?php
           echo $form->field($model, 'customer_id')->widget(\common\widgets\CustomerField::className(), []);
                //->hint('Możesz dodać nową opcję wpisując nazwę i naciskając "Enter"');
            ?>
    </div>
        <?= Html::submitButton(Yii::t('app', Yii::t('app', 'Zapisz')), ['class' =>  'btn btn-success']) ?>
        <?php echo Html::a(Yii::t('app', Yii::t('app', 'Anuluj')), ['index'], ['class'=>'btn btn-danger']); ?>
    
    </div>

    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs('
    //expenseItemsToggle();
//    function expenseItemsToggle(){
//        var tab = $("[href=\"#items-tab\"]").parent("li");
//        
//         if ($("#expense-expense_type").val() <100)
//         {
//            tab.show();
//         }
//         else
//         {
//            tab.hide();
//            if (tab.hasClass("active"))
//            {
//                $("a[href=\"#default-tab\"]").tab("show");
//            }
//         }
//    }
    function expenseItemsToggle(){
        var tab = $("#items-tab")
        
         if ($("#expense-expense_type").val() <100)
         {
            tab.show();
         }
         else
         {
           // tab.hide();
         
         }
    }
    $("#expense-expense_type").on("change", expenseItemsToggle);

    $("#source").appendTo("#destination");
    
');
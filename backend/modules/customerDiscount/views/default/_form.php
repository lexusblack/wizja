<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use wbraganca\dynamicform\DynamicFormWidget;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerDiscount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-discount-form">

    <?php $form = ActiveForm::begin(['id'=>'dynamic-form']); ?>

    <?= $form->field($model, 'discount')->textInput() ?>
    <div class="col-xs-6">
        <h3><?= Yii::t('app', 'Klienci') ?></h3>

        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            'widgetBody' => '.container-items-cust', // required: css class selector
            'widgetItem' => '.item-cust', // required: css class
            'limit' => 999, // the maximum times, an element can be cloned (default 999)
            'min' => 1, // 0 or 1 (default 1)
            'insertButton' => '#add-item-cust', // css class
            'deleteButton' => '.remove-item-cust', // css class
            'model' => new \common\models\CustomerDiscountCategory(),
            'formId' => 'dynamic-form',
            'formFields' => [
                'gear_cat_id',
                'customer_id',
                'discount',
            ],
        ]); ?>
        <div class="row">
            <div class="col-xs-12">
                <div class="pull-right">
                    <button type="button" id="add-item-cust" class="add-item btn btn-success"><i class="glyphicon glyphicon-plus"></i></button>
                </div>
            </div>
        </div>
        
        <hr>

        <div class="container-items-cust"><!-- widgetContainer -->
        <?php foreach ($customers as $i => $customer): ?>
            <div class="item-cust panel panel-default"><!-- widgetBody -->
                <div class="panel-heading">
                    <h3 class="panel-title pull-left"><?= Yii::t('app', 'Klient') ?></h3>
                    <div class="pull-right">
                        <button type="button" class="remove-item-cust btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <div class="col-sm-12">
                        <?= $form->field($customer, "[$i]customer_id")->widget(Select2::classname(), [
                            'data' => \common\models\Customer::getList(),
                            'language' => 'pl',
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);?>

                    </div>
                   
                </div><!-- .row -->

            </div>
        
        <?php endforeach; ?>
        </div>
        <?php DynamicFormWidget::end(); ?>
    </div>
    <div class="col-xs-6">
        <h3><?= Yii::t('app', 'Kategorie') ?></h3>

        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper_cat', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            'widgetBody' => '.container-items', // required: css class selector
            'widgetItem' => '.item', // required: css class
            'limit' => 999, // the maximum times, an element can be cloned (default 999)
            'min' => 1, // 0 or 1 (default 1)
            'insertButton' => '#add-item', // css class
            'deleteButton' => '.remove-item', // css class
            'model' => new \common\models\CustomerDiscountCategory(),
            'formId' => 'dynamic-form',
            'formFields' => [
                'gear_cat_id',
                'customer_id',
                'discount',
            ],
        ]); ?>
        <div class="row">
            <div class="col-xs-12">
                <div class="pull-right">
                    <button type="button" id="add-item" class="add-item btn btn-success"><i class="glyphicon glyphicon-plus"></i></button>
                </div>
            </div>
        </div>
        
        <hr>

        <div class="container-items"><!-- widgetContainer -->
        <?php foreach ($discounts as $i => $discount): ?>
            <div class="item panel panel-default"><!-- widgetBody -->
                <div class="panel-heading">
                    <h3 class="panel-title pull-left"><?= Yii::t('app', 'Kategoria') ?></h3>
                    <div class="pull-right">
                        <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <div class="col-sm-12">
                        <?= $form->field($discount, "[{$i}]category_id")->widget(\kartik\tree\TreeViewInput::className(), [
                            // single query fetch to render the tree
                            // use the Product model you have in the previous step
                            'query' => \common\models\GearCategory::find()->addOrderBy('root, lft'),
                            'headingOptions'=>['label'=>'Categories'],
                            'asDropdown' => true,   // will render the tree input widget as a dropdown.
                            'multiple' => false,     // set to false if you do not need multiple selection
                            'fontAwesome' => false,  // render font awesome icons
                            
                            'options'=>['id' => "catID{$i}",'treeOptions' => ['id'=>'tree_id'.$i]],
                        ])->label(Yii::t('app', 'Wybierz z listy'))
                        ?>

                    </div>
                   
                </div><!-- .row -->

            </div>
        
        <?php endforeach; ?>
        </div>
        <?php DynamicFormWidget::end(); ?>
    </div>

    <div class="clearfix"></div>

    <hr>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Zapisz') : Yii::t('app', 'Aktualizuj'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs('
$(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    $(item).find(".kv-node-detail").click(function(){ 
        var _this = $(this),
        li = _this.closest("li"),
        cat_id = li.data("key"),
        name = _this.find(".kv-node-label").text(),
        inp = _this.closest(".kv-tree-dropdown").find("input[type=\"hidden\"]"),
        container = _this.closest(".kv-tree-dropdown-container"),
        caret = "<div class=\"kv-carets\"><span class=\"caret kv-dn\"></span><span class=\"caret kv-up\"></span></div>";
        text_inp = container.find(".kv-tree-input");

        inp.val(cat_id);
        text_inp.html(caret+name);
        container.removeClass("opened");

    });
});

$(".dynamicform_wrapper_cat").on("afterInsert", function(e, item) {
    $(item).find(".kv-node-detail").click(function(){ 
        var _this = $(this),
        li = _this.closest("li"),
        cat_id = li.data("key"),
        name = _this.find(".kv-node-label").text(),
        inp = _this.closest(".kv-tree-dropdown").find("input[type=\"hidden\"]"),
        container = _this.closest(".kv-tree-dropdown-container"),
        caret = "<div class=\"kv-carets\"><span class=\"caret kv-dn\"></span><span class=\"caret kv-up\"></span></div>";
        text_inp = container.find(".kv-tree-input");

        inp.val(cat_id);
        text_inp.html(caret+name);
        container.removeClass("opened");

    });
});





');

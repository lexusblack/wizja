<div class="row">
    <div class="col-lg-6">
        <?php //echo $form->field($model, 'vat')->dropDownList(\backend\modules\finances\Module::getVatList()); ?>
        <?php echo $form->field($model, 'currency')->dropDownList(\backend\modules\finances\Module::getCurrencyList()); ?>
    </div>
    <div class="col-lg-6">

    </div>
</div>
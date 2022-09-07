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
    <?php
            echo $form->field($model, 'category_ids')->widget(\kartik\tree\TreeViewInput::className(), [
                // single query fetch to render the tree
                // use the Product model you have in the previous step
                'query' => \common\models\GearCategory::find()->where(['active'=>1])->andWhere(['lvl'=>1])->addOrderBy('root, lft'),
                'headingOptions'=>['label'=>'Categories'],
                'asDropdown' => true,   // will render the tree input widget as a dropdown.
                'multiple' => true,     // set to false if you do not need multiple selection
                'fontAwesome' => false,  // render font awesome icons
                //'options'=>['disabled' => true],
            ])
            ?>      

    <div class="clearfix"></div>

    <hr>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Zapisz') : Yii::t('app', 'Aktualizuj'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>



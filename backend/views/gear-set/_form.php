<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearSet */
/* @var $form yii\widgets\ActiveForm */

\mootensai\components\JsBlock::widget(['viewFile' => '_script', 'pos'=> \yii\web\View::POS_END, 
    'viewParams' => [
        'class' => 'GearSetItem', 
        'relID' => 'gear-set-item', 
        'value' => \yii\helpers\Json::encode($model->gearSetItems),
        'isNewRecord' => ($model->isNewRecord) ? 1 : 0
    ]
]);

\mootensai\components\JsBlock::widget(['viewFile' => '_script', 'pos'=> \yii\web\View::POS_END, 
    'viewParams' => [
        'class' => 'GearSetOuterItem', 
        'relID' => 'gear-set-outer-item', 
        'value' => \yii\helpers\Json::encode($model->gearSetItems),
        'isNewRecord' => ($model->isNewRecord) ? 1 : 0
    ]
]);
?>

<div class="gear-set-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Nazwa']) ?>

    <?php
            echo $form->field($model, 'category_id')->widget(\kartik\tree\TreeViewInput::className(), [
                // single query fetch to render the tree
                // use the Product model you have in the previous step
                'query' => \common\models\GearCategory::find()->where(['active'=>1])->addOrderBy('root, lft'),
                'headingOptions'=>['label'=>Yii::t('app', 'Kategorie')],
                'asDropdown' => true,   // will render the tree input widget as a dropdown.
                'multiple' => false,     // set to false if you do not need multiple selection
                'fontAwesome' => false,  // render font awesome icons
                //'options'=>['disabled' => true],
            ])
            ?>
                <div class="form-group">
                <?php echo Html::activeHiddenInput($model, 'photo'); ?>
                <?php echo Html::activeLabel($model, 'photo'); ?>
                <?php echo devgroup\dropzone\DropZone::widget([
                    'url'=>\common\helpers\Url::to(['upload']),
                    'name'=>'file',
                    'options'=>[
                        'maxFiles' => 1,
                    ],
                    'eventHandlers' => [
                        'success' => 'function(file, response) {
               $("#'.Html::getInputId($model, 'photo').'").val(response.filename);

            }',
                    ]
                ]); ?>
                <?php echo Html::error($model, 'photo'); ?>
            </div>

    <?php
    echo $this->render('_formGearSetItem', [
                'row' => \yii\helpers\ArrayHelper::toArray($model->gearSetItems),
                
            ]);
    echo $this->render('_formGearSetOuterItem', [
                'row' => \yii\helpers\ArrayHelper::toArray($model->gearSetOuterItems),
                
            ]);
/*
    echo kartik\tabs\TabsX::widget([
        'items' => $forms,
        'position' => kartik\tabs\TabsX::POS_ABOVE,
        'encodeLabels' => false,
        'pluginOptions' => [
            'bordered' => true,
            'sideways' => true,
            'enableCache' => false,
        ],
    ]);
    */
    ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Zapisz' : 'Zapisz', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

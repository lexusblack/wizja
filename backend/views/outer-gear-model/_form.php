<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OuterGearModel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="outer-gear-model-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
    
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

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>
            <?php echo $form->field($model, 'type')->dropDownList(\common\models\Gear::getTypeList()) ?>
            <?php echo $form->field($model, 'unit')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
            <?= $form->field($model, 'power_consumption')->textInput() ?>

    <?= $form->field($model, 'width')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Długość')]) ?>

    <?= $form->field($model, 'height')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Wysokość')]) ?>

    <?= $form->field($model, 'depth')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Głębokość')]) ?>

    <?= $form->field($model, 'weight')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Waga')]) ?>


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
               $("#outergearmodel-photo").val(response.filename);

            }',
                    ]
                ]); ?>
                <?php echo Html::error($model, 'photo'); ?>
            </div>
    <?= $form->field($model, 'info')->widget(\yii\redactor\widgets\Redactor::className(), [
                'clientOptions' => [
                    'buttons' => ['html','formatting', 'bold', 'italic', 'deleted',
                        'unorderedlist', 'orderedlist','outdent', 'indent', 'alignment', 'link', 'horizontalrule'],
//                    'plugins' => ['clips', 'fontcolor','imagemanager']
                ]
            ]);?>
            
    <div class="form-group submit-button">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs('

$("#'.Html::getInputId($model, 'type').'").change(function(e){
    type = $(this).val();
    if (type==1)
    {
        $(".form-group").show();
        $(".gear-form h2").show();
    }
    if (type == 2)
    {
        $(".form-group").hide();
        $(".field-'.Html::getInputId($model, 'name').'").show();
        $(".field-'.Html::getInputId($model, 'type').'").show();
        $(".field-'.Html::getInputId($model, 'category_id').'").show();
        $(".field-'.Html::getInputId($model, 'unit').'").show();
        $(".field-'.Html::getInputId($model, 'info').'").show();
        $(".submit-button").show();

    }
    if (type ==3)
    {
        $(".form-group").show();
        $(".field-'.Html::getInputId($model, 'power_consumption').'").hide();
    }
    });
    ');
?>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProvisionGroup */
/* @var $form yii\widgets\ActiveForm */

\mootensai\components\JsBlock::widget(['viewFile' => '/provision-group/_script', 'pos'=> \yii\web\View::POS_END, 
    'viewParams' => [
        'class' => 'EventProvisionGroupProvision', 
        'relID' => 'event-provision-group-provision', 
        'value' => \yii\helpers\Json::encode($model->eventProvisionGroupProvisions),
        'isNewRecord' => ($model->isNewRecord) ? 1 : 0
    ]
]);
?>

<div class="provision-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

    <?= $form->field($model, 'team_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\Team::find()->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'wybierz zespół')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?php
            echo $form->field($model, 'level')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>0,
                ]
            ]);
            ?>

    

    <?php
            echo $form->field($model, 'provision')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>

    <?= $form->field($model, 'type')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \common\models\ProvisionGroup::getTypes(),
        'options' => ['placeholder' => Yii::t('app', 'wybierz typ')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>
    <?php echo $form->field($model, 'add_to_users')->dropDownList([1=>Yii::t('app', 'Tak'), 0=>Yii::t('app', 'Nie')]) ?>
    <?php echo $form->field($model, 'main_only')->dropDownList([1=>Yii::t('app', 'Tak'), 0=>Yii::t('app', 'Nie')]) ?>

    <?php
    $forms = [
        [
            'label' => '<i class="glyphicon glyphicon-book"></i> ' . Html::encode(Yii::t('app', 'Prowizje od poszczególnych sekcji')),
            'content' => $this->render('_formEventProvisionGroupProvision', [
                'row' => \yii\helpers\ArrayHelper::toArray($model->eventProvisionGroupProvisions),
            ]),
        ],
    ];
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
    ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
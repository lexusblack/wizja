<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gear-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-12">

            <?= $form->field($model, 'description')->label(Yii::t('app', 'Podaj powód usunięcia'))->widget(\yii\redactor\widgets\Redactor::className(), [
                'clientOptions' => [
                    'buttons' => ['html','formatting', 'bold', 'italic', 'deleted',
                        'unorderedlist', 'orderedlist','outdent', 'indent', 'alignment', 'link', 'horizontalrule'],
                ]
            ]);?>


        </div>
    </div>





    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;


/* @var $this yii\web\View */
/* @var $model \backend\models\SendOfferMail */

$this->title = Yii::t('app', 'Pomoc techniczna - zgłoszenie');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'subject')->textInput() ?>
    <?=$form->field($model, 'priority')->dropDownList([1=>Yii::t('app', 'Niski'), 2=>Yii::t('app','Wysoki'), 3=>Yii::t('app','Uniemożliwiający pracę')]) ?>
    <?=$form->field($model, 'type')->dropDownList([1=>Yii::t('app', 'Błąd'), 2=>Yii::t('app','Pytanie'), 3=>Yii::t('app','Nowa funkcjonalność')]) ?>
    <?= $form->field($model, 'link')->textInput() ?>
    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>
    <?php if (Yii::$app->params['companyID']=='admin'){ ?>
        <?= $form->field($model, 'username')->textInput() ?>
        <?= $form->field($model, 'usermail')->textInput() ?>
        <?php echo $form->field($model, 'company')->dropDownList(\common\models\Company::getList()); ?>

        
    <?php } ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Wyślij'), ['class' => 'btn btn-primary']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>

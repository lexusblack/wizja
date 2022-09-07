<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Zmiana hasła';
?>

<div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>

                <img alt="image" class="img-responsive" src="/themes/e4e/img/newem.png">

            </div>
            <h3><?= Yii::t('app', 'Witaj w New Event Management') ?></h3>
            <p><?= Yii::t('app', 'Perfekcyjnym narzędziu do zarządzania twoją firmą eventową!') ?>
            </p>
            <p><?= Yii::t('app', 'To twoje pierwsze logowanie. Zmień swoje hasło!') ?></p>
       <?php
        $form = ActiveForm::begin([

            ]);
        ?>

       <?php echo $form->field($model, 'old_password')->label(false)
                    ->passwordInput(['placeholder' => $model->getAttributeLabel('old_password')]); ?>
       <?php echo $form->field($model, 'password')->label(false)
                    ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]); ?>
       <?php echo $form->field($model, 'password_repeat')->label(false)
                    ->passwordInput(['placeholder' => $model->getAttributeLabel('password_repeat')]); ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Zapisz', ['class' =>  'btn btn-primary btn-block btn-flat']) ?>
        <?php ActiveForm::end(); ?>
    </div>
            <p class="m-t"> <small><?= Yii::t('app', 'New Management System by newsystems © 2017') ?></small> </p>
        </div>
    </div>

<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @var $this  yii\web\View
 * @var $model dektrium\rbac\models\Role
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation'   => true,
]) ?>

<?= $form->field($model, 'description')->textInput() ?>
<?php echo $form->field($model, 'superuser')->dropDownList([1=>Yii::t('app', 'Tak'), 0=>Yii::t('app', 'Nie')]) ?>

<?= Html::submitButton(Yii::t('app','Zapisz'), ['class' => 'btn btn-success btn-block']) ?>

<?php ActiveForm::end() ?>
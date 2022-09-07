<?php
use yii\bootstrap\Html;
use kartik\form\ActiveForm;
/* @var $this yii\web\View */
/* @var $model \backend\modules\finances\models\SendForm */

$this->title = Yii::t('app', 'Wyślij');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Koszty'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="invoice-send">
    <h3><?= Yii::t('app', 'Koszty') ?></h3>
    <ol>
        <?php foreach ($model->invoices as $invoice): ?>
            <li>
                <?php echo $invoice->number; ?>
            </li>
        <?php endforeach; ?>
    </ol>
    <?php
        $form = ActiveForm::begin([

        ]);
    ?>

    <?php echo $form->field($model, 'to')->textInput(['maxlength'=>true])->hint(Yii::t('app', 'Adresy email oddzielone średnikami ";"')); ?>
    <div>
        <?php echo Html::submitButton(Yii::t('app', 'Wyślij'), ['class'=>'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

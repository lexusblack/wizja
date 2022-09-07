<?php
use yii\bootstrap\Html;
use kartik\form\ActiveForm;
/* @var $this yii\web\View */
/* @var $model \backend\modules\finances\models\SendForm */

$this->title = Yii::t('app', 'Wyślij');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Faktury'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;



?>
<div class="invoice-send">
    <h3><?= Yii::t('app', 'Faktury') ?></h3>
    <ol>
        <?php foreach ($model->invoices as $invoice): ?>
            <li>
                <?php echo $invoice->fullnumber; ?>
            </li>
        <?php endforeach; ?>
    </ol>
    <?php
        $form = ActiveForm::begin([

        ]);
    ?>

    <?php echo $form->field($model, 'to')->multiselect($model->getRecipients()); ?>
    <div>
        <?php echo Html::submitButton(Yii::t('app', 'Wyślij'), ['class'=>'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

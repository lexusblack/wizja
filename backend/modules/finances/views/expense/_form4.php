<?php
/* @var $this \yii\web\View; */
/* @var $model \common\models\Invoice */
/* @var $form \kartik\form\ActiveForm */
use yii\bootstrap\Html;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <?php echo Html::activeLabel($model, 'filename'); ?>
            <?php echo devgroup\dropzone\DropZone::widget([
                'url'=>\common\helpers\Url::to(['upload']),
                'name'=>'file',
                'options'=>[
                ],
                'eventHandlers' => [
                    'success' => 'function(file, response) {
                   $("#'.Html::getInputId($model, 'filename').'").val(response.filename);
                   $("#'.Html::getInputId($model, 'mime_type').'").val(response.type);
                   $("#'.Html::getInputId($model, 'extension').'").val(response.extension);
                   $("#'.Html::getInputId($model, 'base_name').'").val(response.name);
    
                }',
                    'sending' => 'function(file, xhr, formData){
                    formData.append("expense_id", "'.$model->id.'");
                }',
                ]
            ]); ?>
            <div class="text-muted">
                <?= Yii::t('app', 'Możesz dodawać wiele plików naraz, ich typ będzie taki, jak ustawiony poniżej w czasie dodawania.') ?>
            </div>
            <?php echo Html::error($model, 'filename'); ?>
        </div>

    </div>
</div>

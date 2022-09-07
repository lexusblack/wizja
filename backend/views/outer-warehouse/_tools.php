<?php
/* @var $this \yii\web\View */
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\widgets\DatePicker;

$request = Yii::$app->request;
?>
<div class="row">
    <div class="tools warehouse-tools col-md-12">
    <div class="ibox">
        <div class="search-form">
                <?php echo Html::beginForm('/admin/'.Yii::$app->request->getPathInfo(), 'get', ['class'=>'form-inline']); ?>

            <div class="form-group">
                <?php echo Html::textInput('q', $request->get('q'), ['placeholder'=> Yii::t('app', 'Szukaj'), 'class'=>'form-control']); ?>
            </div>
            <div class="form-group">
            <?php
            echo DatePicker::widget([
                'name' => 'from_date',
                'value' => $request->get('from_date'),
                'type' => DatePicker::TYPE_RANGE,
                'name2' => 'to_date',
                'value2' => $request->get('to_date'),
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]);
            ?>
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><?=  Yii::t('app', 'Szukaj') ?></button>
            <?php echo Html::endForm(); ?>
        </div>
        </div>
    </div>
</div>

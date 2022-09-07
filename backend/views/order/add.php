<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->title = Yii::t('app', 'Stwórz zamówienie');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zamówienia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <h2><?= Yii::t('app', 'Firma') ?>: <?=$company->name?></h2>
	<?php $form = ActiveForm::begin(['options'=>[ 'autocomplete'=>"off"]]); ?>
    <div class="form-group">

    <?= $form->field($model, 'contact_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \common\models\Contact::getList($company->id),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz...')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label(Yii::t('app', "Osoba kontaktowa")); ?>
    <?= $form->field($model, 'company_id')->hiddenInput()->label(false); ?>

    <?= $form->field($model, 'reception')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => Yii::t('app', 'Data odbioru'),
                'autoclose' => true,
            ]
        ],
    ])->label(Yii::t('app', "Data odbioru")); ?>
    <?= $form->field($model, 'return')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => Yii::t('app', 'Data zwrotu'),
                'autoclose' => true,
            ]
        ],
    ])->label(Yii::t('app', "Data zwrotu")) ?>

<table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?= Yii::t('app', 'Nazwa') ?></th>
                                <th><?= Yii::t('app', 'Ilość') ?></th>
                                <th><?= Yii::t('app', 'Start pracy') ?></th>
                                <th><?= Yii::t('app', 'Koniec pracy') ?></th>
                                <th><?= Yii::t('app', 'Data odbioru') ?></th>
                                <th><?= Yii::t('app', 'Data zwrotu') ?></th>
                                <th><?= Yii::t('app', 'Cena') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php 
                            $i=0;
                            foreach ($model->eventOuterGear as $eog){ 
                                $i++;
                                $base = "[".$eog->event_id."_".$eog->outer_gear_id."]";
                                ?>
                            <tr>
                                <td><?=$i?></td>
                                <td><?=$eog->outerGear->name?></td>
                                <td><?=$eog->quantity?></td>
                                <td>    <?= $form->field($eog, $base.'start_time')->widget(\kartik\datecontrol\DateControl::classname(), [
                                        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                                        'saveFormat' => 'php:Y-m-d H:i:s',
                                        'ajaxConversion' => true,
                                        'options' => [
                                            'pluginOptions' => [
                                                'placeholder' => Yii::t('app', 'Start pracy'),
                                                'autoclose' => true
                                            ]
                                        ],
                                    ])->label(false);; ?>
                                       
                                    </td>
                                <td>    <?= $form->field($eog, $base.'end_time')->widget(\kartik\datecontrol\DateControl::classname(), [
                                        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                                        'saveFormat' => 'php:Y-m-d H:i:s',
                                        'ajaxConversion' => true,
                                        'options' => [
                                            'pluginOptions' => [
                                                'placeholder' => Yii::t('app', 'Koniec pracy'),
                                                'autoclose' => true,
                                            ]
                                        ],
                                    ])->label(false);; ?>
                                       
                                    </td>
                                    <td>
                                        <?= $form->field($eog, $base.'reception_time')->widget(\kartik\datecontrol\DateControl::classname(), [
                                        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                                        'saveFormat' => 'php:Y-m-d H:i:s',
                                        'ajaxConversion' => true,
                                        'options' => [
                                            'pluginOptions' => [
                                                'placeholder' => Yii::t('app', 'Data odbioru'),
                                                'autoclose' => true,
                                            ]
                                        ],
                                    ])->label(false);; ?>
                                    </td>
                                    <td>
                                        <?= $form->field($eog, $base.'return_time')->widget(\kartik\datecontrol\DateControl::classname(), [
                                        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                                        'saveFormat' => 'php:Y-m-d H:i:s',
                                        'ajaxConversion' => true,
                                        'options' => [
                                            'pluginOptions' => [
                                                'placeholder' => Yii::t('app', 'Data zwrotu'),
                                                'autoclose' => true,
                                            ]
                                        ],
                                    ])->label(false);; ?>
                                    </td>
                                    <td>
                                        <?= $form->field($eog, $base.'price', ['template' => '{input}'])->textInput(); ?>
                                        <?= $form->field($eog, $base.'event_id')->hiddenInput()->label(false); ?>
                                        <?= $form->field($eog, $base.'outer_gear_id')->hiddenInput()->label(false); ?>
                                    </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
        <?= Html::submitButton(Yii::t('app', 'Stwórz'), ['class' => 'btn btn-primary']) ?>
    </div>
	<?php ActiveForm::end(); ?>
</div>

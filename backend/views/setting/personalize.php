<?php
/* @var $this yii\web\View */
/* @var $model \backend\models\SettingsForm; */

use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\ColorInput;

$this->title = Yii::t('app', 'Personalizacja');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app', 'Ustawienia'), 'url'=>['setting/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-personalize">
        <h1><?= Yii::t('app', 'Personalizacja') ?></h1>
    <?php
    $form = ActiveForm::begin([
        'type' => ActiveForm::TYPE_VERTICAL,
        'fieldConfig' => [
        ],
        'formConfig' => [
            'showLabels'=>true,
        ],
    ]);
    ?>
    <div class="row">
        <div class="col-md-4">
            <div class="panel_mid_blocks">
                <div class="panel_block" style="margin-bottom: 0;">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Oddziały') ?></h4>
                    </div>
                </div>
            </div>
            <?php foreach ($departments as $index=>$deparment): ?>
                <?php echo $form->field($deparment, "[$index]color")->widget(\kartik\widgets\ColorInput::className(),[
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz kolor...'),
                    ],
                ])->label($deparment->name);
                ?>
                <?php endforeach; ?>
            <div class="panel_mid_blocks">
                <div class="panel_block" style="margin-bottom: 0;">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Podgląd w kalendarzu') ?></h4>
                        <?php echo $form->field($model, 'blackFieldArray')->widget(\kartik\widgets\Select2::className(), [
                        'data' => \common\models\Event::getBlackFieldList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'multiple' => true,
                        ],
                    ])->label(Yii::t('app', 'Informacje na czarnym polu w kalendarzu'));
                    ?>
                    </div>
                </div>
            </div>
                <h3><?= Yii::t('app', 'Numeracja') ?>:</h3>
            <?php echo $form->field($model, 'eventNumber')->textInput(); ?>
            <?php echo $form->field($model, 'rentNumber')->textInput(); ?>
            <?php echo $form->field($model, 'offerNumber')->textInput(); ?>
            <p class="text-muted">
        <?= Yii::t('app', 'Dostępne znaczniki') ?>:<br />
        [<?= Yii::t('app', 'numer') ?>] - <?= Yii::t('app', 'generowany podczas stworzenia obiektu') ?><br />
        [<?= Yii::t('app', 'dzień') ?>] - <?= Yii::t('app', 'dzień z daty stworzenia') ?><br />
        [<?= Yii::t('app', 'miesiąc') ?>] - <?= Yii::t('app', 'miesiąc z daty stworzenia') ?><br />
        [<?= Yii::t('app', 'rok') ?>] - <?= Yii::t('app', 'rok z daty  stworzenia(czterocyfrowy) np. 2014') ?><br />
        [<?= Yii::t('app', 'rok:format_dwucyfrowy') ?>] - <?= Yii::t('app', 'rok z daty  stworzenia (dwucyfrowy) np. 14') ?>
    </p>
            
        </div>
        <div class="col-md-4">
            <div class="panel_mid_blocks">
                <div class="panel_block" style="margin-bottom: 0;">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Typ Eventu') ?></h4>
                    </div>
                </div>
            </div>
            <?php echo $form->field($model, 'meetingColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'meetingLineColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'meetingTextColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'personalColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'personalTextColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'rentColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'rentLineColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'rentTextColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'vacationColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'vacationTextColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'vacationAcceptedColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'vacationTextAcceptedColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'vacationRejectedColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'vacationTextRejectedColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'produkcjaColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'produkcjaLineColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'biuroColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'biuroLineColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'grafikaColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'grafikaLineColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'magazynColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'magazynLineColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
        </div>
        <div class="col-md-4">
            <div class="panel_mid_blocks">
                <div class="panel_block" style="margin-bottom: 0;">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Event') ?></h4>
                    </div>
                </div>
            </div>
            <?php echo $form->field($model, 'eventBaseColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'eventLineColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'partyColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'montageColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($model, 'disassemblyColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
        </div>
    </div>


    <?php if (Yii::$app->user->can('settingsPersonalizationSave')) { ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>
    <?php } ?>
    <?php
    ActiveForm::end();
    ?>

</div>
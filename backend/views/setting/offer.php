<?php
/* @var $this yii\web\View */
/* @var $model \backend\models\SettingsForm; */
/* @var $settings \backend\models\SettingsOfferForm */

use kartik\grid\GridView;
use yii\bootstrap\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\ColorInput;


$this->title = Yii::t('app', 'Oferty');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app', 'Ustawienia'), 'url'=>['setting/index']];
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="settings-offer">

        <?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-6">
        <?php echo $form->field($settings, 'firstDayPercent', [
            'addon' => ['append' => ['content'=>'%']]
        ])->textInput(); ?>
        <?php echo $form->field($settings, 'offerPayingTerm', [
            'addon' => ['append' => ['content'=>'dni']]
        ])->textInput(); ?>
        <?php //echo $form->field($settings, 'crewConfirm')->dropDownList([1=>Yii::t('app', 'TAK'), 2=>Yii::t('app', 'NIE')]) ?>
            <?php echo $form->field($settings, 'transportColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($settings, 'transportFontColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($settings, 'crewColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($settings, 'crewFontColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($settings, 'otherColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php echo $form->field($settings, 'otherFontColor')->widget(ColorInput::className(), [
                'options' => ['placeholder' => Yii::t('app', 'Wybierz kolor...')],
            ]); ?>
            <?php foreach ($categories as $index=>$cat): ?>
                <?php echo $form->field($cat, "[$index]color")->widget(\kartik\widgets\ColorInput::className(),[
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz kolor...'),
                    ],
                ])->label($cat->name);
                ?>
                <?php echo $form->field($cat, "[$index]font_color")->widget(\kartik\widgets\ColorInput::className(),[
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz kolor...'),
                    ],
                ])->label($cat->name." ".Yii::t('app', 'czcionka'));
                ?>
            <?php endforeach; ?>
    </div>
    <div class="col-md-6">
        <?php echo $form->field($settings, 'orderRules')->widget(\common\widgets\RedactorField::className()); ?>
        <?php if ($user->can('settingsOffersViewFile')) { ?>
            <h5 style="color: white;"><?= Yii::t('app', 'Załączniki oferty') ?></h5>
            <p class="alert alert-info"><?= Yii::t('app', 'Wybrane dokumenty zostaną dołączone na końcu każdej oferty') ?></p>
            <?php
        }
        if ($user->can('settingsOffersAddFile')) {
            echo $form->field($model, 'filename')->widget(\common\widgets\DropZoneField::className(), ['url' => \common\helpers\Url::to(['upload-offer']),
                'eventHandlers' => ['sending' => 'function(file, xhr, formData){
                            formData.append("type", $("#' . Html::getInputId($model, 'type') . '").val());
                        }', 'complete' => 'function(file, response){
                        $.get("", {}, function(r){
                        
                            var sel = ".attachment-list tbody";
                            $(sel).html($(r).find(sel).html());
                        });
                    
                    }',]])->hint(Yii::t('app', 'Możesz dodawać wiele plików naraz.'));
        }
        ?>


        <?= Html::activeHiddenInput($model, 'type'); ?>
        <div class="attachment-list">
            <div class="panel_mid_blocks">
                <div class="panel_block">
            <?php
            if ($user->can('settingsOffersViewFile')) {
                echo GridView::widget([
                    'dataProvider'=>$dataProvider,
                    'tableOptions' => [
                        'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                    ],
                    'columns' => [
                        [
                            'class'=>\yii\grid\SerialColumn::className(),
                        ],
                        [
                            'attribute' => 'filename',
                            'value'=>function($model)
                            {
                                return Html::a($model->filename, ['setting-attachment/download', 'id'=>$model->id]);
                            },
                            'format' => 'html',
                        ],
                        [
                            'class'=>\common\components\ActionColumn::className(),
                            'template'=>'{delete}',
                            'controllerId'=>'setting-attachment',
                            'visible' => $user->can('settingsOffersDeleteFile')
                        ]
                    ],
                ]);
            } ?>
                </div>
            </div>
        </div>
    </div>
</div>


    <?php if ($user->can('settingsOffersSave')) { ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs('


$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});


');
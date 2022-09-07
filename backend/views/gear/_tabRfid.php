<?php
/* @var $model \common\models\Gear; */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="panel-body">
    <div class="row">
        <div class="col-md-12">
            <div class="panel_mid_blocks">
                <div class="panel_block">
                    <?php $form = ActiveForm::begin();
                    echo Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success'])."<br><br>";
                    if ($rfids) {
                        foreach ($rfids as $i => $rfid) {
                            echo $form->field($rfid, "[" . $i . "]rfid_code")->label(Yii::t('app', 'Kod NEIS nr').' ' . ($i + 1));
                        }
                    } ?>

                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
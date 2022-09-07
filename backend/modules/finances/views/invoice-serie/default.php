<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin();
echo Html::a(Yii::t('app', 'Serie faktur'), ['index'], ['class' => 'btn btn-success']); ?>

    <table class="table" style="margin-top: 20px;">

    <?php
        foreach ($models as $id => $invoice) {
            echo "<tr><td style='width: 300px;'>" . $invoice[0] . ":</td><td>" . Html::dropDownList('defaultSeries['.$id.']', $invoice[1], $seriesList[$id]) . "</td></tr>";
        }
    ?>
    </table>

<?php

echo Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']);

ActiveForm::end();
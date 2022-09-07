<?php
use yii\bootstrap\Html;
use yii\helpers\Url;


$formatter = Yii::$app->formatter;
if (count($model->invoicePaymentHistories) > 0) {
    ?>

    <div class="alert alert-danger" role="alert">
        <?= Yii::t('app', 'Nie można skasować faktury numer') ?>: <?= Html::a($model->fullnumber, Url::toRoute(['invoice/view', 'id' => $model->id])); ?>,
        <?= Yii::t('app', 'ponieważ posiada przypisane do siebie przychody. Proszę najpierw skaskować wszystkie przychody tej faktury, a następnie samą fakturę.') ?>
    </div>

    <div class="alert alert-warning" role="alert">
        <div style="font-weight: bold"><?= Yii::t('app', 'Przychody do usunięcia') ?>:</div>
        <?php
        $i = 1;
        foreach ($data['paymentHistory'] as $payment) {
            echo "<div>";
            echo $i . ". [" . $formatter->asDate($payment['date']) . "] " . $payment['label'] . " " . $formatter->asCurrency($payment['amount']) . " " . Html::a(Html::icon('remove'), ['history-remove', 'id'=>$payment['id']], ['class'=>'remove-history'] );;
            echo "</div>";
            $i++;
        }
        ?>
    </div>
    <?php
}
else { ?>

    <div class="alert alert-success" role="alert">
        <?= Yii::t('app', 'Można już skaskować fakturę') ?>:
        <?= Html::a($model->fullnumber, Url::toRoute(['invoice/view', 'id' => $model->id])); ?>
        <?= Html::a(Html::tag('span', null, ['class' => 'glyphicon glyphicon-trash',]), Url::toRoute(['invoice/delete',
            'id' => $model->id]), ['title' => Yii::t('app', 'Usuń'), 'aria-label' => Yii::t('app', 'Usuń'),
            'data' => ['confirm' => Yii::t('app', 'Czy na pewno usunąć ten model?'), 'method' => 'post',]]);  ?>
    </div>

    <?php
}

$this->registerJs('
    $(".remove-history").on("click", function(e){
        e.preventDefault();
        $el = $(this);
        $.get($el.prop("href"), {}, function(){
            location.reload();
        });
        return false;
    });
');
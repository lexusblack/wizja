<?php
use yii\bootstrap\Html;
use yii\helpers\Url;

$count = 0;
foreach ($model->gearItems as $item) {
    if ($item->active) {
        $count++;
    }
}

if ($count > 0) {

    ?>

    <div class="alert alert-danger" role="alert">
        <?= Yii::t('app', 'Nie można skasować modelu') ?>: <?= Html::a($model->name, Url::toRoute(['gear/view', 'id' => $model->id])); ?>,
        <?= Yii::t('app', 'ponieważ posiada przypisane do siebie egzemplarze. Proszę najpierw skaskować wszystkie egzemplarze tego modelu,
        a następnie sam model.') ?>
    </div>

    <div class="alert alert-warning" role="alert">
        <div style="font-weight: bold"><?= Yii::t('app', 'Egzemplarze do usunięcia') ?>:</div>
        <?php
        $i = 1;
        foreach ($model->gearItems as $item) {
            if ($item->active) {
                echo "<div>";
                echo $i . ". " . Html::a($item->name, Url::toRoute(['gear-item/view',
                        'id' => $item->id])) . " ".Yii::t('app', "numer").": " . $item->number . " ";
                echo Html::a(Html::tag('span', null, ['class' => 'glyphicon glyphicon-trash',]), Url::toRoute(['gear-item/delete',
                    'id' => $item->id, 'goBack' => true]), ['title' => Yii::t('app', 'Usuń'), 'aria-label' => Yii::t('app', 'Usuń'),
                    'data' => ['confirm' => Yii::t('app', 'Czy na pewno usunąć ten egzemplarz?'), 'method' => 'post',]]);
                echo "</div>";
                $i++;
            }
        }
        ?>
    </div>
    <?php
}
else { ?>

    <div class="alert alert-success" role="alert">
        <?= Yii::t('app', 'Można już skaskować model') ?>:
        <?= Html::a($model->name, Url::toRoute(['gear/view', 'id' => $model->id])); ?>
        <?= Html::a(Html::tag('span', null, ['class' => 'glyphicon glyphicon-trash',]), Url::toRoute(['gear/delete',
        'id' => $model->id]), ['title' => Yii::t('app', 'Usuń'), 'aria-label' => Yii::t('app', 'Usuń'),
        'data' => ['confirm' => Yii::t('app', 'Czy na pewno usunąć ten model?'), 'method' => 'post',]]);  ?>
    </div>

<?php
}
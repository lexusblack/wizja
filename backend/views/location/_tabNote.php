<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
?>
<div class="panel-body">
<div class="panel_mid_blocks">
    <div class="row">
        <div class="col-md-12">
            <?php echo Html::a(Yii::t('app', 'Dodaj'), ['location-note/create', 'locationId'=>$model->id], ['class'=>'btn btn-success']); ?>
        </div>
    </div>

<div class="row">
    <div class="col-md-12">

        <div class="panel_mid_blocks">
            <div class="panel_block">
            <ul class="notes">
            <?php foreach ($model->locationNotes as $note){ ?>
                        <li>
                            <div>
                                <small><?=$note->create_time?></small>
                                <h1> </h1>
                                <p><?=$note->text?></p>
                                    <?= Html::a('<i class="fa fa-pencil"></i>', ['location-note/update', 'id' => $note->id], ['class'=>'edit',
                                    ]) ?>
                                    <?= Html::a('<i class="fa fa-trash-o"></i>', ['location-note/delete', 'id' => $note->id], [
                                        'data' => [

                                            'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                            </div>
                        </li>
            <?php } ?>
            </ul>
        </div>
        </div>
    </div>
</div>
</div>
</div>
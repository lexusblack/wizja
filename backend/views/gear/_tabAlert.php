<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
$user = Yii::$app->user;
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Uwagi'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
            <p>
            <?php echo $model->getItemsInfo(); ?>
            </p>
                    <?php if ($user->can('gearEdit')){ ?>
                        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edytuj'), ['update-info', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?php } ?>
            </div>
        </div>
    </div>
</div>
</div>
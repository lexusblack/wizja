<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
?>
<div class="panel-body">
<div class="panel_mid_blocks">
<?php if ($model->public<2){ ?>
    <div class="row">
        <div class="col-md-12">
            <?php echo Html::a(Yii::t('app', 'Dodaj'), ['room/create', 'locationId'=>$model->id], ['class'=>'btn btn-success']); ?>
        </div>
    </div>
    <?php } ?>

<div class="row">
    <div class="col-md-12">

        <div class="panel_mid_blocks">
            <div class="panel_block">
                <table class="table rooms-table">
                            <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th><center><?= Yii::t('app', 'Podkowa') ?></center></th>
                                <th><center><?= Yii::t('app', 'Bankiet') ?></center></th>
                                <th><center><?= Yii::t('app', 'Teatr') ?></center></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
            <?php foreach ($model->rooms as $room){
                $img = "";
                foreach ($room->roomPhotos as $rp)
                {
                    if ($img=="")
                        $img = '<a href="'.$rp->getFileUrl().'" title="'.$room->name.'" data-gallery=""><img class="room-photo" src="'.$rp->getFileUrl().'" alt=""></a>';
                    else
                        $img .= '<a href="'.$rp->getFileUrl().'" title="'.$room->name.'" data-gallery="" style="display:none" ><img class="room-photo" src="'.$rp->getFileUrl().'" alt=""></a>';
                }
                ?>
                        <tr>
                                <td><?=$img?></td>
                                <td><strong><?=$room->name?></strong></td>
                                <td class="text-success" style="font-size:16px; font-weight:100;"><center><?=$room->bankiet?></center></td>
                                <td class="text-success" style="font-size:16px; font-weight:100;"><center><?=$room->podkowa?></center></td>
                                <td class="text-success" style="font-size:16px; font-weight:100;"><center><?=$room->teatr?></center></td>
                                <td><?php
                                    if ($room->location->isEditable())
                                    {
                                    echo Html::a('<i class="fa fa-pencil"></i>', ['room/update', 'id'=>$room->id]);
                                    echo Html::a('<i class="fa fa-trash"></i>', ['room/delete', 'id'=>$room->id], [
                                                        'data' => [
                                                            'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                                                            'method' => 'post',
                                                        ],
                                                    ]);
                                    } 
                                     ?>
                                    
                                </td>
                            </tr>
            <?php } ?>
                            </tbody>
                        </table>

            </div>
        </div>
    </div>
</div>
</div>
</div>
                            <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
                                <div class="slides"></div>
                                <h3 class="title"></h3>
                                <a class="prev">‹</a>
                                <a class="next">›</a>
                                <a class="close">×</a>
                                <a class="play-pause"></a>
                                <ol class="indicator"></ol>
                            </div>
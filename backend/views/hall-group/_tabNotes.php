<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Customer; */


?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12">
        <?php echo Html::a(Yii::t('app', 'Dodaj'), ['/hall-group-note/create',  'hall_group_id'=>$model->id], ['class'=>'btn btn-success']); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
            <div class="ibox float-e-margins">
                    <div class="ibox-content">

                            <div>
                                <div class="feed-activity-list">
                                 <?php       
                                    foreach ($model->hallGroupNotes as $m)
                                        {
                                        ?>
                                        <div class="feed-element">
                                        <a href="#" class="pull-left">
                                            <?=$m->user->getUserPhoto("img-circle")?>
                                        </a>
                                        <div class="media-body ">
                                            <small class="pull-right text-navy"><?=$m->datetime?></small>
                                            <strong><?=$m->user->displayLabel?>:<br></strong><?=$m->text?></br>
                                            <div class="actions">
                                            <?php echo Html::a('<i class="fa fa-pencil"></i> '.Yii::t('app', 'Edytuj'), ['/hall-group-note/update', 'id'=>$m->id], ['class'=>'btn btn-xs btn-success']); ?>
                                            <?php echo Html::a('<i class="fa fa-trash"></i> '.Yii::t('app', 'Usuń'), ['/hall-group-note/delete', 'id'=>$m->id], ['class'=>'btn btn-xs btn-danger', 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']]); ?>
                                            </div>
                                        </div>
                                        </div>
                                        <?php
                                          
                                    }
                                    ?>                                
                                </div>
                            </div>
                    </div>
            </div>
    </div>
</div>
</div>

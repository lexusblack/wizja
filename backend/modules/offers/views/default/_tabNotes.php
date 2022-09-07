<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Customer; */


?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12">
        <?php echo Html::a(Yii::t('app', 'Dodaj'), ['/customer-note/create',  'customer_id'=>$model->customer_id, 'contact_id'=>$model->contact_id, 'offer_id'=>$model->id], ['class'=>'btn btn-success']); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
            <div class="ibox float-e-margins">
                    <div class="ibox-title">
                            <h5><?php echo Yii::t('app', 'Notatki'); ?></h5>
                    </div>
                    <div class="ibox-content">

                            <div>
                                <div class="feed-activity-list">
                                 <?php       
                                    foreach ($model->customerNotes as $m)
                                        {
                                        ?>
                                        <div class="feed-element">
                                        <a href="#" class="pull-left">
                                            <?=$m->user->getUserPhoto("img-circle")?>
                                        </a>
                                        <div class="media-body ">
                                            <small class="pull-right text-navy"><?=$m->datetime?></small>
                                            <strong><?=$m->user->displayLabel?>:<br></strong><?=$m->name?></br>
                                            <small class="text-muted"><?=$m->type?></small><br>
                                            <small class="text-muted"><?=Yii::t('app', 'Załączniki: ')?><?php echo Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/customer-note/add-file', 'id'=>$m->id]); ?></small><br>
                                            <?php foreach ($m->clientNoteAttachments as $a){ ?>
                                            <small class="text-muted"><?=Html::a('<i class="fa fa-file"></i> '.$a->filename, $a->getFileUrl())?></small> <?=Html::a('<i class="fa fa-trash"></i> ', ['/customer-note/delete-file', 'id'=>$a->id], [ 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']])?><br/>
                                            <?php } ?>
                                            <div class="actions">
                                            <?php if (isset($m->event)){ echo Html::a('<i class="fa fa-star"></i> '.$m->event->name, ['/event/view', 'id'=>$m->event_id], ['class'=>'btn btn-xs btn-primary']); } ?>
                                            <?php if (isset($m->rent)){ echo Html::a('<i class="fa fa-list"></i> '.$m->rent->name, ['/rent/view', 'id'=>$m->rent_id], ['class'=>'btn btn-xs btn-primary']); } ?>
                                            <?php if (isset($m->meeting)){ echo Html::a('<i class="fa fa-coffee"></i> '.$m->meeting->name, ['/meeting/view', 'id'=>$m->meeting_id], ['class'=>'btn btn-xs btn-primary']); } ?>
                                            <?php if (isset($m->contact)){ echo Html::a('<i class="fa fa-user"></i> '.$m->contact->displayLabel, ['/contact/view', 'id'=>$m->contact_id], ['class'=>'btn btn-xs btn-warning']); } ?>
                                            <?php echo Html::a('<i class="fa fa-pencil"></i> '.Yii::t('app', 'Edytuj'), ['/customer-note/update', 'id'=>$m->id], ['class'=>'btn btn-xs btn-success']); ?>
                                            <?php echo Html::a('<i class="fa fa-trash"></i> '.Yii::t('app', 'Usuń'), ['/customer-note/delete', 'id'=>$m->id], ['class'=>'btn btn-xs btn-danger', 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']]); ?>
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

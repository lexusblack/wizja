<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;


foreach ($m->notes as $n){
                                             ?>
                                       <div class="feed-element">
                                        <a href="#" class="pull-left">
                                            <?=$n->user->getUserPhoto("img-circle")?>
                                        </a>
                                        <div class="media-body ">

                                            <div class="actions pull-right">
                                            <?php if ((!$n->auto)&&($n->user_id==Yii::$app->user->id)) echo Html::a('<i class="fa fa-trash"></i> '.Yii::t('app', 'Usuń'), ['/note/delete', 'id'=>$n->id], ['class'=>'btn btn-xs btn-danger', 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']]); ?>
                                            </div>
                                            <strong><?=$n->user->displayLabel?>: </strong><?=$n->text?>
                                            </br>
                                            <small class="text-navy"><?=$n->datetime?></small></br>
                                            <small class="text-muted"><?=Yii::t('app', 'Załączniki: ')?><?php echo Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/note/add-file', 'id'=>$n->id]); ?>
                                            </small></br>
                                            <?php foreach ($n->noteAttachments as $a){ ?>
                                            <small class="text-muted"><?=Html::a('<i class="fa fa-file"></i> '.$a->filename, $a->getFileUrl())?></small> <?=Html::a('<i class="fa fa-trash"></i> ', ['/note/delete-file', 'id'=>$a->id], [ 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']])?><br/>
                                            <?php } ?>

                                        </div>
                                        </div>
                                        <?php
}
?>
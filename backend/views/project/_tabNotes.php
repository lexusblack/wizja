<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Customer; */


?>
<div class="panel-body">
<div class="row">
<div class="col-md-12" style="margin-bottom:20px">
<textarea class="form-control message-input" name="message" id="noteMessage" placeholder="<?= Yii::t('app', 'wpisz wiadomość') ?>"></textarea>
           <?php echo Html::a(Yii::t('app', 'Dodaj'), '#', ['class' => 'btn btn-success btn-sm', 'id'=>'sendNote']); ?>

</div>
</div>
<div class="row">
    <div class="col-md-12">
                            <div>
                                <div class="feed-activity-list">
                                 <?php       
                                    foreach ($model->notes as $m)
                                        {
                                        ?>
                                        <div class="feed-element">
                                        <a href="#" class="pull-left">
                                            <?=$m->user->getUserPhoto("img-circle")?>
                                        </a>
                                        <div class="media-body ">

                                            <div class="actions pull-right">
                                            <?php if ((!$m->auto)&&($m->user_id==Yii::$app->user->id)) echo Html::a('<i class="fa fa-trash"></i> '.Yii::t('app', 'Usuń'), ['/note/delete', 'id'=>$m->id], ['class'=>'btn btn-xs btn-danger', 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']]); ?>
                                            </div>
                                            <strong><?=$m->user->displayLabel?>: </strong><?=$m->text?></br>
                                            <small class="text-navy"><?=$m->datetime?></small></br>
                                            <small class="text-muted"><?=Yii::t('app', 'Załączniki: ')?><?php echo Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/note/add-file', 'id'=>$m->id]); ?></small></br>
                                            <?php foreach ($m->noteAttachments as $a){ ?>
                                            <small class="text-muted"><?=Html::a('<i class="fa fa-file"></i> '.$a->filename, $a->getFileUrl())?></small> <?=Html::a('<i class="fa fa-trash"></i> ', ['/note/delete-file', 'id'=>$a->id], [ 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']])?><br/>
                                            <?php } ?>

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
<script type="text/javascript">

    function sendNote() {
            data = $("#noteMessage").val();
            if (data!="")
            {
                $.ajax({
                    data: { text:data},
                    type: 'POST',
                    url: "/admin/note/send?project_id=<?=$model->id?>&type=1",
                    success:function(){ location.reload();}
                });
            }
        }
</script>
<?php $this->registerJs('
    $("#sendNote").click(function(e){
        e.preventDefault();
        sendNote();
    });
'); ?>

<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html; ?>


<div class="heading" draggable="true">
                <?= $model->name ?>

                <a class="close-link pull-right" style="color:white" onclick="closeDialog(<?=$model->id?>, 1); return false;">
                        <i class="fa fa-times"></i>
                </a>
                <?php if (Yii::$app->user->can('chatUpdate')) { ?>
                <a href="/admin/chat/update?id=<?=$model->id?>" class="pull-right" style="color:white; margin-right:5px;">
                        <i class="fa fa-pencil"></i>
                </a>
                <?php } ?>
            </div>

            <div id ="messageContent-<?=$model->id?>"class="content" style="overflow: hidden; width: auto; height: 218px;">
            <?php foreach ($messages as $m) { 
                if ($m->user_to==Yii::$app->user->identity->id)
                {
                    $class="left";
                    $class2=" active";
                }
                else
                {
                    $class="right";
                    $class2="";
                }
                ?>
                <?php if ($model->name==Yii::t('app', 'Powiadomienia NEW')){ $class2=""; }?>
                <div class="<?=$class?>">
                <?php if ($class=="left"){ ?>
                <?php if ($model->name!=Yii::t('app', 'Powiadomienia NEW')){ ?>
                    <a href="#" class="pull-left"><?php echo $m->notMe(Yii::$app->user->identity->id)->getUserPhoto("img-circle img-very-small"); ?></a>
                    <?php } ?>
                <?php } ?>

                    <div class="author-name">
                    <?php if ($model->name!=Yii::t('app', 'Powiadomienia NEW')){ ?>
                        <?=$m->userFrom->displayLabel ?>
                        <?php }else{  echo Yii::t('app', 'Powiadomienia NEW'); }?>
                     <small class="chat-date">
                        <?=$m->getTime() ?>
                    </small>
                    </div>
                    <div class="chat-message<?=$class2?>" style="word-wrap: break-word;">
                        <?=$m->getTextUrl() ?>
                    </div>

                </div>
            <?php } ?>

            <?php if ($model->isHistory())
            { ?>
            <div class="chat-message<?=$class2?>">
                        <?=Yii::t('app', 'Zostałeś odpięty od tej rozmowy.') ?>
                    </div>
            <?php } ?>
            </div>
            <?php if (($model->name!=Yii::t('app', 'Powiadomienia NEW'))&&(!$model->isHistory())){ ?>
            <div class="form-chat">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="messageInput-<?=$model->id?>" autocomplete="off">
                    <span class="input-group-btn"> <button class="btn btn-primary" type="button" onclick="sendMessage(<?=$model->id?>, 1); return false;"><?= Yii::t('app', 'Wyślij') ?>
                </button> </span></div>
            </div>
            <?php } ?>
            <script>
            $("#messageInput-<?=$model->id?>").on('keyup', function (e) {
                if (e.keyCode == 13) {
                        // Do something
                        sendMessage(<?=$model->id?>);
                    }
            });
            function intervalTrigger() {
              return window.setInterval( function() { ajaxLoad(<?=$model->id ?>);
              }, 3000 );
            };
            intervals[<?=$model->id ?>] = intervalTrigger();
                $('#messageContent-<?=$model->id?>').slimScroll({
                    alwaysVisible: true,
                    railVisible: true});
            </script>
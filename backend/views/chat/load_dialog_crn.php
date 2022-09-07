<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html; ?>


<div class="heading" draggable="true">
                <?= $model->name ?>

                <a class="close-link pull-right" style="color:white" onclick="closeDialog(<?=$model->id?>, 2); return false;">
                        <i class="fa fa-times"></i>
                </a>
            </div>

            <div id ="crnmessageContent-<?=$model->id?>"class="content" style="overflow: hidden; width: auto; height: 218px;">
            <?php foreach ($messages as $m) { 
                if ($m->company!=Yii::$app->params['companyID'])
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
                <div class="<?=$class?>">
                <?php if ($class=="left"){ ?>
                    <a href="#" class="pull-left"><?=$m->ccompany->getLogo("img-circle img-small")?></a>
                    <?php } ?>

                    <div class="author-name">
                    <?php if ($class=="left"){ ?>
                        <?=$m->ccompany->name ?>
                        <?php }else{ ?>
                        <?=$m->user ?>
                        <?php } ?>
                     <small class="chat-date">
                        <?=$m->getTime() ?>
                    </small>
                    </div>
                    <div class="chat-message<?=$class2?>">
                        <?=$m->text ?>
                    </div>

                </div>
            <?php } ?>

            </div>
            <div class="form-chat">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="crnmessageInput-<?=$model->id?>" autocomplete="off">
                    <span class="input-group-btn"> <button class="btn btn-primary" type="button" onclick="sendMessage(<?=$model->id?>, 2); return false;"><?= Yii::t('app', 'Wyślij') ?>
                </button> </span></div>
            </div>
            <script>
            $("#crnmessageInput-<?=$model->id?>").on('keyup', function (e) {
                if (e.keyCode == 13) {
                        // Do something
                        sendMessage(<?=$model->id?>, 2);
                    }
            });
            function intervalTrigger() {
              return window.setInterval( function() { ajaxLoad(<?=$model->id ?>, 2);
              }, 3000 );
            };
            intervals2[<?=$model->id ?>] = intervalTrigger();
                $('#crnmessageContent-<?=$model->id?>').slimScroll({
                    alwaysVisible: true,
                    railVisible: true});
            </script>
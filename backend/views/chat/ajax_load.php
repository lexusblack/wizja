<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html; ?>
            <?php foreach ($notread as $m) { 
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
                <?php if ($model->name!=Yii::t('app', 'Powiadomienia NEW')){ ?>
                <a href="#" class="pull-left"><?php echo $m->notMe(Yii::$app->user->identity->id)->getUserPhoto("img-circle img-very-small"); ?></a>
                <?php } ?>
                    <div class="author-name">
                        <?php if ($model->name!=Yii::t('app', 'Powiadomienia NEW')){ ?>
                        <?=$m->userFrom->displayLabel ?>
                        <?php }else{  echo Yii::t('app', 'Powiadomienia NEW'); }?>

                         <small class="chat-date">
                        <?=$m->getTime() ?>
                    </small>
                    </div>
                    <div class="chat-message<?=$class2?>">
                        <?=$m->text ?>
                    </div>

                </div>
            <?php } ?>
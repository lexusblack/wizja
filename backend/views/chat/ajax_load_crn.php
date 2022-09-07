<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html; ?>
            <?php foreach ($notread as $m) { 
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
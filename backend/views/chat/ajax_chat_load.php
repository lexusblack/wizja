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

                                    <div class="chat-message <?=$class?>">
                                    <?php echo $m->userFrom->getUserPhoto("message-avatar"); ?>
                                        <div class="message">
                                            <a class="message-author" href="#"><?=$m->userFrom->displayLabel ?></a>
                                            <span class="message-date"> <?=$m->getFullTime() ?> </span>
                                            <span class="message-content"  style="word-wrap: break-word;">
                                            <?=$m->getTextUrl() ?>
                                            </span>
                                        </div>
                                    </div>
            <?php } ?>
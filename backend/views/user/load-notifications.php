<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html; ?>

                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell"></i> 
                        <?php if (count($models)>0): ?>
                         <span class="label label-primary" id="notification-label"><?=count($models)?></span>
                     <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                    <?php foreach ($models as $model){ ?>
                        <li class="divider"></li>
                        <li>
                            <div>
                            <span class="pull-right text-muted small"><?=substr($model->create_time,0,11)?></span>
                            <?=$model->getParsedContent()?>
                            </div>
                        </li>
                    <?php } ?>
                    </ul>
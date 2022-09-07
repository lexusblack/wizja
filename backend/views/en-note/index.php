<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

$this->title = Yii::t('app', 'Aktualności');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="en-note-index">

<div class="row">
    <div class="col-lg-8">
<div class="ibox float-e-margins">
                        <div class="ibox-title lazur-bg">
                            <h5><?= Yii::t('app', 'Aktualności') ?></h5>
                            <div class="ibox-tools white">
                            </div>
                        </div>
        <div class="ibox-content small-font" style="padding-left:10px; padding-left:10px;">
        <div class="feed-activity-list">
                                <?php foreach ($notes as $m){ ?>
                                       <div class="feed-element">
                                        <a href="#" class="pull-left">
                                            <?=$m->company->getLogo("img-circle")?>
                                        </a>
                                        <div class="media-body ">
                                            <strong><?=$m->company->name?>: </strong><?=$m->text?>
                                            </br>
                                            <?=Html::a(Yii::t('app', 'Zobacz'), $m->link)?>
                                            </br>
                                            <small class="text-navy"><?=$m->datetime?></small></br>
    

                                        </div>
                                        </div>
                                    
                                <?php } ?>
        </div>
        </div>
    </div>
    </div>

</div>

</div>

<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
$user = Yii::$app->user;
use kartik\tabs\TabsX;

?>
<div class="panel-body">
<h3><?=$hall->hallGroup->name ?> <?=Html::a(Yii::t('app', 'Edytuj'), ['\hall-group\book-edit', 'id'=>$hall->id], ['class'=>'btn btn-success btn-xs'])?> <?=Html::a(Yii::t('app', 'Usuń'), ['\hall-group\remove', 'id'=>$hall->id], ['class'=>'btn btn-danger btn-xs'])?></h3>
<div class="row">
    <div class="col-md-12">
    <p><b><?=Yii::t('app', 'Data rezerwacji')?></b>: <?= $hall->start_time." - ".$hall->end_time?></p>
<p><b><?=Yii::t('app', 'Status rezerwacji')?></b>: <?= $hall->statut->name?></p>
<p><b><?=Yii::t('app', 'Szszegoły')?></b>: <?= $hall->description?></p>
</div>
    </div>
</div>
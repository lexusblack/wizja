<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\OfferStatutSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\export\ExportMenu;
$this->title = Yii::t('app', 'Harmonogramy');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="offer-statut-index">
<div class="row">
<div class="col-xs-12">
<div class="ibox">
        <div class="ibox-title newsystem-bg"><h4><?=$this->title?></h4></div>
        <div class="ibox-content">
        <?=Html::a(Yii::t('app', 'Dodaj'), ['create-type'], ['class'=>'btn btn-success'])?>
        <table class="table">
        <?php foreach ($eventTypes as $model){ if ($model->default){ $d = Yii::t('app', ' (domyślny)'); }else{ $d="";}?>
        <tr><td><?= Html::a($model->name.$d, ['index', 'id' => $model->id])?></td><td>  <?= Html::a(Yii::t('app', 'Usuń'), ['delete-type', 'id' => $model->id], [
                                    'class' => 'btn btn-xs btn-danger pull-right',
                                    
                                ])?> <?= Html::a(Yii::t('app', 'Edytuj nazwę'), ['update-type', 'id' => $model->id], [
                                    'class' => 'btn btn-xs btn-primary pull-right',
                                    
                                ])?> <?= Html::a(Yii::t('app', 'Edytuj'), ['index', 'id' => $model->id], [
                                    'class' => 'btn btn-xs btn-success pull-right',
                                    
                                ])?></td></tr>
                                
        <?php } ?>
        </table>
        </div>
</div>
</div>


</div>
</div>

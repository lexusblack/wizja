<?php

/* @var $this yii\web\View */ 
/* @var $dataProvider yii\data\ActiveDataProvider */ 

use yii\helpers\Html; 
use yii\helpers\Url; 
use kartik\export\ExportMenu; 
use common\components\grid\GridView;

$this->title = Yii::t('app', 'Magazyny'); 
$this->params['breadcrumbs'][] = $this->title; 

?> 
<div class="warehouse-index"> 

<?php 
$company = \common\models\Company::findOne(['code'=>Yii::$app->params['companyID']]);
if (!$company->warehouses) { echo "Brak uprawnień. Skontaktuj się z działem sprzedaży.";}else{ ?>

        <div>
        <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['create'], ['class'=>'btn btn-primary'])." "; ?>            
        </div>
<div class="row">
<div class="col-xs-12">
<div class="ibox">
        <div class="ibox-title newsystem-bg"><h4><?=$this->title?></h4></div>
        <div class="ibox-content">
        <ul class="todo-list ui-sortable" id="list">
        <?php foreach ($models as $model){ ?>
        <li class="checklist-item" draggable="true" id="bigitem-<?=$model->id?>" style="border-left-color:<?=$model->color?>">
        <div class="row">
        <div class="col-xs-9"><?=$model->name?></div>
        <div class="col-xs-3" style="text-align:right">
                                <?= Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $model->id], [
                                    'class' => 'btn btn-sm',
                                    
                                ])
                                ?>
                                <?= Html::a('<i class="fa fa-trash"></i>', ['delete', 'id' => $model->id], [
                                    'class' => 'btn btn-danger btn-sm',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                                        'method' => 'post',
                                    ],
                                ])
                                ?>
        </div>
        </div>
        </li>
        <?php } ?>
        </ul>
        </div>
</div>
</div>


</div>
    <?php

$this->registerJs("
$( function() {
    $( '#list').sortable({
    update: function (event, ui) {
        var data = $(this).sortable('serialize');
        $.ajax({
            data: data,
            type: 'POST',
            url: '".Url::to(['/warehouse/order'])."'
        });
    }
});
    $( '#list').disableSelection();
  } );
  ");
?>
<?php } ?>
</div> 

<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
use kartik\editable\Editable;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Rent */
date_default_timezone_set(Yii::$app->params['timeZone']);
$this->title = $model->name;
$user = Yii::$app->user;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wypożyczenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rent-view">

    <p>
    <?= Html::a('<i class="fa fa-list"></i> ' . Yii::t('app', 'Packlista'), ['packing-list', 'id' => $model->id], ['class' => 'btn btn-success', 'target'=>'_blank']);?>
        <?php
         if ((!$model->getBlocks('event'))||(Yii::$app->user->can('eventEventBlockEvent'))) { 
        if ($user->can('eventRentsEdit')) {
           echo Html::a('<i class="fa fa-pencil"></i> ' .  Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
        }
        if ($user->can('eventRentsDelete')) {
           echo Html::a('<i class="fa fa-trash"></i> ' .  Yii::t('app', 'Usuń'), ['delete',
                'id' => $model->id], ['class' => 'btn btn-danger',
                'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post',],]);
        } } ?>

 <?php if ($user->can('eventRentsEdit')) { 
            $statuts = \common\models\EventStatut::find()->where(['type'=>2, 'active'=>1, 'button'=>1])->andWhere(['>', 'position', $model->eventStatut->position])->all();
            foreach ($statuts as $s)
            {
                $title = $s->name;
                if ($s->icon)
                {
                    $title = '<i class="fa '.$s->icon.'"></i> '.$title;
                }
                echo " ".Html::a($title, ['#'], ['class' => 'btn status-button', 'data-id'=>$s->id, 'style'=>'color:white; background-color:'.$s->color]);
            }
     } ?>
    </p>



</div>

<div class="row">
    <div class="col-md-4">
        <div class="ibox">
    <div class="ibox-content no-padding" style="min-height:170px">
        <?= DetailView::widget([
            'model' => $model,
            'options' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap\'',
            ],
            'attributes' => [
                [
                'attribute' =>'name',
                'format'=>'html',
                'value' =>function($model)
                {
                    return $model->name.'<span class="label label-warning-light pull-right">'.$model->code.'</span>';
                }
                ],
                [
                'attribute' => 'start_time',
                'value' => function($model){
                    return $model->start_time;
                    }
                ],
                [
                'attribute' => 'end_time',
                'value' => function($model){
                    return $model->end_time;
                    }
                ],
                'days',
            ],
        ]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="ibox">
    <div class="ibox-content no-padding" style="min-height:170px">
        <?= DetailView::widget([
            'model' => $model,
            'options' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap\'',
            ],
            'attributes' => [
                 [
                'attribute' => 'customer_id',
                'format'=>'html',
                'value' => function($model){
                    if ($model->customer_id)
                    {
                        return Html::a($model->customer->displayLabel, ['/customer/view', 'id'=>$model->customer_id]);
                    }else{
                        return "-";
                    }
                    }
                ],
                [
                'attribute' => 'contact_id',
                'format'=>'html',
                'value' => function($model){
                    if ($model->contact_id)
                    {
                        return $model->contact->displayLabel."<br/> ".$model->contact->email."<br/> ".$model->contact->phone;
                    }else{
                        return "-";
                    }
                    }
                ],
                'manager.displayLabel:text:'.Yii::t('app', 'Project Manager'),
                 [
                     'attribute' => 'status',
                    'format' =>'raw',
                     'value' => function($model) use ($user){
                        if ($user->can('eventRentsEdit')) {
                            return Html::dropDownList('status', $model->status, \common\models\Rent::getStatusList($model->status), ['id'=>'rentStatus']);
                        }else{
                            return $model->getStatusButton();
                        }
                        
                    },
                 ],
            ],
        ]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="ibox">
    <div class="ibox-content no-padding" style="min-height:170px">
        <?= DetailView::widget([
            'model' => $model,
            'options' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap\'',
            ],
            'attributes' => [

                'description:html',
                'info:html',
            ],
        ]) ?>

        </div>
    </div>
        <div class="col-md-6">
        <div class="widget style1 lazur-bg">

                        <div class="row vertical-align">
                            <div class="col-md-3">
                                <i class="fa fa-plug fa-2x"></i>
                            </div>
                            <div class="col-md-9 text-right">
                                <h2 class="font-bold pull-right" style="font-size:20px;"><?=floor($model->getGearWeight())." kg"?></h2>
                            </div>
                        </div>
                    </div>
            </div>
        <div class="col-md-6">
        <div class="widget style1 yellow-bg">

                        <div class="row vertical-align">
                            <div class="col-md-3">
                                <i class="fa fa-archive fa-2x"></i>
                            </div>
                            <div class="col-md-9 text-right">
                                <h2 class="font-bold pull-right" style="font-size:20px;"><?=round($model->getGearVolume(), 1)."m"?><sp>3</sp></h2>
                            </div>
                        </div>
                    </div>
            </div>
    </div>
    <div class="row">
    <div class="col-md-12">
        <div class="tabs-container">
            <?php
            $tabItems = [
                [
                    'label'=>'<i class="fa fa-cogs"></i> '. Yii::t('app', 'Sprzęt'),
                    'content'=>$this->render('_tabGear', ['model'=>$model]),
                    'active'=>true,
                    'options'=> [
                        'id'=>'tab-gear',
                    ]
                ],
                [
                    'label'=>'<i class="fa fa-cogs"></i> '. Yii::t('app', 'Sprzęt zewnętrzny'),
                    'content'=>$this->render('_tabOuterGear', ['model'=>$model]),
                    'options'=> [
                        'id'=>'tab-outer-gear',
                    ]
                ],
                [
                    'label'=>Yii::t('app', 'Opis'),
                    'content'=>$this->render('_tabDescription', ['model'=>$model]),
                    'options'=> [
                        'id'=>'tab-description',
                    ]
                ],
                [
                    'label'=>'<i class="fa fa-file"></i> '. Yii::t('app', 'Załączniki'),
                    'content'=>$this->render('_tabAttachment', ['model'=>$model]),
                    'options'=> [
                        'id'=>'tab-attachment',
                    ]
                ],
                [
                    'label'=>Yii::t('app', 'Zadania'),
                    'content'=>$this->render('_tabTask', ['model'=>$model]),
                    'active'=>false,
                    'options'=> [
                        'id'=>'tab-task',
                    ]
                ],
                [
                    'label'=>'<i class="fa fa-shopping-cart"></i> '. Yii::t('app', 'Oferty'),
                    'content'=>$this->render('_tabOffers', ['model'=>$model]),
                    'active'=>false,
                    'options'=> [
                        'id'=>'tab-offer',
                    ]
                ],
                [
                    'label'=>'<i class="fa fa-money"></i> '.Yii::t('app', 'Finanse'),
                    'content'=>$this->render('_tabFinances', ['model'=>$model]),
                    'active'=>false,
                    'options'=> [
                        'id'=>'tab-money',
                    ]
                ],
                [
                    'label'=>'<i class="fa fa-history"></i> '.Yii::t('app', 'Historia'),
                    'content'=>$this->render('_tabLog', ['model'=>$model]),
                    'active'=>false,
                    'options'=> [
                        'id'=>'tab-history',
                    ]
                ],
            ];


            echo TabsX::widget([
                'items'=>$tabItems,
                'encodeLabels'=>false,
                'enableStickyTabs'=>true,
            ]);
            ?>
        </div>
    </div>
</div>
<script type="text/javascript">

        var eventStatuts= [];
    <?php
    $statuts = \common\models\EventStatut::find()->asArray()->all();
    foreach ($statuts as $s)
    {
        if (($s['delete_gear'])||($s['delete_crew']))
        {
            echo "eventStatuts[".$s['id']."]=1;";
        }else{
            echo "eventStatuts[".$s['id']."]=0;";
        }
    }
    ?>
    function changeRentStatus(status)
    {
        if (eventStatuts[status]==1)
        {
            swal({
            title: "<?=Yii::t('app', 'Uwaga!')?>",
            icon:"warning",
            text: "<?=Yii::t('app', ' Zmiana na ten status może usunąć rezerwacje sprzętu z tego Wypożyczenia. Czy chcesz kontynuować?')?>",
          buttons: {
            cancel: "<?=Yii::t('app', 'Nie')?>",
            yes: {
              text: "<?=Yii::t('app', 'Tak')?>",
              value: "yes",
            },
          },
        })
        .then((value) => {
          switch (value) {
         
            case "yes":
        $.ajax({
            url: '<?=Url::to(['rent/change-status'])?>?rent_id=<?=$model->id?>'+'&status='+status,
                    success: function(response){
                        toastr.success('<?=Yii::t('app', 'Status zmieniony')?>');
                        location.reload();
                                  }
            });
              break;       
          }
        });      
        }else{
        $.ajax({
            url: '<?=Url::to(['rent/change-status'])?>?rent_id=<?=$model->id?>'+'&status='+status,
                    success: function(response){
                        toastr.success('<?=Yii::t('app', 'Status zmieniony')?>');
                        location.reload();
                                  }
            });
        }
        
    }
</script>
<?php
$this->registerJs('

$("#rentStatus").on("change", function(){
    changeRentStatus($(this).val());
});

$(".status-button").click(function(e){
    e.preventDefault();
    changeRentStatus($(this).data("id"));
});
');
<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use kartik\tabs\TabsX;

/* @var $this yii\web\View */
/* @var $model common\models\Project */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Projekty'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
            <div class="col-lg-9">
                <div class="wrapper wrapper-content animated fadeInUp">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="m-b-md">
                                    <?php
                                    
                                    echo Html::a('<i class="fa fa-trash"></i> '.Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], ['class'=>"btn btn-danger btn-xs pull-right", 'data' => [
                                        'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                                        'method' => 'post',
                                    ],]);
                                    echo Html::a('<i class="fa fa-pencil"></i> '.Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class'=>"btn btn-white btn-xs pull-right"]);
                                    ?>
                                        <h2><?=$model->name?></h2>
                                    </div>
                                </div>
                            </div>
                        <div class="row">
                                <div class="col-lg-5">
                                    <dl class="dl-horizontal">
                                        <dt><?=Yii::t('app', 'Status:')?></dt> <dd><?=$model->statusLabel()?></dd>
                                    </dl>
                                </div>
                                <div class="col-lg-7">
                                    <dl class="dl-horizontal">
                                        <dt><?=Yii::t('app', 'PM:')?></dt>
                                        <dd class="project-people">
                                        <?php
                                        foreach ($model->projectUsers as $pu)
                                            {
                                                if ($pu->manager)
                                                echo ' <img alt="image" class="img-circle" src="'.$pu->user->getUserPhotoUrl().'" title="'.$pu->user->first_name." ".$pu->user->last_name.'">';
                                            }
                                            ?>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">
                                    <dl class="dl-horizontal">
                                        <dt><?=Yii::t('app', 'Dodał:')?></dt> <dd><?=$model->creator->displayLabel?></dd>
                                        <dt><?=Yii::t('app', 'Aktualności:')?></dt> <dd><?=count($model->notes)?></dd>
                                        <dt><?=Yii::t('app', 'Klient:')?></dt> <dd><?= Html::a($model->customer->name, ['/customer/view', 'id'=>$model->customer_id], ['class'=>'text-navy'])?></dd>
                                        <dt><?=Yii::t('app', 'Kontakt:')?></dt> <dd>
                                        <?php if (isset($model->contact)){ 
                                echo $model->contact->first_name." ".$model->contact->last_name;
                                if ($model->contact->phone!="") { ?>
                                 <span class="label label-primary"><i class="fa fa-phone"></i> <?php echo $model->contact->phone; ?></span>                           
                                <?php } } ?>
                                        </dd>
                                    </dl>
                                </div>
                                <div class="col-lg-7" id="cluster_info">
                                    <dl class="dl-horizontal">

                                        <dt><?=Yii::t('app', 'Start projektu:')?></dt> <dd><?=substr($model->start_time, 0,10)?></dd>
                                        <dt><?=Yii::t('app', 'Koniec projektu:')?></dt> <dd><?=substr($model->end_time, 0,10)?></dd>
                                        <dt><?=Yii::t('app', 'Uczestnicy:')?></dt>
                                        <dd class="project-people">
                                        <?php
                                        foreach ($model->projectUsers as $pu)
                                            {
                                                echo ' <img alt="image" class="img-circle" src="'.$pu->user->getUserPhotoUrl().'" title="'.$pu->user->first_name." ".$pu->user->last_name.'">';
                                            }
                                            ?>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <dl class="dl-horizontal">
                                        <dt><?=Yii::t('app', 'Ukończono:')?></dt>
                                        <dd>
                                            <div class="progress progress-striped active m-b-sm">
                                                <div style="width: <?=$model->getCompletion()['status']?>%;" class="progress-bar"></div>
                                            </div>
                                            <small><?=Yii::t('app', 'Projekt ukończony w')?> <strong><?=$model->getCompletion()['status']?>%</strong>. <?=Yii::t('app', 'Pozostałych do wykonania zadań: ')?><?=$model->getCompletion()['task']-$model->getCompletion()['done'] ?></small>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                            <div class="row m-t-sm">
                                <div class="col-lg-12">
                                    <div class="tabs-container">
                                        <?php
                                        $tabItems = [
                                            [
                                                'label'=>Yii::t('app', 'Zadania'),
                                                'content'=>$this->render('_tabTask', ['model'=>$model]),
                                                'active'=>true,
                                                'options'=> [
                                                    'id'=>'tab-task',
                                                ]
                                            ],
                                            
                                            [
                                                'label'=>Yii::t('app', 'Aktualności'),
                                                'content'=>$this->render('_tabNotes', ['model'=>$model]),
                                                'active'=>false,
                                                'options'=> [
                                                    'id'=>'tab-note',
                                                ]
                                            ],
                                            [
                                                'label'=>Yii::t('app', 'Uczestnicy'),
                                                'content'=>$this->render('_tabUser', ['model'=>$model, 'projectUser'=>$projectUser]),
                                                'active'=>false,
                                                'options'=> [
                                                    'id'=>'tab-user',
                                                ]
                                            ],
                                            [
                                                'label'=>Yii::t('app', 'Wydarzenia'),
                                                'content'=>$this->render('_tabEvent', ['model'=>$model]),
                                                'active'=>false,
                                                'options'=> [
                                                    'id'=>'tab-event',
                                                ]
                                            ],

                                            [
                                                'label'=>Yii::t('app', 'Oferty'),
                                                'content'=>$this->render('_tabOffers', ['model'=>$model]),
                                                'active'=>false,
                                                'options'=> [
                                                    'id'=>'tab-offer',
                                                ]
                                            ]
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
                            </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="wrapper wrapper-content project-manager">
                    <h4><?=Yii::t('app', 'Opis projekt')?></h4>
                    <p class="small">
                        <?=$model->description?>
                    </p>

                    <h5><?=Yii::t('app', 'Działy')?>:</h5>
                    <?php foreach ($model->departments as $d){ ?>
                        <p class="small font-bold">
                        <span><i class="fa fa-circle" style="color:<?=$d->color?>"></i> <?=$d->name?></span>
                    </p>

                    <?php } ?>
                    <h5><?=Yii::t('app', 'Pliki:')?></h5>
                    <p>
                    <ul class="list-unstyled project-files">
                    <?php foreach($model->getFiles() as $a){ ?>
                    <li><?=Html::a('<i class="fa fa-file"></i> '.substr($a->filename,0,30), $a->getFileUrl())?> <?=Html::a('<i class="fa fa-trash"></i> ', ['/note/delete-file', 'id'=>$a->id], [ 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']])?></li>
                        <?php } ?>
                    </ul>
                    </p>
                    <div class="text-center m-t-md">
                        <?=Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj plik'), ['add-file', 'id'=>$model->id], ['class'=>'btn btn-sm btn-primary'])?>

                    </div>
                </div>
            </div>
        </div>
<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->first_name." ".$model->last_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Użytkownicy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="user-view">

    <p>
    <?php if ($user->can('usersUsersEdit')) { ?>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?php } ?>
    <?php if ($user->can('usersUsersDelete')) { ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
    <?php } ?>
    </p>
        <div class="row">
            <div class="col-md-4">
                <div class="contact-box center-version">
                    <a href="#">
                        <?=$model->getUserPhoto("img-circle")?>

                        <h3 class="m-b-xs"><strong><?=$this->title?></strong></h3>

                        <div class="font-bold"><?=$model->getRoleName()?></div>
                        <address class="m-t-md">
                            <strong><?=$model->email?></strong><br>
                            <?php if ($user->can('usersUsersEdit')) { ?>
                            <abbr title="<?=Yii::t('app', 'PESEL')?>">P</abbr>: <?=$model->pesel?> 
                            <abbr title="<?=Yii::t('app', 'Nr Dowodu')?>">ID</abbr>: <?=$model->id_card?><br>
                            <?php } ?>
                            <abbr title="<?=Yii::t('app', 'Telefon')?>">tel: </abbr><?=$model->phone?>
                        </address>
                         <div><strong><?=Yii::t('app', "Działy")?>:</strong> <?=$model->getDepartmentList()?></div>
                         <div><strong><?=Yii::t('app', "Umiejętności")?>:</strong> <?=$model->getSkillList()?></div>
                         <div class="font-bold"><?=Yii::t('app', "Ostatnie logowanie")?>: <?=$model->last_visit?></div>


                    </a>
                    <div class="contact-box-footer">
                        <div class="m-t-xs btn-group">
                            <?php if (Yii::$app->user->can('chatCreate')) { ?><a class="btn btn-xs btn-white" onclick="createUserChat(<?=$model->id?>); return false;"><i class="fa fa-comment"></i> <?=Yii::t('app', 'Wiadomość')?> </a><?php } ?>
                            <a class="btn btn-xs btn-white" href="mailto:<?=$model->email?>"><i class="fa fa-envelope"></i> Email</a>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-8">
            <div class="tabs-container">
        <?php
        $tabItems = [];
        $tabItems[] = [
                'label'=>'<i class="fa fa-star"></i> '.Yii::t('app', 'Wydarzenia'),
                'content'=>$this->render('_tabEvents', ['model'=>$model,'dataProvider'=>$dataProvider,'searchModel'=>$searchModel]),
                'active'=>true,
        ];
        
        $tabItems[] = [
                'label'=>'<i class="fa fa-coffee"></i> '.Yii::t('app', 'Spotkania'),
                'content'=>$this->render('_tabMeetings', ['model'=>$model]),
                'active'=>false,
                'options'=> [
                        'id'=>'tab-meeting',
                    ]
        ];
        $tabItems[] = [
                'label'=>'<i class="fa fa-glass"></i> '.Yii::t('app', 'Urlopy'),
                'content'=>$this->render('_tabVacations', ['model'=>$model]),
                'active'=>false,
                'options'=> [
                        'id'=>'tab-lvacation',
                    ]
        ];
        if ($user->can('usersUsersEdit')) {

        $tabItems[] = [
                'label'=>'<i class="fa fa-money"></i> '.Yii::t('app', 'Finanse'),
                'content'=>$this->render('_tabFinances', ['model'=>$model, 'sections'=>$sections]),
                'active'=>false,
                'options'=> [
                        'id'=>'tab-finance',
                    ]
        ];   
        $tabItems[] = ['label' => Yii::t('app', 'Notatki'),
                        'content' => $this->render('_tabNotes', ['model' => $model]),
                        'active'=>false,
                        'options' => ['id' => 'tab-notes',]

                    ];
        $tabItems[] = ['label' => Yii::t('app', 'Zadania'),
                        'content' => $this->render('_tabTask', ['model' => $model]),
                        'active'=>false,
                        'options' => ['id' => 'tab-tasks',]

                    ]; 
                }
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

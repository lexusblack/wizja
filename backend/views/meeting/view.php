<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Meeting */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Spotkania'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="meeting-view">
<div class="row">
    <div class="col-md-4">
    <div class="row">
    <div class="col-md-12">
                        <div class="widget style1 navy-bg">
                        <div class="row vertical-align">
                            <div class="col-xs-12">
                                <h2 class="font-bold"><?php echo $model->name; ?></h2>
                                <?php if ($model->customer) { ?>
                                    <h4 class="font-bold"> <?= Yii::t('app', 'Klient') ?>: <?php echo Html::a($model->customer->name, ['/customer/view', 'id'=>$model->customer_id], ['style'=>'color:white']); ?></h4>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
    </div>
    </div>
            <p>
            <?= Html::a('<i class="fa fa-download"></i> ' . Yii::t('app', 'Pobierz .ics'), ['ics', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fa fa-envelope"></i> ' . Yii::t('app', 'Wyślij zaproszenie'), ['send-mail', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i>', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="row">
            <div class="col-md-12">
                        <div class="ibox">
                        <div class="ibox-title">
                            <h5><?php echo Yii::t('app', 'Szczegóły'); ?></h5>
                            <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <h4><?php echo Yii::t('app', 'Termin'); ?></h4>
                            <p><?=$model->start_time?></p>
                            <h4><?php echo Yii::t('app', 'Miejsce'); ?></h4>
                            <p><?=$model->location?></p>
                            <?php if ($model->customer) { ?>
                            <h4><?= Yii::t('app', 'Klient') ?></h4> <p><?php echo Html::a($model->customer->name, ['/customer/view', 'id'=>$model->customer_id]); ?></p>
                            <?php } ?>
                            <h4><?php echo Yii::t('app', 'Opis'); ?></h4>
                            <p><?=$model->description?></p>
                            
                        </div>
                    </div>
            </div>    
    </div>
    <?php  if (isset($model->contact)){ ?>
    <div class="row">    
        <div class="col-md-12">
                            <div class="ibox float-e-margins">
                            <div class="ibox-content text-center">
                                <h2><?php echo $model->contact->first_name." ".$model->contact->last_name;?> </h2>
                                        <p class="font-bold"><?php echo Yii::t('app', 'Osoba kontaktowa'); ?></p>

                                <div class="text-center">
                                    <ul class="list-unstyled m-t-md">
                                                        <li>
                                                            <span class="fa fa-envelope m-r-xs"></span>
                                                            <?php echo $model->contact->email; ?>
                                                        </li>
                                                        <li>
                                                            <span class="fa fa-phone m-r-xs"></span>
                                                            <?php echo $model->contact->phone; ?>
                                                        </li>
                                                    </ul>
                                </div>
                            </div>
                            </div>
        </div>
        </div>
        <?php } ?>
    </div>
    <div class="col-md-8">
            <div class="row">
            <?php 
                $teams = $model->getAssignedUsers();
                if ($teams->getTotalCount()>0){
                    $i=1;
                    foreach ($teams->getModels() as $team){
                    if ($i==4)
                    {
                        $i=1;
                        echo "</div><div class='row'>";
                    } 
                    $i++;
            ?>
                <div class="col-md-4">
                            <div class="ibox float-e-margins">
                            <div class="ibox-content text-center">
                                <h2><?php echo $team->first_name." ".$team->last_name;?> </h2>
                                <div class="m-b-sm">
                                        <img alt="image" class="img-circle img-medium" src="<?php echo $team->getPhotoUrl();?>">
                                </div>

                                <div class="text-center">
                                    <ul class="list-unstyled m-t-md">
                                                        <li>
                                                            <span class="fa fa-envelope m-r-xs"></span>
                                                            <?php echo $team->email; ?>
                                                        </li>
                                                        <li>
                                                            <span class="fa fa-phone m-r-xs"></span>
                                                            <?php echo $team->phone; ?>
                                                        </li>
                                                    </ul>
                                </div>
                            </div>
                            </div>
        </div>
            <?php } }?>
            </div>
    </div>
</div>
</div>



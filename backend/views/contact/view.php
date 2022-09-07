<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
/* @var $this yii\web\View */
/* @var $model common\models\Contact */

$this->title = $model->last_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Kontakty'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->customer->name, 'url' => ['/customer/view', 'id'=>$model->customer_id]];
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="contact-view">
    <p>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
<div class="row">
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12">
                            <div class="ibox float-e-margins">
                            <div class="ibox-content text-center">
                                <h2><?= $model->first_name." ".$model->last_name;?> </h2>
                                <?php if ($model->photo){ ?>
                                <div class="m-b-sm">
                                        <img alt="image" class="img-circle img-medium" src="<?php echo $model->getPhotoUrl();?>">
                                </div>
                                <?php } ?>
                                        <p class="font-bold"><?=$model->position;?></p>

                                <div class="text-center">
                                    <ul class="list-unstyled m-t-md">
                                                        <li>
                                                            <span class="fa fa-envelope m-r-xs"></span>
                                                            <?= $model->email; ?>
                                                        </li>
                                                        <li>
                                                            <span class="fa fa-phone m-r-xs"></span>
                                                            <?= $model->phone; ?>
                                                        </li>
                                                        <li>
                                                            <span class="fa fa-info m-r-xs"></span>
                                                            <?= $model->info; ?>
                                                        </li>
                                                    </ul>
                                </div>
                            </div>
                            </div>
            </div>
        </div>
            <div class="row">
            <div class="col-md-12">
            <div class="ibox float-e-margins">
                                <div>
                                    <?php if ($model->customer->logo){ ?>
                                    <div class="ibox-content border-left-right">
                                        <img alt="image" class="img-responsive" src="<?= $model->customer->getLogoUrl(); ?>">
                                    </div>
                                    <?php } ?>
                                    <div class="ibox-content profile-content">
                                        <h4><strong><?php echo Html::a($model->customer->name, ['/customer/view', 'id'=>$model->customer->id]); ?></strong></h4>
                                        <p><i class="fa fa-map-marker"></i> <?= Yii::t('app', 'adres') ?>: <?= $model->customer->address.", ".$model->customer->zip." ".$model->customer->city ?></p>
                                        <p><i class="fa fa-phone"></i> <?= Yii::t('app', 'tel') ?>. <?= $model->customer->phone; ?></p>
                                        <p><i class="fa fa-envelope"></i> <?= Yii::t('app', 'e-mail') ?>: <?= $model->customer->email ?></p>
                                        <p><i class="fa fa-money"></i> <?= Yii::t('app', 'NIP') ?> <?= $model->customer->nip; ?></p>
                                        <p><i class="fa fa-bank"></i> <?= Yii::t('app', 'Nr konta') ?>: <?= $model->customer->bank_account; ?></p>
                                        <h5>
                                            <?= Yii::t('app', 'Informacje') ?>
                                        </h5>
                                        <p>
                                            <?= $model->customer->info; ?>
                                        </p>
                            </div>
                        </div>
                </div>
            </div>
            </div>
    </div>
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-12">
                    <div class="tabs-container">
                <?php
                $tabItems = [];
                if ($user->can('clientContactsSeeMeetings')) {
                    $tabItems[] = ['label' => Yii::t('app', 'Spotkania'),
                        'content' => $this->render('_tabMeetings', ['model' => $model]), 'active' => true,
                        'options' => ['id' => 'tab-meetings',]];
                }
                if ($user->can('clientContactsSeeProjects')) {
                    $tabItems[] = ['label' => Yii::t('app', 'Projekty'),
                        'content' => $this->render('_tabProjects', ['model' => $model]),
                        'options' => ['id' => 'tab-projects',]];
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
</div>
</div>

   
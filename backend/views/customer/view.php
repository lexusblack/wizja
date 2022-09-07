<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
/* @var $this yii\web\View */
/* @var $model common\models\Customer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Klienci'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="customer-view">

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
        <div class="col-md-8">
            <div class="ibox float-e-margins">
                                <div>
                                    <div class="ibox-content profile-content">
                                        <h4><strong><?php echo $model->name ?></strong></h4>
                                        <p><i class="fa fa-map-marker"></i> <?= Yii::t('app', 'adres') ?>: <?php echo $model->address.", ".$model->zip." ".$model->city ?></p>
                                        <p><i class="fa fa-phone"></i> <?= Yii::t('app', 'tel') ?>. <?php echo $model->phone; ?></p>
                                        <p><i class="fa fa-envelope"></i> <?= Yii::t('app', 'e-mail') ?>: <?php echo $model->email ?></p>
                                        <p><i class="fa fa-money"></i> <?= Yii::t('app', 'NIP') ?>: <?php echo $model->nip; ?></p>
                                        <p><i class="fa fa-bank"></i> <?= Yii::t('app', 'Nr konta') ?>: <?php echo $model->bank_account; ?></p>
                                        <h5>
                                            <?= Yii::t('app', 'Informacje') ?>
                                        </h5>
                                        <p>
                                            <?php echo $model->info; ?>
                                        </p>
                            </div>
                        </div>
                </div>
            </div>
        <div class="col-md-4">
            <div class="ibox float-e-margins">
                                <div>
                                    <?php if ($model->logo){ ?>
                                    <div class="ibox-content no-padding border-left-right">
                                        <img alt="image" class="img-responsive" src="<?php echo $model->getLogoUrl(); ?>">
                                    </div>
                                    <?php } ?>
                            </div>
                        </div>
                </div>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="tabs-container">
                <?php
                $tabItems = [];
                if ($user->can('clientClientsSeeContacts')) {
                    $tabItems[] = ['label' => Yii::t('app', 'Kontakty'),
                        'content' => $this->render('_tabContact', ['model' => $model]), 'active' => true,
                        'options' => ['id' => 'tab-contacts',]];
                    $tabItems[] = ['label' => Yii::t('app', 'Spotkania'),
                        'content' => $this->render('_tabMeetings', ['model' => $model]), 'active' => false,
                        'options' => ['id' => 'tab-meetings',]];
                }
                    $tabItems[] = ['label' => Yii::t('app', 'Rabaty'),
                        'content' => $this->render('_tabDiscount', ['model' => $model]),
                        'options' => ['id' => 'tab-discount',]

                    ];
                
                if ($user->can('clientClientsSeeProjects')) {
                    if (Yii::$app->session->get('company')==1)
                    {
                        $tabItems[] = ['label' => Yii::t('app', 'Oferty'),
                            'content' => $this->render('_tabOffers', ['model' => $model]),
                            'options' => ['id' => 'tab-offers',]

                        ];
                    }else{
                         $tabItems[] = ['label' => Yii::t('app', 'Oferty'),
                            'content' => $this->render('_tabAgencyOffers', ['model' => $model]),
                            'options' => ['id' => 'tab-offers',]   
                            ];                    
                    }
                    $tabItems[] = ['label' => Yii::t('app', 'Projekty'),
                            'content' => $this->render('_tabProjects', ['model' => $model]),
                            'options' => ['id' => 'tab-project',]

                        ];
                }
                    $tabItems[] = ['label' => Yii::t('app', 'Notatki'),
                        'content' => $this->render('_tabNotes', ['model' => $model]),
                        'options' => ['id' => 'tab-notes',]

                    ];
                    $tabItems[] = ['label' => Yii::t('app', 'Zadania'),
                        'content' => $this->render('_tabTask', ['model' => $model]),
                        'options' => ['id' => 'tab-tasks',]

                    ];
                    $tabItems[] = ['label' => Yii::t('app', 'Załączniki'),
                        'content' => $this->render('_tabAttachment', ['model' => $model]),
                        'options' => ['id' => 'tab-attachment',]

                    ];
                    
                    $tabItems[] = ['label' => Yii::t('app', 'Historia'),
                        'content' => $this->render('_tabLog', ['model' => $model]),
                        'options' => ['id' => 'tab-log',]

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



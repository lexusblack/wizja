<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
\common\assets\Gmap3Asset::register($this);
$user = Yii::$app->user;

/* @var $this yii\web\View */
/* @var $model common\models\Event */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Miejsca'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;

?>
<div class="event-view">

    <p>
        <?php if ($model->public<2){ ?>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj zdjęcie'), ['location-photo/create', 'locationId'=>$model->id], ['class' => 'btn btn-success']) ?>
        <?php }else{ ?>
        <?= Html::a('<i class="fa fa-alert"></i> ' . Yii::t('app', 'Zgłoś błąd'), ['send-mail', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>
        <?php } ?>

    </p>
<div class="row">
    <div class="col-md-5">
    <div class="row">
    <div class="col-md-12">
    <div class="ibox float-e-margins">
                        <div>
                            <div class="ibox-content no-padding border-left-right">
                            <?php if (($model->photo)||(count($model->locationPhotos))) { ?>
                            <div class="carousel slide" id="carousel1">
                                <div class="carousel-inner">
                                <?php $active = " active"; if ($model->photo){ ?>
                                    <div class="item<?=$active?>">
                                         <img alt="image" class="img-responsive" src="<?php echo $model->getPhotoUrl(); ?>">
                                    </div>
                                <?php $active = ""; } ?>
                                    <?php foreach ($model->locationPhotos as $lp) { ?>
                                        <div class="item<?=$active?>">
                                            <img alt="image"  class="img-responsive" src="<?=$lp->getFileUrl(); ?>">
                                        </div>
                                    <?php $active = ""; } ?>

                                </div>
                                <a data-slide="prev" href="#carousel1" class="left carousel-control">
                                    <span class="icon-prev"></span>
                                </a>
                                <a data-slide="next" href="#carousel1" class="right carousel-control">
                                    <span class="icon-next"></span>
                                </a>
                            </div>
                             <?php } ?>  
                            </div>
                            <div class="ibox-content profile-content">
                                <h4><strong><?php echo $model->name ?></strong>
                                <?php if ($model->stars>0){
                                    for ($i=0; $i<$model->stars;$i++)
                                    {
                                        echo "<i class='fa fa-star'></i>";
                                    }
                                    }?>
                                </h4>
                                 
                                <?php  if ($model->address!="") { ?>
                                <p><i class="fa fa-map-marker"></i> <?php echo $model->address.", ".$model->zip." ".$model->city ?></p>
                                <?php } ?>
                                <div class="row">
                                <div class="col-md-6">
                                <?php  if ($model->manager_phone!="") { ?>
                                <p><i class="fa fa-phone"></i> <?php echo $model->manager_phone; ?></p>
                                <?php } ?>
                                <?php  if ($model->electrician_phone!="") { ?>                                
                                <p><i class="fa fa-plug"></i> <?php echo $model->electrician_phone ?></p>
                                <?php } ?>
                                <?php  if ($model->distance!="") { ?>                                
                                <p><i class="fa fa-truck"></i> <?php echo $model->getGoogleDistance()." km"; ?></p>
                                <?php } ?>

                                </div>
                                <div class="col-md-6">
                                <?php  if ($model->email!="") { ?>                                
                                <p><i class="fa fa-envelope"></i> <?= Html::a($model->email, 'mailto:'.$model->email) ?></p>
                                <?php } ?>
                                <?php  if ($model->website!="") { ?>                                
                                <p><i class="fa fa-globe"></i> <?= Html::a($model->website, $model->website, ['target' => '_blank']) ?></p>
                                <?php } ?>
                                <?php  if ($model->beds>0) { ?>                                
                                <p><i class="fa fa-bed"></i> <?= Yii::t('app', 'Miejsc noclegowych') ?>: <?=$model->beds?></p>
                                <?php } ?>
                                <?php  if ($model->biggest_room>0) { ?>                                
                                <p><i class="fa fa-slideshare"></i> <?= Yii::t('app', 'Największa sala') ?>: <?=$model->biggest_room?></p>
                                <?php } ?>
                                </div>
                                </div>
                                <?php  if ($model->info!="") { ?>                                
                                <h5>
                                    <?= Yii::t('app', 'Informacje') ?>
                                </h5>
                                <p>
                                    <?php echo nl2br($model->info); ?>
                                </p>
                                <?php } ?>                              
                    </div>
                </div>
        </div>
    </div>
    <?php if ($user->can('locationLocationsViewPanorama')) { ?>
    <div class="col-md-12">
    <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Panoramy') ?></h5>
                        </div>
                        <div class="ibox-content">
                        <?php if ($model->public<2){ ?>
                        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['location-panorama/create', 'locationId'=>$model->id], ['class' => 'btn btn-success']) ?>
                        <?php }else{ ?>
                        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Prześlij panoramę'), ['location-panorama/create', 'locationId'=>$model->id], ['class' => 'btn btn-success']) ?>
                        <?php } ?>
                        <?php echo $this->render('_divPanorama', ['model'=>$model]); ?>
                        </div>
                    </div>
    </div>
    <?php }?>
    <?php if ($user->can('locationLocationsViewPlans')) { ?>
    <div class="col-md-12">
    <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Plany techiczne') ?></h5>
                        </div>
                        <div class="ibox-content">
                        <?php if ($model->public<2){ ?>
                        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['location-plan/create', 'locationId'=>$model->id], ['class' => 'btn btn-success']) ?>
                        <?php }else{ ?>
                        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Prześlij plan'), ['location-plan/create', 'locationId'=>$model->id], ['class' => 'btn btn-success']) ?>
                        <?php } ?>
                        <?php echo $this->render('_divPlan', ['model'=>$model]); ?>
                        </div>
                    </div>
    </div>
    <?php } ?>
    <?php if ($model->video!=""){ ?>
    <div class="col-md-12">
    <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Video') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/<?=$model->video ?>" frameborder="0" allowfullscreen class="video"></iframe>
                            </div>
                        </div>
                    </div>
    </div>
    <?php } ?>
    </div>
        </div>
    <div class="col-md-7">
        <div class="tabs-container">

            <?php
            $tabItems = [
                [
                    'label'=>Yii::t('app', 'Opis'),
                    'content'=>$this->render('_tabDescription', ['model'=>$model]),
                    'active'=>true,
                    'options'=> [
                        'id'=>'tab-description',
                    ]
                ],
                [
                    'label'=>Yii::t('app', 'Mapa'),
                    'content'=>$this->render('_tabMap', ['model'=>$model]),
                    'active'=>false,
                     'options'=> [
                        'id'=>'tab-map',
                    ]
                ],
                 [
                    'label'=>Yii::t('app', 'Sale konferencyjne'),
                    'content'=>$this->render('_tabRooms', ['model'=>$model]),
                    'active'=>false,
                     'options'=> [
                        'id'=>'tab-room',
                    ]
                ],               
                [
                    'label'=>'<i class="fa fa-paperclip"></i> '.Yii::t('app', 'Załączniki'),
                    'content'=>$this->render('_tabAttachment', ['model'=>$model]),
                    'active'=>false,
                    'options'=> [
                        'id'=>'tab-files',
                    ]
                ],
                [
                    'label'=>'<i class="fa fa-paperclip"></i> '.Yii::t('app', 'Notatki'),
                    'content'=>$this->render('_tabNote', ['model'=>$model]),
                    'active'=>false,
                    'options'=> [
                        'id'=>'tab-notes',
                    ]
                ],
            ];


            echo TabsX::widget([
                'items'=>$tabItems,
                'encodeLabels'=>false,
                'enableStickyTabs'=>true,
                'pluginEvents'=> [
                    'shown.bs.tab'=>'function(e){
                        var id = $(this).find("ul").prop("id");
                        var tab = $(this).find("ul li.active");                                                
                        var index = $(this).find("ul li").index(tab);
                        var mapTabIndex = $("#tab-map").index(".tab-pane");
                        if (index == mapTabIndex)
                        {
                        $("#map1")
                           .gmap3({
                            address: "'.$model->city.', '.$model->address.'",
                            zoom: 13,
                            mapTypeId : google.maps.MapTypeId.ROADMAP,
                            scrollwheel: false,
                          })
                          .marker([
                                {address:"'.$model->city.', '.$model->address.'"},
                              ]);
                        }
                    }'
                ]
            ]);
            ?>
        </div>
    </div>
</div>
</div>
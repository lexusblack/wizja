<?php
/* @var $this yii\web\View */
/* @var $model common\models\Offer */
/* @var $offerForm \backend\modules\offers\models\OfferForm */
use common\models\EventGearItem;

$formatter = Yii::$app->formatter;
use yii\helpers\Html;

?>
    <div class="pdf_box">
        <div class="client_info">
            <div class="hb fl">
                <div class="upf"><b><?= Yii::t('app', 'Wydarzenie dla') ?>:</b></div>
                <p><b>
                    <?=$model->customer->name ?></b>
                </p>
                <p><?=$model->customer->address ?></p>
                <p><?=$model->customer->zip ?> <?=$model->customer->city ?></p>
                <p><?= Yii::t('app', 'NIP') ?>: <?=$model->customer->nip ?></p>
                <?php if (isset($model->contact)){ ?>
                <p><?=$model->contact->first_name." ".$model->contact->last_name ?></p>
                <p><?= Yii::t('app', 'tel') ?>: <?=$model->contact->phone ?></p>
                <p><?= Yii::t('app', 'e-mail') ?>: <?=$model->contact->email ?></p>
                <?php }else{ ?>
                <p><?= Yii::t('app', 'tel') ?>: <?=$model->customer->phone ?></p>
                <p><?= Yii::t('app', 'e-mail') ?>: <?=$model->customer->email ?></p>
                <?php } ?>

            </div>
            <div class="hb fl">
                <div class="upf"><b><?= Yii::t('app', 'Miejsce') ?>: </b><?=$model->getDestinationAddress();?></div>
                <div class="upf"><b><?= Yii::t('app', 'Samochody') ?>: </b>
                <?php foreach ($model->getVehicles()->all() as $v) { echo $v->name.", "; } ?></div>
                <div class="upf"><b><?= Yii::t('app', 'Harmonogram') ?>:</b></div>
                <?php foreach ($model->eventSchedules as $schedule){ ?>
                <?php if ($schedule->start_time){
                    echo "<p>".$schedule->name.": ".substr($schedule->start_time, 0,16)." - ".substr($schedule->end_time, 0,16)."</p>";
                    } ?>
                <?php 
                    } ?>
            </div>
        </div> 
             <div>
                <h3><u><?= Yii::t('app', 'Pracownicy') ?>:</u></h3>
                <p>
                    <?php foreach($model->getUsers()->all() as $u){
                        echo $u->displayLabel.", ";
                        } ?>
                </p>
                <div>
                <?php if ($model->description){ ?>
                <div class="hb fl">
                <p><b><?=Yii::t('app', 'Opis')?>: </b><?=$model->description?>

                </p>
                </div>
                  <?php  }?>
                <?php
                $fields = \common\models\EventFieldSetting::find()->where(['active'=>1])->andWhere(['visible_on_packlist'=>1])->andWhere(['packlist_position'=>1])->all();
                if ($fields)
                {
                    ?>
                    <div class="hb fl">
                    <?php foreach ($fields as $f)
                    {
                        ?>
                        <p><b><?=$f->name?>: </b><?=nl2br($model->getFieldValue($f->id))?></p>
                        <?php

                        } ?>
                    </div>
                    <?php
                }
                ?>
                </div>
                <h3><u><?= Yii::t('app', 'Sprzęt') ?>:</u></h3>
                <table class="table table-row-border">
                    <tr>
                        <th><?= Yii::t('app', 'Lp') ?>.</th>
                        <th><?= Yii::t('app', 'Nazwa') ?></th>
                        <th style="padding-left:30px; padding-right:30px;"><?= Yii::t('app', 'Ilość') ?></th>
                        <th><?= Yii::t('app', 'Numery') ?></th>
                        <th><?= Yii::t('app','Komentarz')?></th>

                        <th><?= ((Yii::$app->params['companyID']=="redbull")||(Yii::$app->params['companyID']=="corse")||(Yii::$app->params['companyID']=="live-stage"))?Yii::t('app','Uwagi') : Yii::t('app', 'Magazyn') ?></th>

                    </tr>
                    <?php 
                    $i=1;
                    $category_name = "";
                    foreach ($model->getAssignedGearModel([], $sort)->allModels as $m)
                    { 
                        ?>
                <tr>
                <?php if ($sort=="cat"){ ?>
                    
                    <?php
                        $category = $m->gear->category;
                            $categories = $category->parents()->all();
                            if (count($categories) > 1) {
                                $category = $categories[1];
                            }
                        if ($category_name!=$category->name)
                        {
                            echo "<td colspan=6 style='background-color:#aaa'><strong>".$category->name."</strong></td></tr><tr>";
                            $category_name = $category->name;
                        } 
                         } 
                        $planned_gears = [];
                        $m->updateCount();
                        $gear_no=$m->getTotal();
                        foreach ($m->gear->gearItems as $gear_item) {
                                if ($m->gear->no_items)
                                {
                                    
                                }else{
                                     $eg =EventGearItem::find()->where(['event_id' => $model->id])->andWhere(['gear_item_id' => $gear_item->id])->one();
                                     if ($eg) {
                                        $planned_gears[] = $gear_item;
                                    }                                   
                                }

                            }

                        ?>
                        <td><?=$i++?></td>
                        <td><?=$m->gear->name?></td>
                        <td style="text-align:center;"><?=$gear_no?></td>
                        <td>
                        <?php
                        $first = true;
                        foreach ($planned_gears as $g){
                            if ($first)
                            {
                                $first = false;
                            }else{
                                echo ", ";
                            }
                            echo $g->number;
                        }
                        ?>    
                        </td>
                            <td><?php foreach (\common\models\PacklistGear::find()->where(['event_gear_id'=>$m->id])->all() as $mg){ echo $mg->comment." ";}?></td>

                        <td><?php if ((Yii::$app->params['companyID']=="redbull")||(Yii::$app->params['companyID']=="live-stage")){ echo $m->comment; }else{ if (Yii::$app->params['companyID']=="corse"){echo $m->gear->info;}else{echo $m->gear->warehouse;}}  ?></td>

                    </tr>
                    <?php
                    
                }
                    $first = true;
                    foreach($model->getOuterGears()->all() as $gear)
                    {
                        $gear_no = $model->getEventOuterGears()->where(['outer_gear_id'=>$gear->id])->one();
                        if ($first)
                        {
                            $first = false;
                            echo "<tr><td colspan=6 style='background-color:#aaa'><strong>". Yii::t('app', 'Sprzęt wypożyczony')."</strong></td></tr><tr>"; ?>
                            <tr>
                                <th><?= Yii::t('app', 'Lp') ?>.</th>
                                <th><?= Yii::t('app', 'Nazwa') ?></th>
                                <th style="text-align:center;"><?= Yii::t('app', 'Ilość') ?></th>
                                <th><?= Yii::t('app', 'Uwagi') ?></th>
                                <th><?= Yii::t('app', 'Wypożyczający') ?></th>
                            </tr>
                            <?php

                        }
                        ?>
                        <tr>
                        <td><?=$i++?></td>
                        <td><?=$gear->outerGearModel->name?></td>
                        <td style="text-align:center;"><?=$gear_no->quantity?></td>
                        <td><?=$gear_no->description?></td>
                        <td><?=$gear->company->name?></td>
                        </tr>

                    <?php
                    }
                    $first = true;
                    foreach($model->getEventExtraItems()->all() as $gear)
                    {
                        if ($first)
                        {
                            $first = false;
                            echo "<tr><td colspan=6 style='background-color:#aaa'><strong>". Yii::t('app', 'Sprzęt dodatkowy')."</strong></td></tr>"; ?>
                            <tr>
                                <th><?= Yii::t('app', 'Lp') ?>.</th>
                                <th><?= Yii::t('app', 'Nazwa') ?></th>
                                <th style="text-align:center;"><?= Yii::t('app', 'Ilość') ?></th>
                                <th colspan=3><?= Yii::t('app', '') ?></th>
                            </tr>
                            <?php

                        }
                        ?>
                        <tr>
                        <td><?=$i++?></td>
                        <td><?=$gear->name?></td>
                        <td style="text-align:center;"><?=$gear->quantity?></td>
                        <td colspan=2><?=$gear->gearCategory->name." | ".$gear->weight."kg | ".$gear->volume."m3"?></td>
                        </tr>

                    <?php
                    }
                    ?>
                </table>
                <?php
                $fields = \common\models\EventFieldSetting::find()->where(['active'=>1])->andWhere(['visible_on_packlist'=>1])->andWhere(['packlist_position'=>2])->all();
                if ($fields)
                {
                    ?>
                    <div>
                    <?php foreach ($fields as $f)
                    {
                        ?>
                        <p><b><?=$f->name?>: </b><br/><?=nl2br($model->getFieldValue($f->id))?></p>
                        <?php

                        } ?>
                    </div>
                    <?php
                }
                ?>
                <?php
                $fields = \common\models\EventFieldSetting::find()->where(['active'=>1])->andWhere(['visible_on_packlist'=>1])->andWhere(['packlist_position'=>3])->all();
                if ($fields)
                {
                    ?>
                    <div>
                    <?php foreach ($fields as $f)
                    {
                        ?>
                        <pagebreak />
                        <h1><?=$f->name?>: </h1>
                        <p><?=nl2br($model->getFieldValue($f->id))?></p>
                        <?php

                        } ?>
                    </div>
                    <?php
                }
                ?>
            </div>       
        

    </div>



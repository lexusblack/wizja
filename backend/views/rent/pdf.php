<?php
/* @var $this yii\web\View */
/* @var $model common\models\Offer */
/* @var $offerForm \backend\modules\offers\models\OfferForm */
use common\models\RentGearItem;

$formatter = Yii::$app->formatter;
use yii\helpers\Html;

?>
    <div class="pdf_box">
        <div class="client_info">
            <div class="hb fl">
                <div class="upf"><b><?= Yii::t('app', 'Wydarzenie dla') ?>:</b></div>
                <h3>
                    <?=$model->customer->name ?>
                </h3>
                <p><?=$model->customer->address ?></p>
                <p><?=$model->customer->zip ?> <?=$model->customer->city ?></p>
                <p><?= Yii::t('app', 'NIP') ?>: <?=$model->customer->nip ?></p>
                <p><?= Yii::t('app', 'tel') ?>: <?=$model->customer->address ?></p>
                <p><?= Yii::t('app', 'e-mail') ?>: <?=$model->customer->email ?></p>
                <?php if ($model->contact_id){ ?>
                <p><?= Yii::t('app', 'Osoba kontaktowa') ?>: <?=$model->contact->displayLabel."<br/> ".$model->contact->email." ".$model->contact->phone ?></p>
                <?php } ?>
            </div>
            <div class="hb fl">
                <div class="upf"><b><?= Yii::t('app', 'Dane firmy') ?>:</b></div>
                <h3>
                    <?=$settings['companyName']->value ?>
                </h3>
                <p><?=$settings['companyAddress']->value ?></p>
                <p><?=$settings['companyCity']->value ?></p>
                <p><?= Yii::t('app', 'NIP') ?>: <?=$settings['companyNIP']->value ?></p>
                <?php if (isset($model->manager)){ ?>
                <p><?=$model->manager->first_name?> <?=$model->manager->last_name?></p>
                <p><?= Yii::t('app', 'tel') ?>: <?=$model->manager->phone ?></p>
                <p><?= Yii::t('app', 'e-mail') ?>: <?=$model->manager->email ?></p>
                <?php } ?>
            </div>
        </div> 
             <div>
             <?php if ($model->description) { ?>
             <h3><u><?= Yii::t('app', 'Opis') ?>:</u></h3>
             <p><?=$model->description?></p>
             <?php } ?>
             <?php if ($model->info) { ?>
             <h3><u><?= Yii::t('app', 'Uwagi') ?>:</u></h3>
             <p><?=$model->info?></p>
             <?php } ?>
                <h3><u><?= Yii::t('app', 'Packlista') ?>:</u></h3>
                <table class="table table-row-border">
                    <tr>
                        <th><?= Yii::t('app', 'Lp') ?>.</th>
                        <th><?= Yii::t('app', 'Nazwa') ?></th>
                        <th><?= Yii::t('app', 'Ilość') ?></th>
                        <th><?= Yii::t('app', 'Numery') ?></th>
                        <th><?= Yii::t('app', 'Miejsce') ?></th>
                    </tr>
                    <?php 
                    $i=1;
                    $category_name = "";
                    foreach ($model->getAssignedGearModel()->allModels as $m)
                    { ?>
                    <tr>
                    <?php
                        $category = $m->gear->category;
                            $categories = $category->parents()->all();
                            if (count($categories) > 1) {
                                $category = $categories[1];
                            }
                        if ($category_name!=$category->name)
                        {
                            echo "<td colspan=5 style='background-color:#aaa'><strong>".$category->name."</strong></td></tr><tr>";
                            $category_name = $category->name;
                        } 
                        $planned_gears = [];
                        $gear_no=$m->quantity;
                        foreach ($m->gear->gearItems as $gear_item) {
                                if ($m->gear->no_items)
                                {
                                    
                                }else{
                                     $eg =RentGearItem::find()->where(['rent_id' => $model->id])->andWhere(['gear_item_id' => $gear_item->id])->one();
                                     if ($eg) {
                                        $planned_gears[] = $gear_item;
                                    }                                   
                                }

                            }

                        ?>
                        <td><?=$i++?></td>
                        <td><?=$m->gear->name?></td>
                        <td><?=$gear_no?></td>
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
                        <td><?=$m->gear->location?></td>
                    </tr>
                    <?php
                    }
                    $first = true;
                    foreach($model->getOuterGears()->all() as $gear)
                    {
                        $gear_no = $model->getRentOuterGears()->where(['outer_gear_id'=>$gear->id])->one();
                        if ($first)
                        {
                            $first = false;
                            echo "<tr><td colspan=4 style='background-color:#aaa'><strong>". Yii::t('app', 'Sprzęt wypożyczony')."</strong></td></tr><tr>"; ?>
                            <tr>
                                <th><?= Yii::t('app', 'Lp') ?>.</th>
                                <th><?= Yii::t('app', 'Nazwa') ?></th>
                                <th><?= Yii::t('app', 'Ilość') ?></th>
                                <th><?= Yii::t('app', 'Wypożyczający') ?></th>
                            </tr>
                            <?php

                        }
                        ?>
                        <tr>
                        <td><?=$i++?></td>
                        <td><?=$gear->outerGearModel->name?></td>
                        <td><?=$gear_no->quantity?></td>
                        <td><?=$gear->company->name?></td>
                        </tr>

                    <?php
                    }
                    ?>
                    ?>
                </table>
            </div>       
        

    </div>



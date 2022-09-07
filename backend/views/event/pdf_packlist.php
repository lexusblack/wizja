<?php
use yii\bootstrap\Html;

?>
<div class="row" style="margin-top:30px">
    <div class="col-md-12">
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
                <?php if ($model->description){ ?>
                <p><b><?=Yii::t('app', 'Opis')?>: </b><?=$model->description?>

                </p>
                <?php } ?>
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
<h3><?=$packlist->name." <i class='fa fa-circle' style='color:".$packlist->color."'></i>"?>
</h3>
<p><?=str_ireplace("\r\n", "<br/>", $packlist->info);?></p>
<?php if ($sort == "cat"){ ?>
<table class="table table-row-border">
<tr>
    <th>#</th>
    <th><?=Yii::t('app', 'Nazwa')?></th>
    <th><?=Yii::t('app', 'Sztuk')?></th>
    <th><?=Yii::t('app', 'Uwagi')?></th>
    <?php if ($money){ ?>
    <th><?=Yii::t('app', 'Wartość sprzętu w PLN/szt.')?></th>
    <?php } ?>
    <th><?=Yii::t('app', 'Miejsce')?></th>
</tr>
<?php 
$i =1;

foreach ($packlist->getGearsByCategories() as $cat)
{
    if ($cat['cat']->color)
                        {
                            $style= "style='background-color:".$cat['cat']->color.";'";
                        }else{
                            $style = "";
                        }
    ?>
 <tr>
 <?php if ($money){ ?>
 <td colspan="6" <?=$style?>><strong><?=$cat['cat']->name?></strong></td>
 <?php }else{ ?>
    <td colspan="5" <?=$style?>><strong><?=$cat['cat']->name?></strong></td>
    <?php } ?>

</tr>   
    <?php
    foreach ($cat['items'] as $g)
    {
        $val = "";
        if ($g['type']=='gear'){
            $gear = $g['item']->eventGear->gear;
            $name = $gear->name;
            $val = $gear->value. "PLN";
            $info = $g['item']->comment;
            if ($gear->photo == null) {
            $photo = "-";
            }
            else {
                $photo = Html::img($gear->getPhotoUrl(), ['width'=>50]);
            }
        }
        if ($g['type']=='outer_gear'){
            $gear = $g['item']->eventOuterGear->outerGear->outerGearModel;
            $name = $gear->name;
            $info = $g['item']->info;
            if ($gear->photo == null) {
            $photo = "-";
            }
            else {
                $photo = Html::img($gear->getPhotoUrl(), ['width'=>50]);
            }
        }
        if ($g['type']=='extra'){
            $gear = $g['item']->eventExtraItem;
            $name = $gear->name;
            $photo = "-";
            $info = $g['item']->info;
        }
        $gg = $g;
        $g = $g['item']
        ?>
 <tr>
    <td><?=$i++?></td>
    <td><?=$name?></td>
    <td><?=$g->quantity?>
        <?php if ($gg['type']=='gear'){
            $c = \common\models\EventConflict::find()->where(['packlist_gear_id'=>$g->id])->andWhere(['resolved'=>0])->one();
            if ($c)
                echo " + ".$c->quantity." w konflikcie";
        }
        ?>

    </td>
    <td><?=$info?></td>
    <?php if ($money){ ?>
    <td><?=$val?></td>
    <?php } ?>
    <td></td>
</tr>       
<?php
    }
} ?>
</table>
<?php }else{
    ?>
    <h3>Sprzęt wewnętrzny</h3>
<table class="table table-row-border">
<tr>
    <th>#</th>
    <th><?=Yii::t('app', 'Nazwa')?></th>
    <th><?=Yii::t('app', 'Sztuk')?></th>
    <th><?=Yii::t('app', 'Uwagi')?></th>
    <th><?=Yii::t('app', 'Miejsce')?></th>
</tr>
<?php 
$i =1;
if ($sort=='name')
    $gears = \common\models\PacklistGear::find()->where(['packlist_id'=>$packlist->id])->joinWith(['gear'])->orderBy(['gear.name'=>SORT_ASC])->all();
if ($sort=='warehouse')
            $gears = \common\models\PacklistGear::find()->where(['packlist_id'=>$packlist->id])->joinWith(['gear'])->orderBy(['gear.location'=>SORT_ASC, 'gear.name'=>SORT_ASC])->all();
if ($sort=='comment')
            $gears = \common\models\PacklistGear::find()->where(['packlist_id'=>$packlist->id])->joinWith(['gear'])->orderBy(['comment'=>SORT_DESC, 'gear.name'=>SORT_ASC])->all();
foreach ($gears as $g)
{
?>
 <tr>
    <td><?=$i++?></td>
    <td><?=$g->gear->name?></td>
    <td><?=$g->quantity?></td>
    <td><?=$g->comment?></td>
    <td><?=$g->gear->location?></td>
</tr>       
<?php
    }
 ?>
</table>
<?php   $gears = \common\models\PacklistOuterGear::find()->where(['packlist_id'=>$packlist->id])->joinWith(['eventOuterGear'])->all(); 
if ($gears){ 
?>
    <h3>Sprzęt zewnętrzny</h3>
<table class="table table-row-border">
<tr>
    <th>#</th>
    <th><?=Yii::t('app', 'Nazwa')?></th>
    <th><?=Yii::t('app', 'Sztuk')?></th>
    <th><?=Yii::t('app', 'Uwagi')?></th>
    <th><?=Yii::t('app', 'Właściciel')?></th>
</tr>
<?php
foreach ($gears as $g)
{
?>
 <tr>
    <td><?=$i++?></td>
    <td><?=$g->eventOuterGear->outerGear->name?></td>
    <td><?=$g->quantity?></td>
    <td><?=$g->info?></td>
    <td><?=$g->eventOuterGear->outerGear->company->name?></td>
</tr>       
<?php
    }
 ?>
</table>
<?php
    }
 ?>
<?php   $gears = \common\models\PacklistExtra::find()->where(['packlist_id'=>$packlist->id])->joinWith(['eventExtraItem'])->orderBy(['event_extra_item.name'=>SORT_ASC])->all(); 
if ($gears){ 
?>
    <h3>Sprzęt Dodatkowy</h3>

<table class="table table-row-border">
<tr>
    <th>#</th>
    <th><?=Yii::t('app', 'Nazwa')?></th>
    <th><?=Yii::t('app', 'Sztuk')?></th>
    <th><?=Yii::t('app', 'Uwagi')?></th>
    <th><?=Yii::t('app', 'Sekcja')?></th>
</tr>
<?php
foreach ($gears as $g)
{
?>
 <tr>
    <td><?=$i++?></td>
    <td><?=$g->eventExtraItem->name?></td>
    <td><?=$g->quantity?></td>
    <td><?=$g->info?></td>
    <td><?=$g->eventExtraItem->gearCategory->name?></td>
</tr>       
<?php
    }
 ?>
</table>
<?php }
?>
<?php }
?>

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
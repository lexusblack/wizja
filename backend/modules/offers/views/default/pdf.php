<?php
/* @var $this yii\web\View */
/* @var $model common\models\Offer */
/* @var $offerForm \backend\modules\offers\models\OfferForm */

$formatter = Yii::$app->formatter;

use backend\modules\offers\models\OfferExtraItem;
use common\models\OfferGear;
use common\models\OfferOuterGear;
use yii\helpers\Html;
$titlefields = explode(";", $model->offerDraft->title_pdf_fields);
$transport_fields = explode(";", $model->offerDraft->transport_pdf_fields);
$crew_fields = explode(";", $model->offerDraft->crew_pdf_fields);
$gear_fields = explode(";", $model->offerDraft->gear_pdf_fields);
$other_fields = explode(";", $model->offerDraft->other_pdf_fields);
$footerfields = explode(";", $model->offerDraft->footer_pdf_fields);
$header_fields = explode(";", $model->offerDraft->header_pdf_fields);
if (!$prices)
{
    $gear_fields = array_diff($gear_fields, array("price", "total_price"));
    $crew_fields = array_diff($crew_fields, array("price", "total_price"));
    $transport_fields = array_diff($transport_fields, array("price", "total_price"));
    $other_fields = array_diff($other_fields, array("price", "total_price"));
}
$currency = $model->priceGroup->currency;
?>
<?php
                function cmp2($a, $b)
                {
                    return $a["position"] > $b["position"];
                }
                ?>
    <div class="pdf_box">
        <div class="client_info">
        <?php if ((in_array('client_address', $titlefields))||(in_array('client_name', $titlefields))){ ?>
            <div class="hb fl left-side">
                <?php if (in_array('client_name', $titlefields)){ ?>
                <p>    <?=$model->customer->name ?></p>
                <?php } ?>
                <?php if (in_array('client_address', $titlefields)){ ?>
                <p><?=$model->customer->address ?></p>
                <p><?=$model->customer->zip ?> <?=$model->customer->city ?></p>
                <p><?= Yii::t('app', 'NIP') ?>: <?=$model->customer->nip ?></p>
                
                <p><?= Yii::t('app', 'e-mail') ?>: <?=$model->customer->email ?></p>
                <p><?= Yii::t('app', 'tel') ?>: <?=$model->customer->phone ?></p>
                <?php if (isset($model->contact)){ ?>
                
                <p><?=$model->contact->displayLabel ?></p>
                <p><?= Yii::t('app', 'tel') ?>: <?=$model->contact->phone ?></p>
                <?php } ?>
                <?php } ?>
            </div>
        <?php } ?>
        <div class="hb fl">
        <?php if (in_array('manager', $titlefields)){ 
            if ($model->manager_id) {?>
            
                <table class="table table-row-border">
                    <tr>
                        <td><?= Yii::t('app', 'Kierownik projektu') ?>:</td>
                        <td><?=$model->manager->first_name?> <?=$model->manager->last_name?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'tel') ?>:</td>
                        <td><?=$model->manager->phone?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'e-mail') ?>:</td>
                        <td><?=$model->manager->email?></td>
                    </tr>
                </table>
            <?php } } ?>
                            <div class="name_box">
                            <table class="table table-row-border">
            <?php if (in_array('name', $titlefields)){ ?>
                
                <tr><td>    <?= Yii::t('app', 'Nazwa projektu') ?>:</td><td> <?=$model->name?></td></tr>
                
            <?php }?>
                            <?php if (in_array('number', $titlefields)){ ?>
                    <tr>
                        <td><?= Yii::t('app', 'Numer oferty') ?></td>
                        <td><?=$model->id?></td>
                    </tr>
                <?php } ?>
                <?php if (in_array('termin', $titlefields)){ ?>
            <?php if ($model->term_to) { ?>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Oferta ważna do') ?>:
                    </td>
                    <td>
                        <?=$model->term_to?>
                    </td>
                </tr>
            <?php } ?>
                <?php } ?>
                <?php if (in_array('datetime', $titlefields)){ ?>
                    <tr>
                        <td><?= Yii::t('app', 'Data sporządzenia oferty') ?>:</td>
                        <td><?=$model->offer_date?></td>
                    </tr>
                <?php } ?>
                <?php if (in_array('paying_date', $titlefields)){ ?>
            <?php if ($model->payment_days) { ?>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Termin płatności') ?>:
                    </td>
                    <td>
                        <?=$model->payment_days." ".Yii::t("app", "dni")?>
                    </td>
                </tr>
            <?php } ?>
                <?php } ?>
            <?php if (in_array('location', $titlefields)){ ?>
                <?php if ($model->location !== null){ ?>
                <tr><td><?= Yii::t('app', 'Mejsce i adres') ?>:</td><td> <p><?=$model->name?></p>
                <p><?=$model->location->name?></p>
                <p><?=$model->location->city?>, <?=$model->location->zip?></p>
                <p><?=$model->location->address?></p>
                </td></tr>
                <?php }else{ ?> <tr><td><?= Yii::t('app', 'Mejsce i adres') ?>:</td><td><?=$model->address?></td></tr> <?php } ?>
                <?php } ?>
                </table>
            </div>
        
            <?php 
            $showTimes = true;
            if ($showTimes){ ?>
            <?php if (in_array('harmonogram', $titlefields)){ ?>
            <?php if (count($model->offerSchedules)>0) { ?>
                <table class="table table-row-border">
                    <tr>
                        <td></td>
                        <td><?= Yii::t('app', 'Początek') ?></td>
                        <td><?= Yii::t('app', 'Koniec') ?></td>
                    </tr>
                    <?php 

                    

                    foreach ($model->offerSchedules as $schedule){
                        if(isset($schedule->start_time)){
                    
                    ?>
                        <tr>
                            <td><?php if ($schedule->translate!="") echo $schedule->translate; else echo $schedule->name;?></td>
                            <td><?=substr($schedule->start_time, 0, 16)?></td>
                            <td><?=substr($schedule->end_time, 0, 16)?></td>
                        </tr>
                    <?php }} ?>
                    
                </table>
            <?php } ?>
            <?php } ?>
            <?php } ?>
        </div> 
        </div>
        
        <div class="main_info">


            <?php
            if (Yii::$app->params['companyID']=="visualsupport")
            {
            if (in_array('info', $titlefields)){
            if ($model->comment!=""){
                echo "<div class='hb fl'>".$model->comment."</div>";
            }
        } } ?>
        </div>
        <div class="main_info" style="padding-left:1%; padding-right:1%;">
        <table class="table table-row-border offertable" cellpadding="5" cellspacing="0">
            <tbody>
            <!----------|MAGAZYN|---------->
            <?php $total_summ_of_cats = 0; ?>
            <?php
            $summ_of_one_cat = 0;
            $summ_of_v = 0;
            $summ_of_weight = 0;
            $summ_of_power_consumption = 0;
            $gear_fields_count = 7 - count($gear_fields);
             if (in_array('photo', $gear_fields)){
                $gear_fields_count++;
             }
             if (in_array('description', $gear_fields)){
                $gear_fields_count++;
             }
            foreach ($offerForm->allGears as $categoryName => $items):
                usort($items, "cmp2");
                $summ_of_one_cat = 0;
                        $cat = \common\models\GearCategory::find()->where(['lvl'=>1])->andWhere(['name'=>$categoryName])->andWhere(['active'=>1])->one();

                    $categoryName2 = \common\models\GearCategory::getTranslateName($model->language, $categoryName);
                 ?>
                <?php if ($cat) { ?>
                <tr <?=$cat->getStyle()?>>
                    <td <?=$cat->getStyle()?> colspan="7"><b><?=$categoryName2?></b></td>
                </tr>
                <?php }else { ?>
                <tr>
                    <td  colspan="7"><b><?=$categoryName2?></b></td>
                </tr>
                <?php } ?>
                <?php if ($model->offerDraft->gear_section==1){ ?>
                <tr>
                <?php if (in_array('name', $gear_fields)){ ?>
                    <th colspan="<?=$gear_fields_count?>"><?= Yii::t('app', 'Nazwa') ?></th>
                <?php } ?>
                <?php if (in_array('info', $gear_fields)){ ?>
                    <th><?= Yii::t('app', 'Opis') ?></th>
                <?php } ?>
                <?php if (in_array('price', $gear_fields)){ ?>
                    <th><?= Yii::t('app', 'Cena') ?></th>
                <?php } ?>
                <?php if (in_array('quantity', $gear_fields)){ ?>
                    <th style="text-align: center;"><?= Yii::t('app', 'Liczba') ?></th>
                <?php } ?>
                    
                <?php if (in_array('discount', $gear_fields)){ ?>
                    <th style="text-align: center;"><?= Yii::t('app', 'Rabat') ?></th>
                <?php } ?>
                <?php if (in_array('days', $gear_fields)){ ?>
                <?php if (Yii::$app->params['companyID']!="wizja")
                        { ?>
                            <th style="text-align: center;"><?= Yii::t('app', 'Dni pracy') ?></th>
                        <?php }else{ ?>
                            <th style="text-align: center;"><?= Yii::t('app', 'Przelicznik') ?></th>
                        <?php } ?>
                <?php } ?>
                <?php if (in_array('total_price', $gear_fields)){ ?>
                    <th><?= Yii::t('app', 'Razem netto') ?></th>
                <?php } ?>
                </tr>

                <?php } ?>
                <?php foreach ($items as  $gearId => $data):?>
                    <?php if ($data['type'] != 'outerGear') {
                        if ($data['type'] != 'extraGear') {
                            $baseIndex = 'gearModels[' . $gearId . ']'; ?>
                            <?php if ($data['quantity']){ ?>
                            <?php if ($model->offerDraft->gear_section==1){ ?>
                            <tr style="background-color:#fdfdfd">
                            <?php if (in_array('name', $gear_fields)){ ?>
                                <td colspan="<?=$gear_fields_count?>"><?=\common\models\Gear::getTranslateName($data['gear_id'], $model->language, $data['name']) ?></td>
                                <?php } ?>
                            <?php if (in_array('info', $gear_fields)){ ?>
                                <td><?= $data['description']?></td>
                                <?php } ?>
                            <?php if (in_array('price', $gear_fields)){ 
                                if ($data['total_value']==0){?>
                                    <td><?php echo $formatter->asCurrency($data['price'], $currency) ?></td>
                                <?php }else{ ?>
                                    <td><?php echo $formatter->asCurrency($data['total_price'], $currency) ?></td>
                            <?php } } ?>
                            <?php if (in_array('quantity', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $data['quantity']; ?></td>
                                <?php } ?>
                            <?php if (in_array('discount', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $data['discount'] ?></td>
                                <?php } ?>
                            <?php if (in_array('days', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $data['duration'] ?></td>
                                <?php } ?>
                            <?php if (in_array('total_price', $gear_fields)){ ?>
                                <td><?= $formatter->asCurrency($data['total_value'], $currency) ?></td>
                                <?php } ?>
                            </tr>
                            <?php if ((in_array('photo', $gear_fields))||(in_array('description', $gear_fields))){ ?>
                                            <tr>
                                                <?php if (in_array('photo', $gear_fields)){ ?>
                                                <td colspan="2"><?php if ($data['photo']!=""){ echo Html::img(\Yii::getAlias('@uploadroot' . '/gear/').$data['photo'],['height'=>'100']);}  ?></td>
                                                <?php } ?>

                                                <?php if (in_array('description', $gear_fields)){ ?>
                                                <td colspan="5"><?php 
                                                    echo \common\models\Gear::getTranslateDesc($data['gear_id'], $model->language, $data['long_description']);
                                                     ?></td>
                                                <?php } ?>

                                            </tr>

                                            <?php } ?>
                            <?php } ?>
                            <?php
                            $offerGears = OfferGear::find()->where(['offer_gear_id'=>$data['id']])->andWhere(['visible'=>1])->all();
                            foreach ($offerGears as $og)
                            { ?>
                            <?php if ($model->offerDraft->gear_section==1){ ?>
                            <tr>
                             <?php if (in_array('name', $gear_fields)){ ?>
                                <td style="padding-left:10px;" colspan="<?=$gear_fields_count?>">- <?= \common\models\Gear::getTranslateName($og->gear_id, $model->language, $og->gear->name)  ?></td>
                            <?php } ?>
                            <?php if (in_array('info', $gear_fields)){ ?>
                                <td><?= $og->description?></td>
                            <?php } ?>
                            <?php if (in_array('price', $gear_fields)){ ?>
                                <td><?php echo $formatter->asCurrency($og->price, $currency) ?></td>
                            <?php } ?>
                            <?php if (in_array('quantity', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->quantity; ?></td>
                                <?php } ?>
                            <?php if (in_array('discount', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->discount ?></td>
                            <?php } ?>
                            <?php if (in_array('days', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->duration ?></td>
                            <?php } ?>
                            <?php if (in_array('total_price', $gear_fields)){ ?>
                                <td><?= $formatter->asCurrency($og->getValue(), $currency) ?></td>
                            <?php } ?>
                            </tr>
                            <?php if ((in_array('photo', $gear_fields))||(in_array('description', $gear_fields))){ ?>
                                            <tr>
                                                <?php if (in_array('photo', $gear_fields)){ ?>
                                                <td colspan="2"><?php if ($og->gear->photo){ echo Html::img(\Yii::getAlias('@uploadroot' . '/gear/').$og->gear->photo,['height'=>'100']);}  ?></td>
                                                <?php } ?>

                                                <?php if (in_array('description', $gear_fields)){ ?>
                                                <td colspan="5"><?php 
                                                    echo \common\models\Gear::getTranslateDesc($og->gear_id, $model->language, $og->gear->offer_description);
                                                      ?></td>
                                                <?php } ?>

                                            </tr>

                                            <?php } ?>
                            <?php } ?>
                            <?php 
                                $summ_of_one_cat += $og->getValue();
                                $summ_of_v += $og->gear->countVolume() * $og->quantity;
                                $summ_of_weight += $og->gear->weight * $og->quantity;
                                $summ_of_power_consumption += $og->gear->power_consumption * $og->quantity;
                            }
                        }
                            $offerOuterGears = OfferOuterGear::find()->where(['offer_gear_id'=>$data['id']])->andWhere(['visible'=>1])->all();
                            foreach ($offerOuterGears as $og)
                            { ?>
                        <?php if ($model->offerDraft->gear_section==1){ ?>
                            <tr>
                            <?php if (in_array('name', $gear_fields)){ ?>
                                <td style="padding-left:10px;" colspan="<?=$gear_fields_count?>">- <?=\common\models\OuterGearModel::getTranslateName($og->outerGearModel->id, $model->language, $og->outerGearModel->name) ?></td>
                            <?php } ?>
                            <?php if (in_array('info', $gear_fields)){ ?>
                                <td><?= $og->description?></td>
                            <?php } ?>
                            <?php if (in_array('price', $gear_fields)){ ?>
                                <td><?php echo $formatter->asCurrency($og->price, $currency) ?></td>
                            <?php } ?>
                             <?php if (in_array('quantity', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->quantity; ?></td>
                                <?php } ?>
                            <?php if (in_array('discount', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->discount ?></td>
                            <?php } ?>
                            <?php if (in_array('days', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->duration ?></td>
                            <?php } ?>
                            <?php if (in_array('total_price', $gear_fields)){ ?>
                                <td><?= $formatter->asCurrency($og->getValue(), $currency) ?></td>
                            <?php } ?>
                            </tr>
                            <?php } ?>
                            <?php 
                                $summ_of_one_cat += $og->getValue();
                                    $summ_of_v += $og->outerGearModel->countVolume() * $og->quantity;
                                    $summ_of_weight += $og->outerGearModel->weight * $og->quantity;
                                    $summ_of_power_consumption += $og->outerGearModel->power_consumption * $og->quantity;
                            }
                            $summ_of_one_cat += $data['total_value'];
                            $summ_of_v += $data['volume'] * $data['quantity'];
                            $summ_of_weight += $data['weight'] * $data['quantity'];
                            $summ_of_power_consumption += $data['power_consumption'] * $data['quantity'];
                            ?>
                            <?php
                        }
                        else {
                            $summ_of_one_cat += $data['total_value']; ?>
                            <?php if ($model->offerDraft->gear_section==1){ ?>
                            <tr style="background-color:#fdfdfd">
                            <?php if (in_array('name', $gear_fields)){ ?>
                                <td colspan="<?=$gear_fields_count?>"><?= $data['name'] ?></td>
                            <?php } ?>
                            <?php if (in_array('info', $gear_fields)){ ?>
                                <td><?= $data['description'] ?></td>
                            <?php } ?>
                            <?php if (in_array('price', $gear_fields)){ 
                                if ($data['total_value']==0){?>
                                    <td><?php echo $formatter->asCurrency($data['price'], $currency) ?></td>
                                <?php }else{ ?>
                                    <td><?php echo $formatter->asCurrency($data['total_price'], $currency) ?></td>
                            <?php } } ?>
                            <?php if (in_array('quantity', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $data['quantity'] ?></td>
                                <?php } ?>
                            <?php if (in_array('discount', $gear_fields)){ ?>
                            <td style="text-align: center;"><?php echo $data['discount'] ?></td>
                            <?php } ?>
                            <?php if (in_array('days', $gear_fields)){ ?>
                            <td style="text-align: center;"><?php echo $data['duration'] ?></td>
                            <?php } ?>
                            <?php if (in_array('total_price', $gear_fields)){ ?>
                            <td><?= $formatter->asCurrency($data['total_value'], $currency) ?></td>
                            <?php } ?>
                            </tr>   
                            <?php } ?>
                                                 <?php
                            $offerGears = OfferGear::find()->where(['offer_group_id'=>$data['id']])->andWhere(['visible'=>1])->all();
                            foreach ($offerGears as $og)
                            { ?>
                        <?php if ($model->offerDraft->gear_section==1){ ?>
                            <tr>
                            <?php if (in_array('name', $gear_fields)){ ?>
                                <td style="padding-left:10px;" colspan="<?=$gear_fields_count?>">- <?= \common\models\Gear::getTranslateName($og->gear_id, $model->language, $og->gear->name)  ?></td>
                            <?php } ?>
                            <?php if (in_array('info', $gear_fields)){ ?>
                                <td><?= $og->description?></td>
                            <?php } ?>
                            <?php if (in_array('price', $gear_fields)){ ?>
                                <td><?php echo $formatter->asCurrency($og->price, $currency) ?></td>
                            <?php } ?>
                            <?php if (in_array('quantity', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->quantity; ?></td>
                                <?php } ?>
                            <?php if (in_array('discount', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->discount ?></td>
                            <?php } ?>
                            <?php if (in_array('days', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->duration ?></td>
                            <?php } ?>
                            <?php if (in_array('total_price', $gear_fields)){ ?>
                                <td><?= $formatter->asCurrency($og->getValue(), $currency) ?></td>
                            <?php } ?>
                            </tr>
                                <?php if ((in_array('photo', $gear_fields))||(in_array('description', $gear_fields))){ ?>
                                            <tr>
                                                <?php if (in_array('photo', $gear_fields)){ ?>
                                                <td colspan="2"><?php if ($og->gear->photo){ echo Html::img(\Yii::getAlias('@uploadroot' . '/gear/').$og->gear->photo,['height'=>'100']);}  ?></td>
                                                <?php } ?>

                                                <?php if (in_array('description', $gear_fields)){ ?>
                                                <td colspan="5"><?php 
                                                        echo \common\models\Gear::getTranslateDesc($og->gear_id, $model->language, $og->gear->offer_description);
                                                     ?></td>
                                                <?php } ?>

                                            </tr>

                                            <?php } ?>
                            <?php } ?>

                            <?php 
                                $summ_of_one_cat += $og->getValue();
                                $summ_of_v += $og->gear->countVolume() * $og->quantity;
                                $summ_of_weight += $og->gear->weight * $og->quantity;
                                $summ_of_power_consumption += $og->gear->power_consumption * $og->quantity;
                            }
                            $offerOuterGears = OfferOuterGear::find()->where(['offer_group_id'=>$data['id']])->andWhere(['visible'=>1])->all();
                            foreach ($offerOuterGears as $og)
                            { ?>
                        <?php if ($model->offerDraft->gear_section==1){ ?>
                            <tr>
                            <?php if (in_array('name', $gear_fields)){ ?>
                                <td style="padding-left:10px;" colspan="<?=$gear_fields_count?>">- <?=\common\models\OuterGearModel::getTranslateName($og->outerGearModel->id, $model->language, $og->outerGearModel->name) ?></td>
                            <?php } ?>
                            <?php if (in_array('info', $gear_fields)){ ?>
                                <td><?= $og->description?></td>
                            <?php } ?>
                            <?php if (in_array('price', $gear_fields)){ ?>
                                <td><?php echo $formatter->asCurrency($og->price, $currency) ?></td>
                            <?php } ?>
                            <?php if (in_array('quantity', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->quantity; ?></td>
                                <?php } ?>
                            <?php if (in_array('discount', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->discount ?></td>
                            <?php } ?>
                            <?php if (in_array('days', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->duration ?></td>
                            <?php } ?>
                            <?php if (in_array('total_price', $gear_fields)){ ?>
                                <td><?= $formatter->asCurrency($og->getValue(), $currency) ?></td>
                            <?php } ?>
                            </tr>
                            <?php } ?>
                            <?php 
                            $summ_of_one_cat += $og->getValue();
                                    $summ_of_v += $og->outerGearModel->countVolume() * $og->quantity;
                                    $summ_of_weight += $og->outerGearModel->weight * $og->quantity;
                                    $summ_of_power_consumption += $og->outerGearModel->power_consumption * $og->quantity;
                            }
                        }
                    }else{
                        $outer = $data;
                        if ($model->offerDraft->gear_section==1){ ?>
                        <tr style="background-color:#fdfdfd">
                            <?php if (in_array('name', $gear_fields)){ ?>
                            <td colspan="<?=$gear_fields_count?>"><?=\common\models\OuterGearModel::getTranslateName($outer['gear_id'], $model->language, $outer['name']) ?> </td>
                            <?php } ?>
                            <?php if (in_array('info', $gear_fields)){ ?>
                            <td><?=$outer['description']?></td>
                            <?php } ?>
                            <?php if (in_array('price', $gear_fields)){ ?>
                            <td><?php echo $formatter->asCurrency($outer['total_price'], $currency)?></td>
                            <?php } ?>
                            <?php if (in_array('quantity', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $outer['quantity']; ?></td>
                                <?php } ?>
                            <?php if (in_array('discount', $gear_fields)){ ?>
                            <td style="text-align: center;"><?php echo $outer['discount'] ?></td>
                            <?php } ?>
                            <?php if (in_array('days', $gear_fields)){ ?>
                            <td style="text-align: center;"><?php echo $outer['duration'] ?></td>
                            <?php } ?>
                            <?php if (in_array('total_price', $gear_fields)){ ?>
                            <td><?=$formatter->asCurrency($outer['total_value'], $currency)?></td>
                            <?php } ?>
                            
                        </tr>
                        <?php } ?>
                        <?php
                            $offerGears = OfferGear::find()->where(['offer_outer_gear_id'=>$outer['id']])->andWhere(['visible'=>1])->all();
                            foreach ($offerGears as $og)
                            { ?>
                        <?php if ($model->offerDraft->gear_section==1){ ?>
                            <tr>
                            <?php if (in_array('name', $gear_fields)){ ?>
                                <td style="padding-left:10px;" colspan="<?=$gear_fields_count?>">- <?= \common\models\Gear::getTranslateName($og->gear_id, $model->language, $og->gear->name)  ?></td>
                            <?php } ?>
                            <?php if (in_array('info', $gear_fields)){ ?>
                                <td><?= $og->description?></td>
                            <?php } ?>
                            <?php if (in_array('price', $gear_fields)){ ?>
                                <td><?php echo $formatter->asCurrency($og->price, $currency) ?></td>
                            <?php } ?>
                            <?php if (in_array('quantity', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->quantity; ?></td>
                                <?php } ?>
                            <?php if (in_array('discount', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->discount ?></td>
                            <?php } ?>
                            <?php if (in_array('days', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->duration ?></td>
                            <?php } ?>
                            <?php if (in_array('total_price', $gear_fields)){ ?>
                                <td><?= $formatter->asCurrency($og->getValue(), $currency) ?></td>
                            <?php } ?>
                            </tr>
                            <?php if ((in_array('photo', $gear_fields))||(in_array('description', $gear_fields))){ ?>
                                            <tr>
                                                <?php if (in_array('photo', $gear_fields)){ ?>
                                                <td colspan="2"><?php if ($og->gear->photo){ echo Html::img(\Yii::getAlias('@uploadroot' . '/gear/').$og->gear->photo,['height'=>'100']);}  ?></td>
                                                <?php } ?>

                                                <?php if (in_array('description', $gear_fields)){ ?>
                                                <td colspan="5"><?php 
                                                        echo \common\models\Gear::getTranslateDesc($og->gear_id, $model->language, $og->gear->offer_description);
                                                     ?></td>
                                                <?php } ?>

                                            </tr>

                                            <?php } ?>
                            <?php } ?>
                            <?php 
                                $summ_of_one_cat += $og->getValue();
                                $summ_of_v += $og->gear->countVolume() * $og->quantity;
                                $summ_of_weight += $og->gear->weight * $og->quantity;
                                $summ_of_power_consumption += $og->gear->power_consumption * $og->quantity;
                            }
                            $offerOuterGears = OfferOuterGear::find()->where(['offer_outer_gear_id'=>$outer['id']])->andWhere(['visible'=>1])->all();
                            foreach ($offerOuterGears as $og)
                            { ?>
                        <?php if ($model->offerDraft->gear_section==1){ ?>
                            <tr>
                            <?php if (in_array('name', $gear_fields)){ ?>
                                <td style="padding-left:10px;" colspan="<?=$gear_fields_count?>">- <?=\common\models\OuterGearModel::getTranslateName($og->outerGearModel->id, $model->language, $og->outerGearModel->name) ?> </td>
                            <?php } ?>
                            <?php if (in_array('info', $gear_fields)){ ?>
                                <td><?= $og->description?></td>
                            <?php } ?>
                            <?php if (in_array('price', $gear_fields)){ ?>
                                <td><?php echo $formatter->asCurrency($og->price, $currency) ?></td>
                            <?php } ?>
                            <?php if (in_array('quantity', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->quantity; ?></td>
                                <?php } ?>
                            <?php if (in_array('discount', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->discount ?></td>
                            <?php } ?>
                            <?php if (in_array('days', $gear_fields)){ ?>
                                <td style="text-align: center;"><?php echo $og->duration ?></td>
                            <?php } ?>
                            <?php if (in_array('total_price', $gear_fields)){ ?>
                                <td><?= $formatter->asCurrency($og->getValue(), $currency) ?></td>
                            <?php } ?>
                            </tr>
                            <?php } ?>
                            <?php 
                            $summ_of_one_cat += $og->getValue();
                                    $summ_of_v += $og->outerGearModel->countVolume() * $og->quantity;
                                    $summ_of_weight += $og->outerGearModel->weight * $og->quantity;
                                    $summ_of_power_consumption += $og->outerGearModel->power_consumption * $og->quantity;
                            }
                        $summ_of_one_cat += $outer['total_value'];
                        $summ_of_v += $outer['volume'] * $outer['quantity'];
                        $summ_of_weight += $outer['weight'] * $outer['quantity'];
                        $summ_of_power_consumption += $outer['power_consumption'] * $outer['quantity'];
                    }

                endforeach;

                ?>
                <?php if ($prices){ ?>
                <tr class="warning">
                    <td colspan="6"><b><?= Yii::t('app', 'Łącznie') ?> <?=$categoryName2?></b></td>
                    <td><?=$formatter->asCurrency($summ_of_one_cat, $currency)?></td>
                </tr>
                <?php } ?>
                <?php $total_summ_of_cats += $summ_of_one_cat; ?>
            <?php endforeach; ?>
            <!----------|END MAGAZYN|---------->
            </tbody>
            </table>
            <!----------|TRANSPORT|---------->
            <table class="table table-row-border offertable" cellpadding="5" cellspacing="0">
            <?php $transport_summ = 0; $transport_fields_count = 8 - count($transport_fields);?>
                <?php 
                if (($model->getVehicleData()[Yii::t('app', 'Transport')])||($model->getExtraItem(OfferExtraItem::TYPE_VEHICLE))){
                if (isset($settings['transportColor']))
                        {
                            $style= "style='background-color:".$settings['transportColor']->value.";";
                        }else{
                            $style = "style='";
                        } 
                if (isset($settings['transportFontColor']))
                        {
                        if ($settings['transportFontColor']!="")
                                    $style.= "color:".$settings['transportFontColor']->value.";";
                        }
                $style .= "'"; ?>
            <tr <?=$style?>>
                <td colspan="9" <?=$style?>><b><?= Yii::t('app', 'Transport') ?></b></td>
            </tr>
            <?php if ($model->offerDraft->transport_section==1){ ?>
            <tr>
            <?php if (in_array('name', $transport_fields)){ ?>
                <th><?= Yii::t('app', 'Samochód') ?></th>
                <?php } ?>
                        <?php if (in_array('description', $transport_fields)){ ?>
                <th><?= Yii::t('app', 'Opis') ?></th>
                <?php } ?>
                <th><?= Yii::t('app', 'Liczba') ?></th>
            <?php if (in_array('km', $transport_fields)){ ?>
                <th><?= Yii::t('app', 'Przelicznik') ?></th>
            <?php } ?>
            <?php if (in_array('price_group', $transport_fields)){ ?>
                <th><?= Yii::t('app', 'Stawka') ?></th>
                <?php } ?>
            <?php if (in_array('price', $transport_fields)){ ?>
                <th><?= Yii::t('app', 'Cena') ?></th>
            <?php } ?>
                <th colspan="<?=$transport_fields_count?>"></th>
            <?php if (in_array('total_price', $transport_fields)){ ?>
                <th><?= Yii::t('app', 'Razem netto') ?></th>
            <?php } ?>
            </tr>
            <?php } ?>
            <?php foreach ($model->offerSchedules as $schedule)
            {
                $time_type = $schedule->id;
                if ($schedule->translate)
                    $s_name = $schedule->translate;
                else
                    $s_name = $schedule->name;
                $title = "<tr><td colspan='9' style='text-align:center; font-weight:bold; background-color:#fdfdfd;'>".$s_name."</td></tr>";
                $title_shown = false;
            foreach ($model->getVehicleData() as $key => $vehicles): ?>
                <?php foreach ($vehicles as $id=>$vehicle): 
                if ($vehicle['type']==$time_type){ 
                        if (!$title_shown)
                            {
                                $title_shown = true;
                                echo $title;
                            } ?>
                    ?>
                    <?php $baseIndex = 'vehicleModels['.$id.']'; ?>
                    <?php
                    $price = $vehicle['value'];
                    $transport_summ += $price;
                    ?>
                    <?php if ($model->offerDraft->transport_section==1){ ?>
                    <tr>
                    <?php if (in_array('name', $transport_fields)){ ?>
                        <td><?=\common\models\Vehicle::getTranslateName($vehicle['id'], $model->language, $vehicle['name']) ?></td>
                    <?php } ?>
                    <?php if (in_array('description', $transport_fields)){ ?>
                        <td><?=$vehicle['description']?></td>
                    <?php } ?>
                        <td><?=$vehicle['quantity']?></td>
                    <?php if (in_array('km', $transport_fields)){ ?>
                        <td><?php echo  $vehicle['distance']." ".$vehicle['unit'] ?></td>
                    <?php } ?>
                    <?php if (in_array('price_group', $transport_fields)){ ?>
                        <td><?=$vehicle['price_group']?></td>
                    <?php } ?>
                    <?php if (in_array('price', $transport_fields)){ ?>
                        <td><?php echo  $vehicle['price'] ?></td>
                    <?php } ?>
                        <td colspan="<?=$transport_fields_count?>"></td>
                    <?php if (in_array('total_price', $transport_fields)){ ?>
                        <td><?php echo $formatter->asCurrency($price, $currency); ?></td>
                    <?php } ?>
                    </tr>
                    <?php } ?>
                <?php }endforeach; ?>
            <?php endforeach; ?>
            <?php
            foreach ($model->getExtraItem(OfferExtraItem::TYPE_VEHICLE) as $vehicle) {
                if ($vehicle->time_type==$time_type){ 
                    if (!$title_shown)
                            {
                                $title_shown = true;
                                echo $title;
                            }
                $transport_summ += $vehicle->price * $vehicle->quantity * $vehicle->duration * (1 - $vehicle->discount / 100); ?>
                <?php if ($model->offerDraft->transport_section==1){ ?>
                <tr>
                <?php if (in_array('name', $transport_fields)){ ?>
                    <td><?= $vehicle->name ?></td>
                <?php } ?>
                <?php if (in_array('description', $transport_fields)){ ?>
                        <td><?=$vehicle->description?></td>
                    <?php } ?>
                    <td><?= $vehicle->quantity ?></td>
                    <?php if (in_array('km', $transport_fields)){ ?>
                    <td><?= $vehicle->duration ?></td>
                    <?php } ?>
                    <?php if (in_array('price_group', $transport_fields)){ ?>
                        <td></td>
                    <?php } ?>
                    <?php if (in_array('price', $transport_fields)){ ?>
                    <td><?= $vehicle->price ?></td>
                    <?php } ?>
                    <td colspan="<?=$transport_fields_count?>"></td>
                    <td><?= $formatter->asCurrency($vehicle->price * $vehicle->quantity * $vehicle->duration * (1 - $vehicle->discount / 100), $currency); ?></td>
                </tr>
                <?php } }?>
                <?php
            }
                }
            ?>
            <?php if ($prices){ ?>
            <tr class="warning">
                <td colspan="8"><b><?= Yii::t('app', 'Łącznie Transport') ?></b></td>
                <td><?=$formatter->asCurrency($transport_summ, $currency)?></td>
            </tr>
            <?php } ?>
            <?php } ?>
            <!----------|END TRANSPORT|---------->

            <!----------|START ROLES|---------->
                            <?php 
                            $skills_summ = 0;
                            $crew_fields_count = 8 - count($crew_fields);
            if (($offerForm->roleModels)||($model->getExtraItem(OfferExtraItem::TYPE_CREW))){
                    if (isset($settings['crewColor']))
                        {
                            $style= "style='background-color:".$settings['crewColor']->value.";";
                        }else{
                            $style = "style='";
                        } 
                        if (isset($settings['crewFontColor']))
                        {
                        if ($settings['crewFontColor']!="")
                                    $style.= "color:".$settings['crewFontColor']->value.";";
                        }
                $style .= "'"; ?>?>
            <tr>
                <td colspan="9" <?=$style?>><b><?= Yii::t('app', 'Obsługa techniczna') ?></b></td>
            </tr>
            <?php if ($model->offerDraft->crew_section==1){ ?>
            <tr>
            <?php if (in_array('name', $crew_fields)){ ?>
                <th><?= Yii::t('app', 'Nazwa') ?></th>
            <?php } ?>
            <?php if (in_array('description', $crew_fields)){ ?>
                <th><?= Yii::t('app', 'Opis') ?></th>
            <?php } ?>
            <?php if (in_array('price', $crew_fields)){ ?>
                <th><?= Yii::t('app', 'Cena') ?></th>
            <?php } ?>
                <th style="text-align: center;"><?= Yii::t('app', 'Liczba') ?></th>
            
            <?php if (in_array('days', $crew_fields)){ ?>
                <th style="text-align: center;"><?= Yii::t('app', 'Okres') ?></th>
            <?php } ?>
            <?php if (in_array('price_group', $crew_fields)){ ?>
                <th style="text-align: center;"><?= Yii::t('app', 'Stawka') ?></th>
            <?php } ?>
            <td colspan="<?=$crew_fields_count?>"></td>
            <?php if (in_array('total_price', $crew_fields)){ ?>
                <th><?= Yii::t('app', 'Razem netto') ?></th>
            <?php } ?>
                
            </tr>
            <?php } ?>
            <?php
            
            $time_type = "";
            $skills_summ = 0;
            foreach ($model->offerSchedules as $schedule)
            {
                $time_type = $schedule->id;
                if ($schedule->translate)
                    $s_name = $schedule->translate;
                else
                    $s_name = $schedule->name;
                $title = "<tr><td colspan='9' style='text-align:center; font-weight:bold; background-color:#fdfdfd;'>".$s_name."</td></tr>";
                $title_shown = false;
                            

            foreach ($offerForm->roleModels as $id => $rm):
                if ($rm->time_type==$time_type){
                /* @var $rm \common\models\OfferRole; */
                $skills_summ += $rm->getValue();
                $role = $rm->role;

                $baseIndex = 'roleModels['.$id.']';
                            if ($model->offerDraft->crew_section==1){
                            if (!$title_shown)
                            {
                                $title_shown = true;
                                echo $title;
                            } ?>
                            
                        <tr>
                            <?php if (in_array('name', $crew_fields)){ ?>
                                <td><?=\common\models\UserEventRole::getTranslateName($role->id, $model->language, $role->name) ?></td>
                                <?php } ?>
                            <?php if (in_array('description', $crew_fields)){ ?>
                                <td><?= $rm['description'] ?></td>
                            <?php } ?>
                            <?php if (in_array('price', $crew_fields)){ ?>
                                <td><?= $formatter->asCurrency($rm->price, $currency); ?></td>
                            <?php } ?>
                                <td style="text-align: center;"><?= $rm['quantity'] ?></td>
                                
                            <?php if (in_array('days', $crew_fields)){ ?>
                                <td style="text-align: center;"><?= $rm['duration']." ".$rm['unit'] ?>
                            </td>
                            <?php } ?>
                            <?php if (in_array('price_group', $crew_fields)){ ?>
                                <td style="text-align: center;"><?php if ($rm['role_price_id']){ echo \common\models\RolePrice::getList()[$rm['role_price_id']];}else{ echo "-"; } ?></td>
                                 <?php } ?>
                            <td colspan="<?=$crew_fields_count?>"></td>
                            <?php if (in_array('total_price', $crew_fields)){ ?>
                                <td><?= $formatter->asCurrency($rm->getValue(), $currency); ?></td>
                            <?php } ?>
                        
                            </tr>
                            <?php } } endforeach;  ?>
                            <?php
                                foreach ($model->getExtraItem(OfferExtraItem::TYPE_CREW) as $crew) {
                                    if ($crew->time_type==$time_type){
                                    $skills_summ += $crew->price * $crew->quantity * $crew->duration * (1 - $crew->discount / 100); ?>
                                    <?php if ($model->offerDraft->crew_section==1){ 
                                        if (!$title_shown)
                                        {
                                            $title_shown = true;
                                            echo $title;
                                        } ?>
                                    <tr>
                                    <?php if (in_array('name', $crew_fields)){ ?>
                                        <td><?= $crew->name ?></td>
                                    <?php } ?>
                                    <?php if (in_array('description', $crew_fields)){ ?>
                                        <td><?= $crew->description ?></td>
                                    <?php } ?>
                                    <?php if (in_array('price', $crew_fields)){ ?>
                                        <td><?= $formatter->asCurrency($crew->price, $currency) ?></td>
                                    <?php } ?>
                                        <td style="text-align: center;"><?= $crew->quantity ?></td>
                                        
                                    <?php if (in_array('days', $crew_fields)){ ?>
                                        <td style="text-align: center;"><?= $crew->duration ?></td>
                                    <?php } ?>
                                    <?php if (in_array('price_group', $crew_fields)){ ?>
                                <td style="text-align: center;"></td>
                                 <?php } ?>
                                        <td colspan="<?=$crew_fields_count?>"></td>
                                    <?php if (in_array('total_price', $crew_fields)){ ?>
                                        <td><?= $formatter->asCurrency($crew->price * $crew->quantity * $crew->duration * (1 - $crew->discount / 100), $currency); ?></td>
                                    <?php } ?>
                                    </tr>
                                    <?php } ?>
                                <?php
                                
                                }
                                } ?>
                <?php        }
                ?>
                

           <?php if ($prices){ ?>
            <tr class="warning">
                <td colspan="8"><b><?= Yii::t('app', 'Łącznie Obsługa techniczna') ?></b></td>
                <td><?php echo $formatter->asCurrency($skills_summ, $currency)?></td>
            </tr>
            <?php } ?>
            <?php } ?>
            <!----------|END ROLES|---------->
                <!----------|START CUSTOM FIELDS|---------->
            <?php
                $show_discount = false;
                $custom_summ = 0;
                $other_fields_count = 6 - count($other_fields);
            if ($model->offerCustomItems) {
                foreach ($model->offerCustomItems as $key => $custom_field) {
                    if ($custom_field->discount) {
                        $show_discount = true;
                    }
                }
            ?>
                            <?php if (isset($settings['otherColor']))
                        {
                            $style= "style='background-color:".$settings['otherColor']->value.";";
                        }else{
                            $style = "style='";
                        }
                        if (isset($settings['otherFontColor']))
                        {
                        if ($settings['otherFontColor']!="")
                                    $style.= "color:".$settings['otherFontColor']->value.";";
                        }
                $style .= "'"; ?> ?>
                    <tr>
                        <td colspan="7" <?=$style?>><b><?= Yii::t('app', 'Inne') ?></b></td>
                    </tr>
                    <?php if ($model->offerDraft->other_section==1){ ?>
                    <tr>
                <?php if (in_array('name', $other_fields)){ ?>
                        <th><?= Yii::t('app', 'Nazwa') ?></th>
                        <?php } ?>
                <?php if (in_array('price', $other_fields)){ ?>
                        <th><?= Yii::t('app', 'Cena') ?></th>
                        <?php } ?>
                        <th style="text-align: center;"><?= Yii::t('app', 'Liczba') ?></th>
                <?php if (in_array('discount', $other_fields)){ ?>
                        <th style="text-align: center;"><?php if ($show_discount) echo Yii::t('app', "Rabat %"); ?></th>
                        <?php } ?>
                        <th colspan="<?=$other_fields_count?>"></th>
                
                <?php if (in_array('total_price', $other_fields)){ ?>
                        <th><?= Yii::t('app', 'Razem netto') ?></th>
                <?php } ?>
                    </tr>
                    <?php } ?>
                    <?php
                    
                    foreach ($model->offerCustomItems as $key => $custom_field) { 
                        $custom_full_price = $custom_field->diff_count*$custom_field->quantity*($custom_field->price - ($custom_field->price*(int)$custom_field->discount/100));
                        ?>
                        <?php if ($model->offerDraft->other_section==1){ ?>
                        <tr>
                        <?php if (in_array('name', $other_fields)){ ?>
                            <td><?=$custom_field->name?></td>
                        <?php } ?>
                        <?php if (in_array('price', $other_fields)){ ?>
                            <td><?=$formatter->asCurrency($custom_field->price, $currency)?></td>
                        <?php } ?>
                            <td style="text-align: center;"><?=$custom_field->diff_count?></td>
                        <?php if (in_array('discount', $other_fields)){ ?>
                            <td style="text-align: center;"><?php if ($show_discount) { echo $custom_field->discount; } ?></td>
                        <?php } ?>
                            <td colspan="<?=$other_fields_count?>"></td>
                        <?php if (in_array('total_price', $other_fields)){ ?>
                            <td><?=$formatter->asCurrency($custom_full_price, $currency)?></td>
                        <?php } ?>
                        </tr>
                        <?php } ?>
                    <?php $custom_summ += $custom_full_price; } ?>
                    <?php if ($prices){ ?>
                    <tr class="warning">
                        <td colspan="6"><b><?= Yii::t('app', 'Łącznie inne') ?></b></td>
                        <td><?=$formatter->asCurrency($custom_summ, $currency)?></td>
                    </tr>
                    <?php } ?>
                    <?php } ?>
                <!----------|END CUSTOM FIELDS|---------->    
                <!----------|TOTAL|---------->
                <?php if ($prices){ ?>
                <?php $total = $total_summ_of_cats+$transport_summ+$skills_summ+$custom_summ; ?>
                <tr class="success">
                    <td colspan="8"><b><?= Yii::t('app', 'Podsumowanie') ?></b></td>
                    <td><?=$formatter->asCurrency($total, $currency)?></td>
                </tr>
                <?php } ?>
                <!----------|END TOTAL|---------->
            </tbody>
        </table>
            </div>
        <?php if ($prices){ ?>
        <div class="hb fl">
            <p><b><?= Yii::t('app', 'Podsumowanie kosztów') ?>: <?=$model->name;?></b></p>
            <table class="table table-row-border">
                <tr>
                    <td>
                        <?= Yii::t('app', 'Koszt sprzętu') ?>:
                    </td>
                    <td class="text-right">
                        <?=$formatter->asCurrency($total_summ_of_cats, $currency)?>
                    </td>
                </tr>
                <?php if ($transport_summ>0) { ?>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Koszt transportu') ?>:
                    </td>
                    <td class="text-right">
                        <?=$formatter->asCurrency($transport_summ, $currency)?>
                    </td>
                </tr>
                <?php } ?>
                <?php if ($skills_summ>0) { ?>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Koszt obsługi') ?>:
                    </td>
                    <td class="text-right">
                        <?= $formatter->asCurrency($skills_summ, $currency) ?>
                    </td>
                </tr>
                <?php } ?>
                <?php if ($custom_summ>0) { ?>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Inne koszty') ?>:
                    </td>
                    <td class="text-right">
                        <?= $formatter->asCurrency($custom_summ, $currency) ?>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <td>
                        <b><?= Yii::t('app', 'Wartość netto po rabacie') ?>:</b>
                    </td>
                    <td class="text-right">
                        <?=$formatter->asCurrency($total, $currency)?>
                    </td>
                </tr>
                <?php if ((isset($model->budget))&&($total>$model->budget)) { ?>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Dodatkowy rabat') ?>:
                    </td>
                    <td class="text-right">
                        <?=$formatter->asCurrency($total-$model->budget, $currency)." (".round(($total-$model->budget)/$total*100)."%)"?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b><?= Yii::t('app', 'Wartość po dodatkowym rabacie') ?>:</b>
                    </td>
                    <td class="text-right">
                        <?=$formatter->asCurrency($model->budget, $currency)?>
                    </td>
                </tr>                
                <?php 
                $total = $model->budget;
                } ?>                
                <tr>
                    <td>
                        <?= Yii::t('app', 'Podatek VAT') ?>:
                    </td>
                    <td class="text-right">
                        <?php $vat = $model->getVatValue(); 
                            echo $formatter->asCurrency($vat, $currency); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b><?= Yii::t('app', 'Wartość brutto') ?>:</b>
                    </td>
                    <td class="text-right">
                        <?php $brutto = $total + $vat; 
                            echo $formatter->asCurrency($brutto, $currency); ?>
                    </td>
                </tr>
                <?php if (($model->pm_cost)||($model->pm_cost_percent)){ ?>
                    <tr>
                    <td>
                        <b><?= Yii::t('app', 'Zaliczka brutto') ?>:</b>
                    </td>
                    <td class="text-right">
                    <?php
                        if (($model->pm_cost)&&($model->pm_cost>0))
                        {
                             echo $formatter->asCurrency($model->pm_cost*1.23, $currency);
                        }else{
                            echo $formatter->asCurrency($model->pm_cost_percent*$brutto/100, $currency);
                        } ?>
                    </td>
                </tr>
                   <?php } ?>
                   <?php if (in_array('paying_date', $footerfields)){ ?>
            <?php if ($model->payment_days) { ?>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Termin płatności') ?>:
                    </td>
                    <td class="text-right">
                        <?=$model->payment_days." ".Yii::t("app", "dni")?>
                    </td>
                </tr>
            <?php } ?>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
        <div class="hb fl">
        <p><b><?= Yii::t('app', 'Uwagi do sprzętu') ?>:</b></p>
            <table class="table table-row-border">


                <?php if (in_array('gear_details', $footerfields)){ ?>

                <tr>
                    <td>
                        <?= Yii::t('app', 'Całkowita objętość') ?>:
                    </td>
                    <td>
                         <?php $summ_of_v_in_m_3 = $summ_of_v/1000000; echo round($model->getTotalVolumeAndWeight()['volume'], 2); ?> <?= Yii::t('app', 'm') ?><sup>3</sup>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Całkowita waga netto') ?>:
                    </td>
                    <td>
                        <?php  echo round($model->getTotalVolumeAndWeight()['weight'], 0); ?> <?= Yii::t('app', 'kg') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Całkowita moc') ?>:
                    </td>
                    <td>
                        <?php  echo round($model->getTotalVolumeAndWeight()['power'], 0); ?> <?= Yii::t('app', 'W') ?>
                    </td>
                </tr>

                <?php } ?>
                            <?php if ($model->payment_date) { ?>
                                                            <tr>
                    <td>&nbsp;</td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Platność w terminie') ?>:
                    </td>
                    <td>
                        <?= $model->payment_date ?>
                    </td>
                </tr>
            <?php } ?>
            </table>
        </div>
       
        <?php 
        if (in_array('info', $titlefields)){
            if ($model->comment!=""){
            if (Yii::$app->params['companyID']!="visualsupport")
            {
                echo "<p><strong>".Yii::t('app', 'Uwagi: ')."</strong>".$model->comment."</p>";
            }
        }
        }
        if ($prices)
        {
        if ($model->order_rules){?>
        <pagebreak />
        <h1><?=Yii::t('app', 'Warunki zamówienia')?></h1>
        <?=$model->order_rules?>
          <?php  } }?>
    </div>



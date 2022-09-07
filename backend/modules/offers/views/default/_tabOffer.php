<?php
use yii\bootstrap\Html;
use kartik\grid\GridView;
use kartik\widgets\ActiveForm;
use yii\web\View;
use backend\modules\offers\models\OfferExtraItem;
use common\models\OfferGear;
use common\models\OfferOuterGear;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
/* @var $model \common\models\Event; */
/* @var $this \yii\web\View */
$formatter = Yii::$app->formatter;
use yii\bootstrap\Modal;

$currency = $model->priceGroup->currency;


Modal::begin([
    'id' => 'favorite-modal',
    'header' => Yii::t('app', 'Ulubione'),
    'class' => 'modal',
    'size'=>'modal-lg',
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class='modalContent'></div>";
Modal::end();
$user = Yii::$app->user;
  $form = ActiveForm::begin([
            'id'=>'offer-form',
        ]);
?>
<?php 
$gearsPrices = \common\models\GearsPrice::find()->all();
$gps = [];
foreach ($gearsPrices as $gp)
{
    $gps[$gp->id] = $gp;
}

$countprice = false;
if (($model->budget>0)&&($model->value>$model->budget)&&($model->getGearValue()>0)) { 
$countprice = true;
$budget = ($model->budget-$model->value+$model->getGearValue())/$model->getGearValue();
}

?>
<div id="open-favorite">
<?= Html::a('<i class="fa fa fa-heart"></i>', ['/warehouse/favorites', 'id'=>$model->id], ['class'=>'open-favorite-list'])?>
</div>

<div id="toast-container" class="toast-top-right" aria-live="polite" role="alert" style="display:none"><div class="toast toast-error" style=""><div class="toast-message"><?= Html::a('<i class="fa fa fa-reload"></i>'.Yii::t('app', 'Oferta zmieniona kliknij, aby odświeżyć'), ['/offer/default/view', 'id'=>$model->id])?></div></div></div>
    <?php


        if ($user->can('menuOffersViewEdit')) {
    ?>
        <div class="form-group" data-spy="affix" style="z-index: 1000; right: 10px; top: 250px;">
            <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?><br/>
            <?= Html::a(Yii::t('app', 'Dodaj'), ['/offer/default/add-item', 'offer' => $model->id, 'id' => $model->id], ['class' =>  'btn btn-primary', 'id' => 'add-model']) ?>
        </div>
    <?php } ?>
<div class="panel-body">

        <table class="offer_table table gear">
            <tbody>
                <!----------|MAGAZYN|---------->
                <?php $total_summ_of_cats = 0; ?>
                <?php
                    $summ_of_one_cat = 0;
                    $summ_of_v = 0;
                    $summ_of_weight = 0;
                    $summ_of_power_consumption = 0;
                ?>
                <?php
                function cmp2($a, $b)
                {
                    return $a["position"] > $b["position"];
                }
                ?>
                <?php foreach ($offerForm->allGears as $categoryName => $items):
                

                usort($items, "cmp2");
                
                        $cat = \common\models\GearCategory::find()->where(['lvl'=>1])->andWhere(['name'=>$categoryName])->one();
                        $summ_of_one_cat = 0;
                        if ($cat->color)
                        {
                            $style= "style='background-color:".$cat->color.";'";
                        }else{
                            $style = "";
                        }

                    ?>

                    <tr class="cat-row">
                        <td colspan="12" class="newsystem-bg" <?=$style?>><b><u><?=$categoryName?></u></b><?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/warehouse/assign', 'id'=>$model->id, 'type'=>'offer', 'c'=>$cat->id], ['class'=>'btn btn-xs pull-left white-button']); ?></td>
                    </tr>
                    <tr class="cat-row">
                        <td colspan="3"><?= $form->field($offerForm, 'gearSettings['.$categoryName.'][first_day_percent]')->textInput([
                                'class'=>'change-children',
                                'data' => [
                                    'category'=>str_replace(" ", "-", $categoryName),
                                    'attribute'=>'first_day_percent',
                                ]
                            ])->label(Yii::t('app', '% dnia pierwszego')); ?></td>
                        <td colspan="3">
                            <?= $form->field($offerForm, 'gearSettings['.$categoryName.'][gears_price_id]')->dropDownList(\common\models\GearsPrice::getPricesNames($cat->id, $model->price_group_id), ['class'=>'change-children', 'data' => [
                                    'category'=>str_replace(" ", "-", $categoryName),
                                    'attribute'=>'gears_price_id',
                                ]])->label(Yii::t('app', 'Stawka')) ?>
                        </td>
                        <td colspan="3">
                            <?= $form->field($offerForm, 'gearSettings['.$categoryName.'][duration]')->textInput([
                                'class'=>'change-children',
                                'data' => [
                                    'category'=>str_replace(" ", "-", $categoryName),
                                    'attribute'=>'duration',
                                ]
                            ])->label(Yii::t('app', 'Liczba dni pracy sprzętu')); ?>
                        </td>
                        <td colspan="3">
                            <?= $form->field($offerForm, 'gearSettings['.$categoryName.'][discount]', [
                                'addon' => ['append' => ['content'=>'%']],

                            ])->textInput([
                                'class'=>'change-children',
                                'data' => [
                                    'category'=>str_replace(" ", "-", $categoryName),
                                    'attribute'=>'discount',
                                ]
                            ])->label(Yii::t('app', 'Rabat')); ?>
                        </td>
                    </tr>
                    <tr class="cat-row">
                    <th style="width:100px"></th>
                        <th><?= Yii::t('app', 'Nazwa') ?></th>
                        <th><?= Yii::t('app', 'Opis') ?></th>
                        <th style="width:100px"><?= Yii::t('app', 'Stawka') ?></th>
                        <th style="width:150px"><?= Yii::t('app', '% dnia pierwszego') ?></th>
                        <th style="width:100px"><?= Yii::t('app', 'Cena') ?></th>
                        <th style="width:100px"><?= Yii::t('app', 'Liczba') ?></th>
                        <th style="width:100px"><?= Yii::t('app', 'Rabat') ?></th>
                        
                        <?php if (Yii::$app->params['companyID']!="wizja")
                        { ?>
                            <th style="width:100px"><?= Yii::t('app', 'Dni pracy') ?></th>
                        <?php }else{ ?>
                            <th style="width:100px"><?= Yii::t('app', 'Przelicznik') ?></th>
                        <?php } ?>
                        <th style="width:100px"><?= Yii::t('app', 'VAT %') ?></th>
                        <th><?= Yii::t('app', 'Razem netto') ?></th>
                        <th style="width:60px"></th>
                    </tr>
                    <?php foreach ($items as  $key => $data):
                            if ($data['type'] != 'extraGear') {
                                $gearId = $data["id"];
                                $baseIndex = $data['type'] . 'Models[' . $gearId . ']';
                                 ?>
                                <tr style="background-color: #fafafa;" data-type="<?=$data['type']?>" data-id="<?=$gearId?>" data-position="<?=$data['position']?>" data-cat ='<?=str_replace(" ", "-", $categoryName)?>' class="parent-row">
                                <td><?php if ($user->can('menuOffersViewEdit')) {echo Html::a(Html::icon('arrow-up'),'#', ['class' => 'sort-up-button']); echo Html::a(Html::icon('arrow-down'),'#', ['class' => 'sort-down-button']); echo Html::a(Html::icon('plus'),['/warehouse/assign', 'id'=>$model->id, 'type'=>'offer', 'c'=>$cat->id,'item' => $gearId, 'type2'=>$data['type']], ['class' => 'btn-xs btn ']);}?><?php echo Html::a('<i class="fa fa-caret-down"></i>','#', ['class' => 'btn-xs btn pull-right toggle-gear', 'data-toggle'=>$data['type'].'-'.$data['id']]);
                                if ($data["visible"])
                                                $class ="visible";
                                            else
                                                $class="in-visible";
                                            echo Html::a('<i class="fa fa-eye"></i>', ['/offer/default/visible-item',
                                                'offerId' => $model->id,
                                                'itemId' => $gearId, 'type'=>$data['type']], ['class' => 'btn btn-xs visible-item '.$class]);?></td>
                                    <td> <?= $data['name'] ?><?= $form->field($offerForm, $baseIndex . '[gear_id]')->hiddenInput()->label(false) ?></td>
                                    <td><?= $form->field($offerForm, $baseIndex . '[description]')->textInput()->label(false) ?></td>
                                    
                                    <?php if ($data['type'] == 'gear') { ?>
                                    <td><?= $form->field($offerForm, $baseIndex . '[gears_price_id]')->dropDownList(\common\models\Gear::getPricesNames($data["gear_id"], $model->price_group_id), ['class'=>'gears-price-dropdown child-to-change', 'data-gearid'=>$data["gear_id"],
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'gears_price_id']])->label(false) ?></td>
                                    <?php }else{ ?>
                                    <td><?= $form->field($offerForm, $baseIndex . '[gears_price_id]')->dropDownList(\common\models\Gear::getPricesNames(null, $model->price_group_id),['class' => 'child-to-change',
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'gears_price_id']])->label(false) ?></td>
                                    <?php } ?>
                                    <td><?php if ((isset($data['gears_price_id']))&&(isset($gps[$data['gears_price_id']]))) { echo $gps[$data['gears_price_id']]->getPercentes();}else{ echo $form->field($offerForm, $baseIndex . '[first_day_percent]')->textInput(['class'=>'first_day_percent child-to-change',
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'first_day_percent']])->label(false);}?></td>
                                    <td><?= $form->field($offerForm, $baseIndex . '[price]')->textInput(['class'=>'price-input'])->label(false) ?></td>
                                    <td><?= $form->field($offerForm, $baseIndex . '[quantity]')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($offerForm, $baseIndex . '[discount]')->textInput(['class' => 'child-to-change',
                                            'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'discount',]])->label(false); ?></td>
                                    <td><?php echo $form->field($offerForm, $baseIndex . '[duration]')->textInput(['class' => 'child-to-change',
                                            'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'duration',]])->label(false); ?></td>
                                    <td><?= $form->field($offerForm, $baseIndex . '[vat_rate]')->textInput()->label(false) ?></td>
                                    <td class="value"><?= $formatter->asCurrency($data['value'], $currency) ?> <?php if ($countprice) echo "<br/><small>".$formatter->asCurrency($data['value']*$budget, $currency)."</small>";  ?></td>
                                    <td><?php
                                        if ($user->can('menuOffersViewEdit')) {
                                            
                                            echo Html::a(Html::icon('trash'), ['/offer/default/remove-item',
                                                'offerId' => $model->id, 'itemType' => $data['type'],
                                                'itemId' => $gearId, 'id' => $model->id], ['class' => 'btn btn-danger btn-xs remove-item']);

                                        } ?>
                                    </td>
                                </tr>
                                <?php
                                if ($data['type'] == 'gear') {
                                    $offerGears = OfferGear::find()->where(['offer_gear_id'=>$data['id']])->andWhere(['offer_id'=>$model->id])->all();
                                    foreach ($offerGears as $og)
                                    { 
                                        $gearId = $og->id;
                                        $baseIndex = 'gear' . 'Models[' . $gearId . ']'; ?>
                                        
                                        <tr class="<?=$data['type'].'-'.$data['id'] ?>">
                                        <td><?php if ($og->visible)
                                                        $class ="visible";
                                                    else
                                                        $class="in-visible";
                                                    echo Html::a('<i class="fa fa-eye"></i>', ['/offer/default/visible-item',
                                                    'offerId' => $model->id,
                                                        'itemId' => $gearId, 'type'=>'gear'], ['class' => 'pull-right btn btn-xs visible-item '.$class]); ?></td>
                                            <td>- <?= $og->gear->name ?><?= $form->field($offerForm, $baseIndex . '[gear_id]')->hiddenInput()->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[description]')->textInput()->label(false) ?></td>

                                            <td><?= $form->field($offerForm, $baseIndex . '[gears_price_id]')->dropDownList(\common\models\Gear::getPricesNames($og->gear_id, $model->price_group_id), ['class'=>'gears-price-dropdown child-to-change', 'data-gearid'=>$og->gear_id,
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'gears_price_id']])->label(false) ?></td>
                                            <td><?php if (isset($og->gears_price_id)){ echo $gps[$og->gears_price_id]->getPercentes();}else{echo $form->field($offerForm, $baseIndex . '[first_day_percent]')->textInput(['class'=>'first_day_percent child-to-change',
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'first_day_percent']])->label(false);}?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[price]')->textInput(['class'=>'price-input'])->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[quantity]')->textInput()->label(false); ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[discount]')->textInput(['class' => 'child-to-change',
                                                    'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                        'attribute' => 'discount',]])->label(false); ?></td>
                                            <td><?php echo $form->field($offerForm, $baseIndex . '[duration]')->textInput(['class' => 'child-to-change',
                                                    'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                        'attribute' => 'duration',]])->label(false); ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[vat_rate]')->textInput()->label(false) ?></td>
                                            <td class="value"><?= $formatter->asCurrency($og->getValue(), $currency) ?> <?php if ($countprice) echo "<br/><small>".$formatter->asCurrency($og->getValue()*$budget, $currency)."</small>";  ?></td>
                                            <td><?php
                                                if ($user->can('menuOffersViewEdit')) {
                                                
                                                    echo Html::a(Html::icon('trash'), ['/offer/default/remove-item',
                                                        'offerId' => $model->id, 'itemType' => 'gear',
                                                        'itemId' => $gearId, 'id' => $model->id], ['class' => 'btn btn-danger btn-xs remove-item']);

                                                } ?>
                                            </td>
                                        </tr>
                                    <?php
                                $summ_of_one_cat += $og->getValue();
                                $summ_of_v += $og->gear->countVolume() * $og->quantity;
                                $summ_of_weight += $og->gear->weight * $og->quantity;
                                $summ_of_power_consumption += $og->gear->power_consumption * $og->quantity;
                                     }
                                    $offerGears = OfferOuterGear::find()->where(['offer_gear_id'=>$data['id']])->andWhere(['offer_id'=>$model->id])->all();
                                    foreach ($offerGears as $og)
                                    { 
                                        $gearId = $og->id;
                                        $baseIndex = 'outerGear' . 'Models[' . $gearId . ']'; ?>
                                        
                                        <tr class="<?=$data['type'].'-'.$data['id'] ?>">
                                            <td><?php if ($og->visible)
                                                        $class ="visible";
                                                    else
                                                        $class="in-visible";
                                                    echo Html::a('<i class="fa fa-eye"></i>', ['/offer/default/visible-item',
                                                    'offerId' => $model->id,
                                                        'itemId' => $gearId, 'type'=>'outerGear'], ['class' => 'pull-right btn btn-xs visible-item '.$class]); ?></td>
                                            <td>- <?= $og->outerGearModel->name ?><?= $form->field($offerForm, $baseIndex . '[outer_gear_model_id]')->hiddenInput()->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[description]')->textInput()->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[gears_price_id]')->dropDownList(\common\models\Gear::getPricesNames(null, $model->price_group_id),['class' => 'child-to-change',
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'gears_price_id']])->label(false) ?></td>
                                            <td><?php if (isset($og->gears_price_id)){ echo $gps[$og->gears_price_id]->getPercentes();}else{echo $form->field($offerForm, $baseIndex . '[first_day_percent]')->textInput(['class'=>'first_day_percent child-to-change',
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'first_day_percent']])->label(false);}?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[price]')->textInput(['class'=>'price-input'])->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[quantity]')->textInput()->label(false); ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[discount]')->textInput(['class' => 'child-to-change',
                                                    'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                        'attribute' => 'discount',]])->label(false); ?></td>
                                            <td><?php echo $form->field($offerForm, $baseIndex . '[duration]')->textInput(['class' => 'child-to-change',
                                                    'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                        'attribute' => 'duration',]])->label(false); ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[vat_rate]')->textInput()->label(false) ?></td>
                                            <td class="value"><?= $formatter->asCurrency($og->getValue(), $currency) ?> <?php if ($countprice) echo "<br/><small>".$formatter->asCurrency($og->getValue()*$budget, $currency)."</small>";  ?></td>
                                            <td><?php
                                                if ($user->can('menuOffersViewEdit')) {
                                                    echo Html::a(Html::icon('trash'), ['/offer/default/remove-item',
                                                        'offerId' => $model->id, 'itemType' => 'outerGear',
                                                        'itemId' => $gearId, 'id' => $model->id], ['class' => 'btn btn-danger btn-xs remove-item']);

                                                } ?>
                                            </td>
                                        </tr>
                                    <?php 
                                    $summ_of_one_cat += $og->getValue();
                                    $summ_of_v += $og->outerGearModel->countVolume() * $og->quantity;
                                    $summ_of_weight += $og->outerGearModel->weight * $og->quantity;
                                    $summ_of_power_consumption += $og->outerGearModel->power_consumption * $og->quantity;
                                }
                                }
                                if ($data['type'] == 'outerGear') {
                                    $offerGears = OfferGear::find()->where(['offer_outer_gear_id'=>$data['id']])->andWhere(['offer_id'=>$model->id])->all();
                                    foreach ($offerGears as $og)
                                    { 
                                        $gearId = $og->id;
                                        $baseIndex = 'gear' . 'Models[' . $gearId . ']'; ?>
                                        
                                        <tr class="<?=$data['type'].'-'.$data['id'] ?>">
                                        <td><?php if ($og->visible)
                                                        $class ="visible";
                                                    else
                                                        $class="in-visible";
                                                    echo Html::a('<i class="fa fa-eye"></i>', ['/offer/default/visible-item',
                                                    'offerId' => $model->id,
                                                        'itemId' => $gearId, 'type'=>'gear'], ['class' => 'pull-right btn btn-xs visible-item '.$class]); ?></td>
                                            <td>- <?= $og->gear->name ?><?= $form->field($offerForm, $baseIndex . '[gear_id]')->hiddenInput()->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[description]')->textInput()->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[gears_price_id]')->dropDownList(\common\models\Gear::getPricesNames($og->gear_id, $model->price_group_id), ['class'=>'gears-price-dropdown child-to-change', 'data-gearid'=>$og->gear_id,
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'gears_price_id']])->label(false) ?></td>
                                            <td><?php if (isset($og->gears_price_id)){ echo $gps[$og->gears_price_id]->getPercentes();}else{echo $form->field($offerForm, $baseIndex . '[first_day_percent]')->textInput(['class'=>'first_day_percent child-to-change',
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'first_day_percent']])->label(false);}?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[price]')->textInput(['class'=>'price-input'])->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[quantity]')->textInput()->label(false); ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[discount]')->textInput(['class' => 'child-to-change',
                                                    'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                        'attribute' => 'discount',]])->label(false); ?></td>
                                            <td><?php echo $form->field($offerForm, $baseIndex . '[duration]')->textInput(['class' => 'child-to-change',
                                                    'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                        'attribute' => 'duration',]])->label(false); ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[vat_rate]')->textInput()->label(false) ?></td>
                                            <td class="value"><?= $formatter->asCurrency($og->getValue(), $currency) ?> <?php if ($countprice) echo "<br/><small>".$formatter->asCurrency($og->getValue()*$budget, $currency)."</small>";  ?></td>
                                            <td><?php
                                                if ($user->can('menuOffersViewEdit')) {
                                                    echo Html::a(Html::icon('trash'), ['/offer/default/remove-item',
                                                        'offerId' => $model->id, 'itemType' => 'gear',
                                                        'itemId' => $gearId, 'id' => $model->id], ['class' => 'btn btn-danger btn-xs remove-item']);

                                                } ?>
                                            </td>
                                        </tr>
                                    <?php
                                $summ_of_one_cat += $og->getValue();
                                $summ_of_v += $og->gear->countVolume() * $og->quantity;
                                $summ_of_weight += $og->gear->weight * $og->quantity;
                                $summ_of_power_consumption += $og->gear->power_consumption * $og->quantity;
                                     }
                                    $offerGears = OfferOuterGear::find()->where(['offer_outer_gear_id'=>$data['id']])->andWhere(['offer_id'=>$model->id])->all();
                                    foreach ($offerGears as $og)
                                    { 
                                        $gearId = $og->id;
                                        $baseIndex = 'outerGear' . 'Models[' . $gearId . ']'; ?>
                                        
                                        <tr class="<?=$data['type'].'-'.$data['id'] ?>">
                                            <td><?php if ($og->visible)
                                                        $class ="visible";
                                                    else
                                                        $class="in-visible";
                                                    echo Html::a('<i class="fa fa-eye"></i>', ['/offer/default/visible-item',
                                                    'offerId' => $model->id,
                                                        'itemId' => $gearId, 'type'=>'outerGear'], ['class' => 'pull-right btn btn-xs visible-item '.$class]); ?></td>
                                            <td>- <?= $og->outerGearModel->name ?><?= $form->field($offerForm, $baseIndex . '[outer_gear_model_id]')->hiddenInput()->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[description]')->textInput()->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[gears_price_id]')->dropDownList(\common\models\Gear::getPricesNames(null, $model->price_group_id),['class' => 'child-to-change',
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'gears_price_id']])->label(false) ?></td>
                                                <td><?php if (isset($og->gears_price_id)){ echo $gps[$og->gears_price_id]->getPercentes();}else{echo $form->field($offerForm, $baseIndex . '[first_day_percent]')->textInput(['class'=>'first_day_percent child-to-change',
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'first_day_percent']])->label(false);}?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[price]')->textInput(['class'=>'price-input'])->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[quantity]')->textInput()->label(false); ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[discount]')->textInput(['class' => 'child-to-change',
                                                    'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                        'attribute' => 'discount',]])->label(false); ?></td>
                                            <td><?php echo $form->field($offerForm, $baseIndex . '[duration]')->textInput(['class' => 'child-to-change',
                                                    'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                        'attribute' => 'duration',]])->label(false); ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[vat_rate]')->textInput()->label(false) ?></td>
                                            <td class="value"><?= $formatter->asCurrency($og->getValue(), $currency) ?> <?php if ($countprice) echo "<br/><small>".$formatter->asCurrency($og->getValue()*$budget, $currency)."</small>";  ?></td>
                                            <td><?php
                                                if ($user->can('menuOffersViewEdit')) {

                                                    echo Html::a(Html::icon('trash'), ['/offer/default/remove-item',
                                                        'offerId' => $model->id, 'itemType' => 'outerGear',
                                                        'itemId' => $gearId, 'id' => $model->id], ['class' => 'btn btn-danger btn-xs remove-item']);

                                                } ?>
                                            </td>
                                        </tr>
                                    <?php
                                    $summ_of_one_cat += $og->getValue();
                                    $summ_of_v += $og->outerGearModel->countVolume() * $og->quantity;
                                    $summ_of_weight += $og->outerGearModel->weight * $og->quantity;
                                    $summ_of_power_consumption += $og->outerGearModel->power_consumption * $og->quantity;
                                     }
                                }
                                $summ_of_one_cat += $data['value'];
                                $summ_of_v += $data['volume'] * $data['quantity'];
                                $summ_of_weight += $data['weight'] * $data['quantity'];
                                $summ_of_power_consumption += $data['power_consumption'] * $data['quantity'];
                            }
                            else {
                                $extraItemModel = OfferExtraItem::findOne($data['id']); ?>
                                <tr style="background-color: #fafafa;" data-type="<?=$data['type']?>" data-id="<?=$data['id']?>" data-position="<?=$data['position']?>" class="parent-row"  data-cat ='<?=str_replace(" ", "-", $categoryName)?>'>
                                    <td><?php
                                        if ($user->can('menuOffersViewEdit')) {echo Html::a(Html::icon('arrow-up'),'#', ['class' => 'sort-up-button']); echo Html::a(Html::icon('arrow-down'),'#', ['class' => 'sort-down-button']);echo Html::a(Html::icon('plus'),['/warehouse/assign', 'id'=>$model->id, 'type'=>'offer', 'c'=>$cat->id,'item' => $data['id'], 'type2'=>$data['type']], ['class' => 'btn-xs btn']);}

                                         if ($data["visible"])
                                                $class ="visible";
                                            else
                                                $class="in-visible";
                                            echo Html::a('<i class="fa fa-eye"></i>', ['/offer/default/visible-item',
                                                'offerId' => $model->id,
                                                'itemId' => $data['id'], 'type'=>$data['type']], ['class' => 'btn btn-xs visible-item '.$class]);
                                        echo Html::a('<i class="fa fa-caret-down"></i>','#', ['class' => 'btn-xs btn pull-right toggle-gear', 'data-toggle'=>'extraGear-'.$data['id']]);
                                           ?></td>
                                    <td><?= $form->field($extraItemModel, '['.$data['id'].']name')->textInput()->label(false) ?></td>
                                    <td><?= $form->field($extraItemModel, '['.$data['id'].']description')->textInput()->label(false) ?></td>
                                    <td><?= $form->field($extraItemModel, '['.$data['id'].']gears_price_id')->dropDownList(\common\models\Gear::getPricesNames(null, $model->price_group_id),['class' => 'child-to-change',
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'gears_price_id']])->label(false) ?></td>
                                    <td><?php if ((isset($data['gears_price_id']))&&(isset($gps[$data['gears_price_id']]))){ echo $gps[$data['gears_price_id']]->getPercentes();}else{echo $form->field($extraItemModel, '['.$data['id'].']first_day_percent')->textInput(['class'=>'first_day_percent child-to-change',
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'first_day_percent']])->label(false);}?></td>
                                    <td><?= $form->field($extraItemModel, '['.$data['id'].']price')->textInput()->label(false) ?></td>
                                    <td><?= $form->field($extraItemModel, '['.$data['id'].']quantity')->textInput()->label(false) ?></td>
                                    <td><?= $form->field($extraItemModel, '['.$data['id'].']discount')->textInput(['class' => 'child-to-change',
                                                    'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                        'attribute' => 'discount',]])->label(false) ?></td>
                                    <td><?= $form->field($extraItemModel, '['.$data['id'].']duration')->textInput(['class' => 'child-to-change',
                                                    'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                        'attribute' => 'duration',]])->label(false) ?></td>

                                    <td><?= $form->field($extraItemModel, '['.$data['id'].']vat_rate')->textInput()->label(false) ?></td>
                                    <td><?= $formatter->asCurrency($data['value'], $currency) ?> <?php if ($countprice) echo "<br/><small>".$formatter->asCurrency($data['value']*$budget, $currency)."</small>";  ?></td>
                                    <td>
                                        <?php
                                        if ($user->can('menuOffersViewEdit')) {
                                            echo Html::a(Html::icon('pencil'), ['/offer/default/edit-extra-item', 'id' => $model->id, 'item' => $data['id']], ['class' => 'btn-xs btn btn-success extra_edit']);
                                            echo Html::a(Html::icon('trash'), ['/offer/default/delete-extra-item', 'id' => $model->id, 'item' => $data['id']], ['class' => 'btn-xs btn btn-danger extra_delete']);
                                        } ?>
                                    </td>
                                </tr><?php
                                    $offerGears = OfferGear::find()->where(['offer_group_id'=>$data['id']])->andWhere(['offer_id'=>$model->id])->all();
                                    foreach ($offerGears as $og)
                                    { 
                                        $gearId = $og->id;
                                        $baseIndex = 'gear' . 'Models[' . $gearId . ']'; ?>
                                        
                                        <tr class="extraGear-<?=$data['id']?>">
                                        <td><?php if ($og->visible)
                                                        $class ="visible";
                                                    else
                                                        $class="in-visible";
                                                    echo Html::a('<i class="fa fa-eye"></i>', ['/offer/default/visible-item',
                                                    'offerId' => $model->id,
                                                        'itemId' => $gearId, 'type'=>'gear'], ['class' => 'pull-right btn btn-xs visible-item '.$class]); ?></td>
                                            <td>- <?= $og->gear->name ?><?= $form->field($offerForm, $baseIndex . '[gear_id]')->hiddenInput()->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[description]')->textInput()->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[gears_price_id]')->dropDownList(\common\models\Gear::getPricesNames($og->gear_id, $model->price_group_id), ['class'=>'gears-price-dropdown child-to-change', 'data-gearid'=>$og->gear_id,
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'gears_price_id']])->label(false) ?></td>
                                                <td><?php if (isset($og->gears_price_id)){ echo $gps[$og->gears_price_id]->getPercentes();}else{echo $form->field($offerForm, $baseIndex . '[first_day_percent]')->textInput(['class'=>'first_day_percent child-to-change',
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'first_day_percent']])->label(false);}?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[price]')->textInput(['class'=>'price-input'])->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[quantity]')->textInput()->label(false); ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[discount]')->textInput(['class' => 'child-to-change',
                                                    'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                        'attribute' => 'discount',]])->label(false); ?></td>
                                            <td><?php echo $form->field($offerForm, $baseIndex . '[duration]')->textInput(['class' => 'child-to-change',
                                                    'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                        'attribute' => 'duration',]])->label(false); ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[vat_rate]')->textInput()->label(false) ?></td>
                                            <td class="value"><?= $formatter->asCurrency($og->getValue(), $currency) ?> <?php if ($countprice) echo "<br/><small>".$formatter->asCurrency($og->getValue()*$budget, $currency)."</small>";  ?></td>
                                            <td><?php
                                                if ($user->can('menuOffersViewEdit')) {

                                                    echo Html::a(Html::icon('trash'), ['/offer/default/remove-item',
                                                        'offerId' => $model->id, 'itemType' => 'gear',
                                                        'itemId' => $gearId, 'id' => $model->id], ['class' => 'btn btn-danger btn-xs remove-item']);

                                                } ?>
                                            </td>
                                        </tr>
                                    <?php
                                $summ_of_one_cat += $og->getValue();
                                $summ_of_v += $og->gear->countVolume() * $og->quantity;
                                $summ_of_weight += $og->gear->weight * $og->quantity;
                                $summ_of_power_consumption += $og->gear->power_consumption * $og->quantity;
                                     }
                                    $offerGears = OfferOuterGear::find()->where(['offer_group_id'=>$data['id']])->andWhere(['offer_id'=>$model->id])->all();
                                    foreach ($offerGears as $og)
                                    { 
                                        $gearId = $og->id;
                                        $baseIndex = 'outerGear' . 'Models[' . $gearId . ']'; ?>
                                        
                                        <tr class="extraGear-<?=$data['id']?>">
                                            <td><?php if ($og->visible)
                                                        $class ="visible";
                                                    else
                                                        $class="in-visible";
                                                    echo Html::a('<i class="fa fa-eye"></i>', ['/offer/default/visible-item',
                                                    'offerId' => $model->id,
                                                        'itemId' => $gearId, 'type'=>'gear'], ['class' => 'pull-right btn btn-xs visible-item '.$class]); ?></td>
                                            <td>- <?= $og->outerGearModel->name ?><?= $form->field($offerForm, $baseIndex . '[outer_gear_model_id]')->hiddenInput()->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[description]')->textInput()->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[gears_price_id]')->dropDownList(\common\models\Gear::getPricesNames(null, $model->price_group_id),['class' => 'child-to-change',
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'gears_price_id']])->label(false) ?></td>
                                            <td><?php if (isset($og->gears_price_id)){ echo $gps[$og->gears_price_id]->getPercentes();}else{echo $form->field($offerForm, $baseIndex . '[first_day_percent]')->textInput(['class'=>'first_day_percent child-to-change',
                                        'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'first_day_percent']])->label(false);}?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[price]')->textInput(['class'=>'price-input'])->label(false) ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[quantity]')->textInput()->label(false); ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[discount]')->textInput(['class' => 'child-to-change',
                                                    'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                        'attribute' => 'discount',]])->label(false); ?></td>
                                            <td><?php echo $form->field($offerForm, $baseIndex . '[duration]')->textInput(['class' => 'child-to-change',
                                                    'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                        'attribute' => 'duration',]])->label(false); ?></td>
                                            <td><?= $form->field($offerForm, $baseIndex . '[vat_rate]')->textInput()->label(false) ?></td>
                                            <td class="value"><?= $formatter->asCurrency($og->getValue(), $currency) ?> <?php if ($countprice) echo "<br/><small>".$formatter->asCurrency($og->getValue()*$budget, $currency)."</small>";  ?></td>
                                            <td><?php
                                                if ($user->can('menuOffersViewEdit')) {

                                                    echo Html::a(Html::icon('trash'), ['/offer/default/remove-item',
                                                        'offerId' => $model->id, 'itemType' => 'outerGear',
                                                        'itemId' => $gearId, 'id' => $model->id], ['class' => 'btn btn-danger btn-xs remove-item']);

                                                } ?>
                                            </td>
                                        </tr>
                                    <?php
                                    $summ_of_one_cat += $og->getValue();
                                    $summ_of_v += $og->outerGearModel->countVolume() * $og->quantity;
                                    $summ_of_weight += $og->outerGearModel->weight * $og->quantity;
                                    $summ_of_power_consumption += $og->outerGearModel->power_consumption * $og->quantity;
                                     }
                                $summ_of_one_cat += $data['value'];
                            }
                         ?>
                    <?php endforeach; ?>
                    <tr class="cat-row"><td colspan="12">
                    <?php
                        if ($user->can('menuOffersEdit')) {
                            //echo Html::a(Yii::t('app', 'Dodaj pozycję'), ['/warehouse/assign', 'id' => $model->id, 'type' => 'offer'], ['class' => 'btn btn-success btn-sm']);
                        } ?>
                    </td></tr>
                    <tr class="warning cat-row">
                        <td colspan="10"><b><u><?= Yii::t('app', 'Łącznie') ?> <?=$categoryName?></u></b></td>
                        <td><?=$formatter->asCurrency($summ_of_one_cat, $currency)?></td><td></td>
                    </tr>
                    <tr><td colspan="12 cat-row"></td></tr>
                    <?php $total_summ_of_cats += $summ_of_one_cat; ?>
                <?php endforeach; ?>
                <!----------|END MAGAZYN|---------->
        </tbody>
        </table>
        <table class="offer_table table">
        <tbody>
                <!----------|TRANSPORT|---------->

                   
                <!----------|END TRANSPORT|---------->
<?php if (isset($settings['transportColor']))
                        {
                            $style= "style='background-color:".$settings['transportColor']->value.";'";
                        }else{
                            $style = "";
                        } ?>
                    <tr>
                        <td colspan="9" class="newsystem-bg" <?=$style?>><b><u><?= Yii::t('app', 'Transport') ?></u></b><?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Zarządzaj'), ['/offer/default/assign-vehicle', 'id'=>$model->id], ['class'=>'btn btn-xs pull-left white-button']); ?></td>
                </tr>
                <tr>
                        <td colspan="4"><b><u><?= Yii::t('app', 'Zapotrzebowanie transport osób:') ?></u></b><br/>
                        <?php 
                        $labels = [1=>Yii::t('app', 'Pakowanie'), 2=>Yii::t('app', 'Montaż'), 3=>Yii::t('app', 'Event'), 4=>Yii::t('app', 'Demontaż')];
                        for ($i=1; $i<5; $i++){
                            $count = $model->getWorkersCount($i);
                            echo $labels[$i].": ".$count.Yii::t('app', 'os.')."<br/>";

                             }?>
                        </td>
                        <td colspan="4"><b><u><?= Yii::t('app', 'Zapotrzebowanie transport sprzętu:') ?></u></b><br/>
                        <?php $summ_of_v_in_m_3 = $summ_of_v/1000000; echo Yii::t('app', 'Objętość: ').round($model->getTotalVolumeAndWeight()['volume'], 2); ?> <?= Yii::t('app', 'm') ?><sup>3</sup> 
                        <?= Yii::t('app', 'Waga netto: ').$model->getTotalVolumeAndWeight()['weight']; ?> <?= Yii::t('app', 'kg') ?>

                        </td>
                    </tr>
                <tr>
                    <th><?= Yii::t('app', 'Nazwa') ?></th>
                    <th><?= Yii::t('app', 'Opis') ?></th>
                    <th style="width:100px"><?= Yii::t('app', 'Cena') ?></th>
                    <th style="width:100px"><?= Yii::t('app', 'Liczba') ?></th>
                    <th style="width:100px"><?= Yii::t('app', 'Przelicznik') ?></th>
                    <th style="width:130px"><?= Yii::t('app', 'Stawka') ?></th>
                    <th style="width:130px"><?= Yii::t('app', 'Stawka VAT') ?></th>
                    <th><?= Yii::t('app', 'Razem netto') ?></th>
                    <th></th>
                </tr>
                <?php
                $time_type = "";
                $transport_summ= 0;
                    foreach ($model->offerSchedules as $schedule)
                    {
                        $time_type = $schedule->id;
                        $title = "<tr><td colspan='8' style='text-align:center; font-weight:bold; background-color:#eee;'>".$schedule->name." ".$schedule->getPeriodTime()."h</td></tr>";
                        $title_shown = false;

                        foreach ($model->getVehicleData() as $key => $vehicles): ?>
                        <?php foreach ($vehicles as $id=>$vehicle): ?>
                        <?php $baseIndex = 'vehicleModels['.$id.']'; ?>
                        <?php 
                            if ($vehicle['type']==$time_type){
                        /* @var $rm \common\models\OfferRole; */
                        $transport_summ += $vehicle['value'];
                        if (!$title_shown)
                        {
                            $title_shown = true;
                            echo $title;
                        }
                        ?>

                    <tr>
                        <td><?php echo $vehicle['name']; ?></td>
                        <td><?php echo  $form->field($offerForm, $baseIndex.'[description]')->textInput()->label(false); ?></td>
                        <td><?php echo  $form->field($offerForm, $baseIndex.'[price]')->textInput(['class'=>'price-input'])->label(false); ?></td>
                        <td><?php echo  $form->field($offerForm, $baseIndex.'[quantity]')->textInput()->label(false); ?></td>
                        <td><?php echo  $form->field($offerForm, $baseIndex.'[distance]')->textInput(['class'=>'duration-input'])->label(false); ?></td>
                        <td><?php echo $form->field($offerForm, $baseIndex.'[vehicle_price_id]')->dropDownList(\common\models\VehiclePrice::getList($vehicle['id'], $currency), ['class'=>'typeSalaryV', 'data-vehicle'=>$id])->label(false) ?></td>
                        <td><?php echo  $form->field($offerForm, $baseIndex.'[vat_rate]')->textInput()->label(false); ?></td>

                        <td><?php echo $formatter->asCurrency($vehicle['value'], $currency); ?></td>
                        <td><?php if ($user->can('menuOffersViewEdit')) {
    
                                    echo Html::a(Html::icon('trash'), ['/offer/default/delete-vehicle', 'id' => $model->id], ['class' => 'btn-xs btn btn-danger vehicle_delete', 'data-vehicleID' => $id]);
                                } ?>
                            </td>
                    </tr>

                <?php } endforeach; ?>
                    <?php
                        foreach ($model->getExtraItem(OfferExtraItem::TYPE_VEHICLE) as $vehicle) {
                            if ($vehicle['time_type']==$time_type){
                            $transport_summ += $vehicle->price * $vehicle->quantity * $vehicle->duration * (1 - $vehicle->discount / 100);
                            ?>

                            <tr>
                                <td><?= $form->field($vehicle, '['.$vehicle->id.']name')->textInput()->label(false) ?></td>
                                 <td><?= $form->field($vehicle, '['.$vehicle->id.']description')->textInput()->label(false) ?></td>
                                <td><?= $form->field($vehicle, '['.$vehicle->id.']price')->textInput()->label(false); ?></td>
                                <td><?= $form->field($vehicle, '['.$vehicle->id.']quantity')->textInput()->label(false) ?></td>
                                <td><?= $form->field($vehicle, '['.$vehicle->id.']duration')->textInput()->label(false); ?></td>
                                <td></td>
                                <td><?= $form->field($vehicle, '['.$vehicle->id.']vat_rate')->textInput()->label(false); ?></td>
                                <td><?= $formatter->asCurrency($vehicle->price * $vehicle->quantity * $vehicle->duration * (1 - $vehicle->discount / 100), $currency); ?></td>
                                <td>
                                    <?php
                                        if ($user->can('menuOffersViewEdit')) {
                                            echo Html::a(Html::icon('pencil'), ['/offer/default/edit-extra-item', 'id' => $model->id, 'item' => $vehicle->id], ['class' => 'btn-xs btn btn-success extra_edit']);
                                            echo Html::a(Html::icon('trash'), ['/offer/default/delete-extra-item', 'id' =>  $model->id, 'item' => $vehicle->id], ['class' => 'btn-xs btn btn-danger extra_delete']);
                                        } ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                     endforeach;
                    ?>
                    <?php } ?>
                    <tr class="warning">
                        <td colspan="7"><b><u><?= Yii::t('app', 'Łącznie Transport') ?></u></b></td>
                        <td><?=$formatter->asCurrency($transport_summ, $currency)?></td>
                        <td></td>
                    </tr>
                    <tr><td colspan="8"></td></tr>
            

                <!----------|START ROLES|---------->
                <?php if (isset($settings['crewColor']))
                        {
                            $style= "style='background-color:".$settings['crewColor']->value.";'";
                        }else{
                            $style = "";
                        } ?>
                    <tr>
                        <td colspan="9" class="newsystem-bg" <?=$style?>><b><u><?= Yii::t('app', 'Obsługa techniczna') ?></u></b><?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Zarządzaj'), ['/offer/role/assign', 'id'=>$model->id], ['class'=>'btn btn-xs pull-left white-button']); ?><div class="pull-right">    <?= Html::dropDownList('schema_id', null,
      ArrayHelper::map(\common\models\OfferRoleSchema::find()->where(['user_id'=>\Yii::$app->user->id])->all(), 'id', 'name'),['class'=>'form-control','style'=>'width:300px; display:inline-block; color:black;', 'id'=>'offer-role-schema-id']) ?>
    <?php echo Html::a(Yii::t('app', 'Załaduj szablon'), ['#'], ['class'=>'btn btn-success btn-sm', 'onclick'=>'loadSchema(); return false;']); ?></div></td>
                </tr>
                <tr>
                    <th><?= Yii::t('app', 'Nazwa') ?></th>
                    <th><?= Yii::t('app', 'Opis') ?></th>
                    <th style="width:100px"><?= Yii::t('app', 'Cena') ?></th>
                    <th style="width:100px"><?= Yii::t('app', 'Liczba') ?></th>
                    <?php if (Yii::$app->params['companyID']!="wizja")
                        { ?>
                            <th style="width:100px"><?= Yii::t('app', 'Okres') ?></th>
                        <?php }else{ ?>
                            <th style="width:100px"><?= Yii::t('app', 'Przelicznik') ?></th>
                        <?php } ?>
                    <th style="width:130px"><?= Yii::t('app', 'Stawka') ?></th>
                    <th style="width:130px"><?= Yii::t('app', 'Stawka VAT') ?></th>
                    <th colspan="2"><?= Yii::t('app', 'Razem netto') ?></th>
                </tr>
                <?php
                $time_type = "";
                $skills_summ = 0;
                    foreach ($model->offerSchedules as $schedule)
                    {
                        $time_type = $schedule->id;
                        $title = "<tr><td colspan='8' style='text-align:center; font-weight:bold; background-color:#eee;'>".$schedule->name." ".$schedule->getPeriodTime()."h</td></tr>";
                        $title_shown = false;

                        foreach ($offerForm->roleModels as $id => $rm):
                            if ($rm->time_type==$time_type){
                        /* @var $rm \common\models\OfferRole; */
                        $skills_summ += $rm->getValue();
                        $role = $rm->role;
                        $baseIndex = 'roleModels['.$id.']';
                        if (!$title_shown)
                        {
                            $title_shown = true;
                            echo $title;
                        }
                        ?>

                    <tr>
                        <td><?php echo $role->name; ?></td>
                        <td><?php echo  $form->field($offerForm, $baseIndex.'[description]')->textInput()->label(false); ?></td>
                        <td><?php echo  $form->field($offerForm, $baseIndex.'[price]')->textInput(['class'=>'price-input'])->label(false); ?></td>
                        <td><?php echo  $form->field($offerForm, $baseIndex.'[quantity]')->textInput()->label(false); ?></td>
                        <td><?php echo  $form->field($offerForm, $baseIndex.'[duration]')->textInput(['class'=>'duration-input'])->label(false); ?></td>
                        <td><?php echo $form->field($offerForm, $baseIndex.'[role_price_id]')->dropDownList(\common\models\RolePrice::getList($role->id, $currency), ['class'=>'typeSalary', 'data-role'=>$rm->id, 'data-time'=>$i])->label(false) ?></td>
                        <td><?php echo  $form->field($offerForm, $baseIndex.'[vat_rate]')->textInput()->label(false); ?></td>
                        <td><?php echo $formatter->asCurrency($rm->getValue(), $currency); ?></td>
                        <td>
                            <?php if ($user->can('menuOffersViewEdit')) {
                                echo Html::a(Html::icon('trash'), ['/offer/default/delete-role', 'id' => $rm->id], ['class'=>'btn-xs btn btn-danger role_delete','data-role'=>$role['id']]);
                            } ?>
                        </td>
                    </tr>

                <?php } endforeach;   

                            
                            foreach ($model->getExtraItem(OfferExtraItem::TYPE_CREW) as $crew) {
                                if ($crew->time_type==$time_type)
                                {
                                    $skills_summ += $crew->price * $crew->quantity * $crew->duration * (1 - $crew->discount / 100);
                                    if (!$title_shown)
                                    {
                                        $title_shown = true;
                                        echo $title;
                                    } ?>

                                <tr>
                                    <td><?= $form->field($crew, '['.$crew->id.']name')->textInput()->label(false) ?></td>
                                    <td><?= $form->field($crew, '['.$crew->id.']description')->textInput()->label(false) ?></td>
                                    <td><?= $form->field($crew, '['.$crew->id.']price')->textInput()->label(false) ?></td>
                                    <td><?= $form->field($crew, '['.$crew->id.']quantity')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($crew, '['.$crew->id.']duration')->textInput()->label(false); ?></td>
                                    <td></td>
                                    <td><?= $form->field($crew, '['.$crew->id.']vat_rate')->textInput()->label(false); ?></td>
                                    <td><?= $formatter->asCurrency($crew->price * $crew->quantity * $crew->duration * (1 - $crew->discount / 100), $currency); ?></td>
                                    <td>
                                        <?php if ($user->can('menuOffersViewEdit')) {
                                             echo Html::a(Html::icon('pencil'), ['/offer/default/edit-extra-item', 'id' => $model->id, 'item' => $crew->id], ['class' => 'btn-xs btn btn-success extra_edit']);
                                            echo Html::a(Html::icon('trash'), ['/offer/default/delete-extra-item', 'id'=>$model->id, 'item' => $crew->id], ['class' => 'btn-xs btn btn-danger extra_delete']);
                                        }
                                }
                                 ?>
                        </td>
                    </tr>
                    <?php
                     }  
                    }
                    ?>



                <tr class="warning">
                    <td colspan="7"><b><u><?= Yii::t('app', 'Łącznie Obsługa techniczna') ?></u></b></td>
                    <td><?php echo $formatter->asCurrency($skills_summ, $currency)?></td>
                    <td></td>
                </tr>
                <tr><td colspan="8"></td></tr>
                <!----------|END ROLES|---------->

                <!----------|START CUSTOM FIELDS|---------->
                <?php if (isset($settings['otherColor']))
                        {
                            $style= "style='background-color:".$settings['otherColor']->value.";'";
                        }else{
                            $style = "";
                        } ?>
                    <tr>
                        <td colspan="9" class="newsystem-bg" <?=$style?>><b><u><?= Yii::t('app', 'Inne') ?></u></b><?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/offer/default/offer-custom-items', 'id'=>$model->id], ['class'=>'btn btn-xs pull-left white-button']); ?></td>
                    </tr>
                    <tr>
                        <th></th>
                        <th><?= Yii::t('app', 'Nazwa') ?></th>
                        <th><?= Yii::t('app', 'Cena') ?></th>
                        <th><?= Yii::t('app', 'Liczba') ?></th>
                        <th><?= Yii::t('app', 'Rabat %') ?></th>
                        <td></td><td></td>
                        <th><?= Yii::t('app', 'Razem') ?></th>
                    </tr>
                    <?php 
                    $custom_summ = 0;
                    $currentDepartment = '';
                    foreach ($model->getCustomData(false) as $key => $custom) {
                        $custom_full_price = $custom['value'];
                        ?>
                        <?php
                            if ($currentDepartment != $custom['department'])
                            {
                                $currentDepartment = $custom['department'];
                                echo Html::tag('tr',Html::tag('td').Html::tag('th',$currentDepartment, ['colspan'=>6]));
                            }

                        ?>
                        <tr>
                            <td><?php // echo $custom['quantity']; ?></td>
                            <td><?php echo $custom['name']; ?></td>
                            <td><?=$formatter->asCurrency($custom['price'], $currency) ?></td>
                            <td><?php echo $custom['diff_count']; ?></td>
                            <td><?php echo $custom['discount']; ?></td>
                            <td></td><td></td>
                            <td><?=$formatter->asCurrency($custom_full_price, $currency)?></td>
                            <td><?php if ($user->can('menuOffersViewEdit')) {
                                    echo Html::a(Html::icon('trash'), ['/offer/default/delete-custom-field', 'id' => $model->id, 'offer_id' => $model->id], ['class' => 'btn-xs btn btn-danger custom_field_delete', 'data-custom' => $custom['id']]);
                                } ?></td>
                        </tr>
                    <?php $custom_summ += $custom_full_price; } ?>
                    <tr class="warning">
                        <td colspan="7"><b><u><?= Yii::t('app', 'Łącznie inne') ?></u></b></td>
                        <td><?= $formatter->asCurrency($custom_summ, $currency)?></td>
                        <td></td>
                    </tr>
                    <tr><td colspan="9"></td></tr>
                <!----------|END CUSTOM FIELDS|---------->    
                <!----------|TOTAL|---------->
                <?php $total = $total_summ_of_cats+$transport_summ+$skills_summ+$custom_summ; ?>
                <tr class="success">
                    <td colspan="7"><b><u><?= Yii::t('app', 'Podsumowanie') ?></u></b></td>
                    <td><?=$formatter->asCurrency($total, $currency)?></td>
                    <td></td>
                </tr>
                <!----------|END TOTAL|---------->
            </tbody>
        </table>
        <hr>
        <div class="col-sm-offset-6 col-sm-6">
            <div class="panel_mid_blocks">
                <div class="panel_block" style="margin-bottom: 0;">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Podsumowanie kosztów') ?>: <?=$model->name;?></h4>
                    </div>
                </div>
            </div>
            <div class="panel_mid_blocks">
                <div class="panel_block">
            <table class="table">
                <tr>
                    <td>
                        <?= Yii::t('app', 'Koszt sprzętu') ?>:
                    </td>
                    <td class="text-right">
                        <?=$formatter->asCurrency($total_summ_of_cats, $currency)?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Koszt transportu') ?>:
                    </td>
                    <td class="text-right">
                        <?=$formatter->asCurrency($transport_summ, $currency)?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Koszt obsługi') ?>:
                    </td>
                    <td class="text-right">
                        <?= $formatter->asCurrency($skills_summ, $currency) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Inne koszty') ?>:
                    </td>
                    <td class="text-right">
                        <?= $formatter->asCurrency($custom_summ, $currency) ?>
                    </td>
                </tr>
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
            </table>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="panel_mid_blocks">
                <div class="panel_block">
            <table class="table">
                <?php if ($model->payment_date) { ?>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Platność w terminie') ?>:
                    </td>
                    <td>

                    </td>
                </tr>
                <?php } ?>
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
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <b><?= Yii::t('app', 'Uwagi do sprzętu') ?>:</b>
                    </td>
                    <td></td>
                </tr>
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
                        <?= Yii::t('app', 'Całkowita waga') ?>:
                    </td>
                    <td>
                        <?= $model->getTotalVolumeAndWeight()['weight']?> <?= Yii::t('app', 'kg') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Całkowita moc') ?>:
                    </td>
                    <td>
                        <?=$summ_of_power_consumption; ?> <?= Yii::t('app', 'W') ?>
                    </td>
                </tr>
            </table>
            <?php echo $form->field($offerForm, 'comment')->widget(\common\widgets\RedactorField::className())->label(Yii::t('app', 'Uwagi')); ?>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>

<div class="row">
    <div class="col-md-12">
                        <div class="ibox">
                        <div class="ibox-title newsystem-bg">
                            <h5><?= Yii::t('app', 'Załączniki') ?></h5>
                        </div>
                        <div class="ibox-content">
        <?php
        echo \yii\grid\GridView::widget([
            'dataProvider'=>$settingAttachmentDataProvider,
            'layout'=>'{items}',
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'attribute' => 'filename',
                    'value'=>function($model)
                    {
                        return Html::a($model->filename, ['setting-attachment/download', 'id'=>$model->id]);
                    },
                    'format' => 'html',
                ],
            ],
        ]);
        ?>
        </div>
    </div>
    </div>
</div>
<!-- Modal -->
<div id="gearModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <form id="quantity_gear_form" action="">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><?= Yii::t('app', 'Nagłówek') ?></h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="quantity_inp"><?= Yii::t('app', 'Ilość') ?></label>
              <input type="number" class="form-control" name="quantity" min="1" id="quantity_inp" placeholder="<?= Yii::t('app', 'Ilość') ?>">
            </div>
            <div class="form-group">
              <label for="discount_inp"><?= Yii::t('app', 'Rabat na sprzęt, %') ?></label>
              <input type="number" class="form-control" name="discount" min="0" id="discount_inp" placeholder="<?= Yii::t('app', 'Rabat') ?>">
            </div>
          </div>
          <div class="modal-footer">
            <div id="form_result" class="pull-left"></div>
            <button type="submit" class="btn btn-success"><?= Yii::t('app', 'Zapisz') ?></button>
          </div>
        </div>
    </form>

  </div>
</div>


<div id="vehicleModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <form id="quantity_vehicle_form" action="">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><?= Yii::t('app', 'Nagłówek') ?></h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="quantity_inp"><?= Yii::t('app', 'Ilość') ?></label>
              <input type="number" class="form-control" name="quantity" min="1" id="quantity_inp" placeholder="<?= Yii::t('app', 'Ilość') ?>">
            </div>
          </div>
          <div class="modal-footer">
            <div id="form_result" class="pull-left"></div>
            <button type="submit" class="btn btn-success"><?= Yii::t('app', 'Zapisz') ?></button>
          </div>
        </div>
    </form>
</div>
</div>


<?php
$this->registerJs('
    $(".extra_edit").click(function(e){
        e.preventDefault();
        $("#new-model").modal("show").find(".modal-content").load($(this).attr("href"));
    });
    $(".extra_edit").on("contextmenu",function(){
       return false;
    }); 
');
$this->registerCss('
    .visible-item.in-visible{
            color:red;
    }');

$this->registerJs('
    $(".typeSalary").change(function(){
                role_id = $(this).data("role");
                group_id = $(this).val();
                var tr = $(this).parent().parent().parent();
                $.ajax({
                  type: "POST",
                  url: "'.Url::to(['role/save2', 'offer_id'=>$model->id]).'",
                  data: {id:role_id, group_id:group_id}, 
                  success: function(result){
                    tr.find(".price-input").val(result.price);
                  }
                }); 

    });
        $(".typeSalaryV").change(function(){
                vehicle_id = $(this).data("vehicle");
                time_type = $(this).data("time");
                group_id = $(this).val();
                var tr = $(this).parent().parent().parent();
                $.ajax({
                  type: "POST",
                  url: "'.Url::to(['default/save2', 'offer_id'=>$model->id]).'",
                  data: {vehicle_id:vehicle_id, group_id:group_id, time_type:time_type}, 
                  success: function(result){
                    tr.find(".price-input").val(result.price);
                  }
                }); 

    });

    $(".gears-price-dropdown").change(function(){
        gear_id = $(this).data("gearid")
        price_id = $(this).val();
        $(this).parent().parent().parent().find(".price-input").val(prices[price_id][gear_id]); 

    });

    $(".visible-item").click(function(e){
            var _this = $(this);
            e.preventDefault();
            var data = {
            }
            $(this).removeClass("visible");
            $(this).removeClass("in-visible");
            $.post(_this.attr("href"), data, function(response){
                if (response.visible)
                {
                        _this.addClass("visible");
                        var cl = "."+response.type+"-"+response.item;
                        $(cl+" .visible-item").removeClass("in-visible").addClass("visible");
                }else{
                        _this.addClass("in-visible");
                        var cl = "."+response.type+"-"+response.item;
                        $(cl+" .visible-item").addClass("in-visible").removeClass("visible");
                }
            });
        
    })
    $(".toggle-gear").click(function(e){

            e.preventDefault();
            $class = $(this).data("toggle");
            if ($(this).hasClass("is-hidden"))
            {
                $("."+$class).show();
                $(this).removeClass("is-hidden");
                $(this).find("i").addClass("fa-caret-down").removeClass("fa-caret-right");
            }else{
                $("."+$class).hide();
                $(this).addClass("is-hidden"); 
                $(this).find("i").removeClass("fa-caret-down").addClass("fa-caret-right");               
            }
    });

    jQuery(document).ready(function($){
        $(".gear_delete").on("click",function(){
            var _this = $(this);
            var data = {
                itemId : _this.data("gearid")
            }
            $.post(_this.attr("href"), data, function(response){
                _this.hide("slow", function(){ _this.closest("tr").remove(); });
            });
            return false;
        });

        $(".gear_edit").on("click",function(e){

            e.preventDefault();

            var _this = $(this);
            var data = {
                itemId : _this.data("gearid")
            };
            $.post(_this.attr("href"), data, function(response){
                console.log("sdfsdf");
                var form = $("#quantity_gear_form"),
                modal = $("#gearModal");
                form.attr("action",_this.attr("href")+"&update=1")
                form.find("#quantity_inp").val(response.quantity);
                form.find("#discount_inp").val(response.discount);
                form.data("gearid",_this.data("gearid"));
                modal.modal("show");
            }).fail(function(response) {
                console.log( response );
              });
            
        });

        $("#quantity_gear_form").validate({
            rules: {
                quantity: {
                    minlength: 1,
                    required: true,
                    number: true,
                    min: 1,
                },
                discount: {
                    number: true,
                    min: 0,
                    max: 100,
                }
            },
            highlight: function (element) {
                $(element).closest(".control-group").removeClass("success").addClass("error");
            },
            success: function (element) {
                element.text("").addClass("valid").closest(".control-group").removeClass("error").addClass("success");
            },
            submitHandler: function(form) {
                var _this = $(form),
                result = $("#form_result"),
                data = {
                    itemId : _this.data("gearid"),
                    quantity : _this.find("#quantity_inp").val(),
                    discount : _this.find("#discount_inp").val()
                };
                $.post(_this.attr("action"), data, function(response){
                    result.text(response.mess);
                    location.reload();
                });
                
                return false;
            }
        });


        $(".extra_delete").on("click",function(){
            $(this).hide("slow", function(){ $(this).closest("tr").remove(); });
            var data  = {};
            $.post($(this).attr("href"), data, function() {
                //location.reload();
                showReloadButton();
            });
            return false;
        });

        $(".vehicle_delete").on("click",function(){
            var _this = $(this);
            var data = {
                itemId : _this.data("vehicleid")
            }
            $.post(_this.attr("href"), data, function(response){
                _this.hide("slow", function(){ _this.closest("tr").remove(); });
                //location.reload();
                showReloadButton();
            });
            return false;
        });
        
        $(".role_delete").on("click",function(e){
            var _this = $(this);
            e.preventDefault();
            var data = {
            }
            $.post(_this.attr("href"), data, function(response){
                _this.hide("slow", function(){ _this.closest("tr").remove(); });
                //location.reload();
                showReloadButton();
            });
            return false;
        });
        
        $(".custom_field_delete").on("click",function(){
            var _this = $(this);
            var data = {
                custom : _this.data("custom")
            }
            $.post(_this.attr("href"), data, function(response){
                _this.hide("slow", function(){ _this.closest("tr").remove(); });
                //location.reload();
                showReloadButton();
            });
            return false;
        });
        
        $(".remove-item").on("click",function(){
            var _this = $(this);
            var data = {};
            
            $.post(_this.attr("href"), data, function(response){
                _this.hide("slow", function(){ _this.closest("tr").remove(); });
                //location.reload();
                showReloadButton();

            });
            return false;
        });

        $(".add-connected").on("click",function(){
            var _this = $(this);
            var data = {};
            
            $.post(_this.attr("href"), data, function(response){
                _this.closest("tr").after("<tr><td colspan=9>"+response+"</td></tr>");
            });
            return false;
        });

        $(".vehicle_edit").on("click",function(e){
            e.preventDefault();
            var _this = $(this);
            var data = {
                itemId : _this.data("vehicleid")
            }
            $.post(_this.attr("href"), data, function(response){
                var form = $("#quantity_vehicle_form"),
                modal = $("#vehicleModal");
                form.attr("action",_this.attr("href")+"&update=1")
                form.find("#quantity_inp").val(response.quantity);
                form.data("vehicleid",_this.data("vehicleid"));
                modal.modal("show");
            });
            
        });

        $("#quantity_vehicle_form").validate({
            rules: {
                quantity: {
                    minlength: 1,
                    required: true,
                    number: true,
                    min: 1,
                }
            },
            highlight: function (element) {
                $(element).closest(".control-group").removeClass("success").addClass("error");
            },
            success: function (element) {
                element.text("").addClass("valid").closest(".control-group").removeClass("error").addClass("success");
            },
            submitHandler: function(form) {
                var _this = $(form),
                result = $("#form_result"),
                data = {
                    itemId : _this.data("vehicleid"),
                    quantity : _this.find("#quantity_inp").val()
                };
                $.post(_this.attr("action"), data, function(response){
                    result.text(response.mess);
                    location.reload();
                });
                
                return false;
            }
        });
    });
    
    $("input.change-children").on("input", function(){
        var el = $(this);
        var val = el.val();
        $(".child-to-change").filter("[data-category="+el.data("category")+"]").filter("[data-attribute="+el.data("attribute")+"]").each(function(i,e){
            $(e).val(val);
        });
    });

    $(".sort-up-button").click(function(e){
        e.preventDefault();
        row = $(this).parent().parent();
        p = row.next();
        var sub_rows = [];
        var i=0;
        while ((!p.hasClass("parent-row"))&&(!p.hasClass("cat-row")))
            {
                sub_rows[i] = p;
                i++;
                p = p.next();
            }
            p = row.prev();
            while ((!p.hasClass("parent-row"))&&(p.length))
            {
                p = p.prev();
            }
            if (p.length)
                row.insertBefore(p);
            for (j=0; j<i; j++)
            {
                sub_rows[j].insertAfter(row);
            }
            savePositions(row.data("cat"));
        
    });
    $(".sort-down-button").click(function(e){
        e.preventDefault();
        //alert($(this).parent().parent().data("id"));
        row = $(this).parent().parent();
        p = row.next();
        var sub_rows = [];
        var i=0;
        while ((!p.hasClass("parent-row"))&&(!p.hasClass("cat-row")))
            {
                sub_rows[i] = p;
                i++;
                p = p.next();
            }
        
        var sub_rows2 = [];
        var i2=0;
        t = p.next();
        while ((!t.hasClass("parent-row"))&&(!t.hasClass("cat-row")))
            {
                sub_rows2[i2] = t;
                i2++;
                t = t.next();
            }
        row.insertAfter(p);
        for (j=0; j<i; j++)
        {
            sub_rows[j].insertAfter(row);
        }
        for (j=0; j<i2; j++)
        {
            sub_rows2[j].insertAfter(p);
        }
        savePositions(row.data("cat"));
    });
    
    function savePositions(cat)
    {
        var pos = 0;
        var data = [];
        $(".offer_table.gear").children("tbody").children("tr").each(function(){
            if ($(this).data("cat")){
                if ($(this).data("cat")==cat)
                {
                    data[pos] = $(this).data("id")+"_"+$(this).data("type");
                    $(this).data("position", pos);
                    pos++;
                }
            }
        });
        $.post("/admin/offer/default/save-order", {items:data}, function(response){
            });
        
    }
    $("select.change-children").on("change", function(){
        var el = $(this);
        var val = el.val();
        $(".child-to-change").filter("[data-category="+el.data("category")+"]").filter("[data-attribute="+el.data("attribute")+"]").each(function(i,e){
            $(e).val(val);
            if ($(e).hasClass("gears-price-dropdown")){
                gear_id = $(e).data("gearid")
                price_id = $(e).val();
                if (price_id>0)
                    $(e).parent().parent().parent().find(".price-input").val(prices[price_id][gear_id]); 
            }             
                

            });

        $(this).closest("form").submit();

    });
    
    '); ?>
<script type="text/javascript">
    var prices = JSON.parse('<?php echo json_encode($model->getGearPrices())?>');

    function showReloadButton()
    {
        $("#toast-container").show();
    }
    function saveSchema()
    {
        swal({
          text: 'Podaj nazwę schematu',
          content: {
            element: "input",
            attributes: {
              placeholder: "Podaj nazwę",
              type: "text",
            }
        },
          button: {
            text: "OK",
            closeModal: true,
          },
        })
        .then(name => {
          if (!name) name="schemat1";
            x = name;
            url = "<?=Url::to('/admin/offer/role/save-schema?offer_id='.$model->id.'&name=')?>"+x;
            $.ajax({url: url, success: function(result){
                toastr.success('<?=Yii::t('app', 'Zapisano!')?>')
            }});
        });
    }
    function loadSchema()
    {
        val = $("#offer-role-schema-id").val();
        location.href = "<?=Url::to('/admin/offer/role/load-schema?view=true&offer_id='.$model->id.'&schema=')?>"+val;
    }
</script>
<?php
$this->registerJs('
     
    $(".open-favorite-list").click(function(e){
        e.preventDefault();
        $("#favorite-modal").modal("show").find(".modalContent").empty().load($(this).attr("href"));
        //$("#favorite-modal").find(".close").hide();
    });

     $(".duration-input").change(function(){
    $(this).val($(this).val().replace(",", "."));
    $(this).val($(this).val().replace(" ", ""));
});
     $(".child-to-change").change(function(){
    $(this).val($(this).val().replace(",", "."));
    $(this).val($(this).val().replace(" ", ""));
});

    $(".price-input").change(function(){
    $(this).val($(this).val().replace(",", "."));
    $(this).val($(this).val().replace(" ", ""));
});');

$this->registerCSS('
    #open-favorite{
    position: fixed;
    top: 450px;
    right: 0px;
    z-index: 100;
}
.open-favorite-list{
height: 48px;
    width: 48px;
    display: block;
    background: #1ab394;
    padding: 7px 8px;
    text-align: center;
    color: #fff;
    border-radius: 50%;
    font-size: 24px;
    }

.open-favorite-list:hover{
    background: #20cba8;
    color: #fff;
    }

@media (min-width: 1200px)
{
.modal-lg {
    width: 1180px;
}
}
    ');
?>

<?php

use backend\modules\offers\models\OfferExtraItem;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
//use Symfony\Component\VarDumper\VarDumper;

use kartik\form\ActiveForm;
\common\assets\AreYouSureAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\models\Offer */
/* @var $offerForm \backend\modules\offers\models\OfferForm */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Oferty'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$labels = $model->attributeLabels();
$formatter = Yii::$app->formatter;
$user = Yii::$app->user;

Modal::begin([
    'id' => 'new-model',
    'options' => [
        'tabindex' => false,
    ],
]);
Modal::end();

$this->registerJs('
    $("#add-model").click(function(e){
        e.preventDefault();
        $("#new-model").modal("show").find(".modal-content").load($(this).attr("href"));
    });
    $("#add-model").on("contextmenu",function(){
       return false;
    }); 
');

?>
<div class="offer-view">
<div class="row">
<div class="col-xs-12">
    <div class="ibox float-e-margins">
    <div class="ibox-content">

    <div class="post-tools col-xs-8">
            <?php

        if ($user->can('menuOffersEdit')) {
            echo Html::a(Yii::t('app', 'Magazyn'), ['/warehouse/assign', 'id' => $model->id, 'type' => 'offer'], ['class' => 'btn btn-success btn-sm']);
        } ?>
        <?= Html::a(Yii::t('app', 'Magazyn zewnętrzny'), ['/outer-warehouse/assign', 'id'=>$model->id, 'type'=>'offer'], ['class'=>'btn btn-success btn-sm']); ?>
        <?= Html::a(Yii::t('app', 'Flota'), ['/vehicle/manage-offer', 'id'=>$model->id], ['class'=>'btn btn-success btn-sm']); ?>
        <?= Html::a(Yii::t('app', 'Obsługa'), ['/offer/role/assign', 'id'=>$model->id], ['class'=>'btn btn-success btn-sm']); ?>
        <?= Html::a(Yii::t('app', 'Inne'), ['/offer/default/offer-custom-items', 'id'=>$model->id], ['class'=>'btn btn-success btn-sm']); ?>
        <?= Html::a(Yii::t('app', 'Wyśli E-mailem'), ['/offer/default/send-mail', 'id'=>$model->id], ['class'=>'btn btn-success btn-sm'])." "; ?>
        <?php
        echo Html::a(Yii::t('app', 'PDF'), ['/offer/default/pdf', 'id'=>$model->id], ['class'=>'btn btn-sm btn-default download-pdf', 'target' => '_blank'])." ";
        echo Html::a(Yii::t('app', 'XLS'), ['/offer/default/excel', 'id'=>$model->id], ['class'=>'btn btn-sm btn-default download-pdf', 'target' => '_blank'])." ";
        ?>
        <br/>
        <?php 	if(isset($model->event_id)){
        			echo Html::a(Yii::t('app', 'Zobacz Event'), ['/event/view', 'id'=>$model->event_id], ['class'=>'btn btn-primary btn-sm']);
        		} else if (isset($model->rent_id)) {
                    echo Html::a(Yii::t('app', 'Zobacz Wypożyczenie'), ['/rent/view', 'id'=>$model->rent_id], ['class'=>'btn btn-primary btn-sm'])." ";
        		}
            if ($user->can('eventsEventAdd')){
                if ((!isset($model->event_id))&&(!isset($model->rent_id)))
                {
                    echo Html::a(Yii::t('app', 'Stwórz event'), ['event', 'id' => $model->id], ['class' => 'btn btn-success btn-sm'])." ";
                    echo Html::a(Yii::t('app', 'Dodaj do eventu'), ['/offer/default/add-to-events', 'id'=>$model->id], ['class'=>'btn btn-success btn-sm'])." ";
                    echo Html::a(Yii::t('app', 'Stwórz wypożyczenie'), ['rent', 'id' => $model->id], ['class' => 'btn btn-success btn-sm'])." ";
                    echo Html::a(Yii::t('app', 'Dodaj do wypożyczenia'), ['/offer/default/add-to-rent', 'id'=>$model->id], ['class'=>'btn btn-success btn-sm']);

                }
            }
         ?>
    </div>
    <div class="post-tools col-xs-4">
    <?php
            if ($user->can('menuOffersViewDuplicate')) {
                echo Html::a(Yii::t('app', 'Duplikuj'), ['duplicate', 'id' => $model->id], ['class' => 'btn btn-warning btn-sm pull-right'])." ";
            }
            if ($user->can('menuOffersEdit')) {
                echo Html::a(Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm pull-right']). " ";
            }
            if ($user->can('menuOffersDelete')) {
                echo Html::a(Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-sm pull-right',
                    'data' => [
                        'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                        'method' => 'post',
                    ],
                ])." ";
            }
            ?>
    </div>
    <div class="clearfix"></div>
    <hr>
    <?php
        $form = ActiveForm::begin([
            'id'=>'offer-form',
        ]);

        if ($user->can('menuOffersViewEdit')) {
    ?>
        <div class="form-group" data-spy="affix" style="z-index: 1000; right: 10px">
            <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?><br/>
            <?= Html::a(Yii::t('app', 'Dodaj'), ['/offer/default/add-item', 'offer' => $model->id, 'id' => $model->id], ['class' =>  'btn btn-primary', 'id' => 'add-model']) ?>
        </div>
    <?php } ?>
    <div class="pdf_box">
        <div class="header">
            <div class="col-xs-6 customer_info">
                <br>
                <div class="logo"><?= isset($settings['companyLogo']) ? Html::img(\Yii::getAlias('@uploads' . '/settings/').$settings['companyLogo']->value,['height'=>'300']) : '';?></div>
            </div>

            <div class="col-xs-6 customer_info">
                <div class="panel_mid_blocks">
                    <div class="panel_block">
                <table class="table">
                    <tr>
                        <td><?= Yii::t('app', 'Nazwa') ?>:</td>
                        <td><?= $model->name ?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Numer') ?>:</td>
                        <td><?= $model->id ?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Termin') ?>:</td>
                        <td><?= $model->term_from ?> <?= Yii::t('app', 'do') ?> <?= $model->term_to ?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Data oferty') ?>:</td>
                        <td><?= $model->offer_date ?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Strona') ?>:</td>
                        <td><?= $model->page ?></td>
                    </tr>
                </table>
                    </div>
                </div>

            </div>
        </div>

        <div class="clearfix"></div>

        <?php if ($model->customer !== null): ?>
            <div class="client_info">
                <div class="col-xs-6">
                    <div class="upf"><b><?= Yii::t('app', 'zamawiający') ?>:</b></div>
                    <h3><br>
                        <?= $model->customer->name ?>
                    </h3>
                <p><?=$model->customer->address ?></p>
                <p><?=$model->customer->zip ?> <?=$model->customer->city ?></p>
                <p><?= Yii::t('app', 'NIP') ?>: <?=$model->customer->nip ?></p>
                
                <p><?= Yii::t('app', 'e-mail') ?>: <?=$model->customer->email ?></p>
                <p><?= Yii::t('app', 'tel.') ?>: <?=$model->customer->phone ?></p>
                </div>
                <div class="col-xs-6">
                    <div class="panel_mid_blocks">
                        <div class="panel_block">
                    <table class="table table-bordered">
                        <tr>
                            <td><?= Yii::t('app', 'Kierownik projektu') ?>:</td>
                            <td><?php if ($model->manager) { echo $model->manager->first_name ." " . $model->manager->last_name; } ?></td>
                        </tr>
                        <tr>
                            <td><?= Yii::t('app', 'tel') ?>:</td>
                            <td><?php if ($model->manager) { echo $model->manager->phone; } ?></td>
                        </tr>
                        <tr>
                            <td><?= Yii::t('app', 'e-mail') ?>:</td>
                            <td><?php if ($model->manager) { $model->manager->email; } ?></td>
                        </tr>
                    </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="main_info col-xs-12">
            <h1>
                <?= Yii::t('app', 'Nazwa projektu') ?>: <?= $model->name ?>
            </h1>
            <p><u><?= Yii::t('app', 'Mejsce i adres') ?></u></p>
            <?php if ($model->location === null): ?>
                <p> <?=$model->address?> </p>
            <?php else: ?>
                <p><?= $model->location->name ?></p>
                <p><?= $model->location->city ?>, <?= $model->location->zip ?></p>
                <p><?= $model->location->address ?></p>
            <?php endif; ?>


			<div class="row">
				<div class="col-xs-6">
					<p><u><?= Yii::t('app', 'Harmonogram') ?></u></p>
                    <div class="panel_mid_blocks">
                        <div class="panel_block">
		            <table class="table">
		            	<tr>
		            		<th><?= Yii::t('app', 'Typ') ?></th>
		            		<th><?= Yii::t('app', 'Od') ?></th>
		            		<th><?= Yii::t('app', 'Do') ?></th>
		            	</tr>
		            	<?php 
                        $attrs = [
                            'packing',
                            'montage',
                            'practice',
                            'readiness',
                            'event',
                            'disassembly'
                        ];
                        $labels = $model->attributeLabels();

                        foreach ($attrs as $key => $attr) {
                            if(isset($model->{$attr.'_start'}) && isset($model->{$attr.'_end'})){
                        ?>
							<tr>
		            			<td><?= $labels[$attr.'DateRange'] ?></td>
		            			<td><?= $model->{$attr.'_start'} ?></td>
		            			<td><?= $model->{$attr.'_end'} ?></td>
		            		</tr>
		            	<?php }} ?>
		            	
		            </table>
                        </div>
                    </div>
				</div>
			</div>

        </div>
    <div class="panel_mid_blocks">
        <div class="panel_block">
        <table class="offer_table table">
            <tbody>
                <!----------|MAGAZYN|---------->
                <?php $total_summ_of_cats = 0; ?>
                <?php
                    $summ_of_one_cat = 0;
                    $summ_of_v = 0;
                    $summ_of_weight = 0;
                    $summ_of_power_consumption = 0;
                ?>
                <?php foreach ($offerForm->allGears as $categoryName => $items):
                        $cat = \common\models\GearCategory::find()->where(['lvl'=>1])->andWhere(['name'=>$categoryName])->one();
                        $summ_of_one_cat = 0;
                        if ($cat->color)
                        {
                            $style= "style='background-color:".$cat->color.";'";
                        }else{
                            $style = "";
                        }

                    ?>

                    <tr>
                        <td colspan="9" class="newsystem-bg" <?=$style?>><b><u><?=$categoryName?></u></b><?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/warehouse/assign', 'id'=>$model->id, 'type'=>'offer', 'c'=>$cat->id], ['class'=>'btn btn-xs pull-left white-button']); ?></td>
                    </tr>
                    <tr>
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
                            <?php echo $form->field($offerForm, 'gearSettings['.$categoryName.'][first_day_percent]')->textInput([
                                'class'=>'change-children',
                                'data' => [
                                    'category'=>str_replace(" ", "-", $categoryName),
                                    'attribute'=>'first_day_percent',
                                ]
                            ])->label(Yii::t('app', 'Procent dnia pierwszego')); ?>
                        </td>
                    </tr>
                    <tr>

                        <th><?= Yii::t('app', 'Nazwa') ?></th>
                        <th><?= Yii::t('app', 'Opis') ?></th>
                        <th style="width:100px"><?= Yii::t('app', 'Cena') ?></th>
                        <th style="width:100px"><?= Yii::t('app', 'Liczba') ?></th>
                        <th style="width:100px"><?= Yii::t('app', 'Rabat') ?></th>
                        <th style="width:100px"><?= Yii::t('app', 'Dni pracy') ?></th>
                        <th style="width:100px"><?= Yii::t('app', '% dnia pierwszego') ?></th>
                        <th><?= Yii::t('app', 'Razem netto') ?></th>
                        <th></th>
                    </tr>
                    <?php foreach ($items as  $key => $data):
                            if ($data['type'] != 'extraGear') {
                                $gearId = $data["id"];
                                $baseIndex = $data['type'] . 'Models[' . $gearId . ']'; ?>
                                <tr>

                                    <td><?= $data['name'] ?></td>
                                    <td><?= $form->field($offerForm, $baseIndex . '[description]')->textInput()->label(false) ?></td>
                                    <td><?= $form->field($offerForm, $baseIndex . '[price]')->textInput()->label(false) ?></td>
                                    <td><?= $form->field($offerForm, $baseIndex . '[quantity]')->textInput()->label(false); ?></td>
                                    <td><?= $form->field($offerForm, $baseIndex . '[discount]')->textInput(['class' => 'child-to-change',
                                            'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'discount',]])->label(false); ?></td>
                                    <td><?php echo $form->field($offerForm, $baseIndex . '[duration]')->textInput(['class' => 'child-to-change',
                                            'data' => ['category' => str_replace(" ", "-", $categoryName),
                                                'attribute' => 'duration',]])->label(false); ?></td>
                                    <td><?= $form->field($offerForm, $baseIndex . '[first_day_percent]')->textInput()->label(false); ?></td>
                                    <td class="value"><?= $formatter->asCurrency($data['value']) ?></td>
                                    <td><?php
                                        if ($user->can('menuOffersViewEdit')) {
                                            echo Html::a(Html::icon('trash'), ['/offer/default/remove-item',
                                                'offerId' => $model->id, 'itemType' => $data['type'],
                                                'itemId' => $gearId, 'id' => $model->id], ['class' => 'btn btn-danger btn-xs remove-item']);
                                        } ?>
                                    </td>
                                </tr>
                                <?php
                                $summ_of_one_cat += $data['value'];
                                $summ_of_v += $data['volume'] * $data['quantity'];
                                $summ_of_weight += $data['weight'] * $data['quantity'];
                                $summ_of_power_consumption += $data['power_consumption'] * $data['quantity'];
                            }
                            else {
                                $extraItemModel = OfferExtraItem::findOne($data['id']); ?>
                                <tr>
                                    <td><?= $form->field($extraItemModel, '['.$data['id'].']name')->textInput()->label(false) ?></td>
                                    <td><?= $form->field($extraItemModel, '['.$data['id'].']description')->textInput()->label(false) ?></td>
                                    <td><?= $form->field($extraItemModel, '['.$data['id'].']price')->textInput()->label(false) ?></td>
                                    <td><?= $form->field($extraItemModel, '['.$data['id'].']quantity')->textInput()->label(false) ?></td>
                                    <td><?= $form->field($extraItemModel, '['.$data['id'].']discount')->textInput()->label(false) ?></td>
                                    <td><?= $form->field($extraItemModel, '['.$data['id'].']duration')->textInput()->label(false) ?></td>
                                    <td><?= $form->field($extraItemModel, '['.$data['id'].']first_day_percent')->textInput()->label(false) ?></td>
                                    <td><?= $formatter->asCurrency($data['value']) ?></td>
                                    <td>
                                        <?php
                                        if ($user->can('menuOffersViewEdit')) {
                                            echo Html::a(Html::icon('trash'), ['/offer/default/delete-extra-item', 'id' => $model->id, 'item' => $data['id']], ['class' => 'btn-xs btn btn-danger extra_delete']);
                                        } ?>
                                    </td>
                                </tr><?php
                                $summ_of_one_cat += $data['value'];
                            }
                         ?>
                    <?php endforeach; ?>
                    <tr class="warning">
                        <td colspan="8"><b><u><?= Yii::t('app', 'Łącznie') ?> <?=$categoryName?></u></b></td>
                        <td><?=$formatter->asCurrency($summ_of_one_cat)?></td>
                    </tr>
                    <tr><td colspan="9"></td></tr>
                    <?php $total_summ_of_cats += $summ_of_one_cat; ?>
                <?php endforeach; ?>
                <!----------|END MAGAZYN|---------->
        </tbody>
        </table>
        <table class="offer_table table">
        <tbody>
                <!----------|TRANSPORT|---------->
                <?php if (isset($settings['transportColor']))
                        {
                            $style= "style='background-color:".$settings['transportColor']->value.";'";
                        }else{
                            $style = "";
                        } ?>
                    <tr>
                        <td colspan="7" class="newsystem-bg" <?=$style?>><b><u><?= Yii::t('app', 'Transport') ?></u><?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/vehicle/manage-offer', 'id'=>$model->id], ['class'=>'btn btn-xs pull-left white-button']); ?></b></td>
                    </tr>
                <tr>
                    <th></th>
                    <th><?= Yii::t('app', 'Samochód') ?></th>
                    <th style="width:100px"><?= Yii::t('app', 'Liczba') ?></th>
                    <th style="width:100px"><?= Yii::t('app', 'Km') ?></th>
                    <th><?= Html::a('Zmień na ryczałt', Url::toRoute(['/offer/default/change-vehicle-type', 'id' => $model->id, 'offer_id'=>$model->id])) ?></th>
                    <th><?= Yii::t('app', 'Razem') ?></th>
                </tr>
                    <?php $transport_summ = 0;?>
                    <?php foreach ($model->getVehicleData() as $key => $vehicles): ?>
                        <?php foreach ($vehicles as $id=>$vehicle): ?>
                            <?php $baseIndex = 'vehicleModels['.$id.']'; ?>
                        <?php 
                            $price = $vehicle['value'];
                            $transport_summ += $price;
                        ?>
                        <tr>
                            <td></td>
                            <td><?= $vehicle['name'] ?></td>
                            <td><?= $vehicle['quantity'] ?></td>
                            <td><?= $form->field($offerForm, $baseIndex.'[distance]')->textInput()->label(false); ?></td>
                            <td><?= $form->field($offerForm, $baseIndex.'[price]')->textInput()->label(false); ?></td>
                            <td><?= $formatter->asCurrency($price); ?></td>
                            <td><?php if ($user->can('menuOffersViewEdit')) {
                                    echo Html::a(Html::icon('pencil'), ['/offer/default/manage-vehicle', 'id' => $model->id, 'offer_id' => $model->id], ['class' => 'btn-xs btn btn-warning vehicle_edit', 'data-vehicleID' => $vehicle['id']]);
                                    echo Html::a(Html::icon('trash'), ['/offer/default/delete-vehicle', 'id' => $model->id, 'offer_id' => $model->id], ['class' => 'btn-xs btn btn-danger vehicle_delete', 'data-vehicleID' => $vehicle['id']]);
                                } ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <?php
                        foreach ($model->getExtraItem(OfferExtraItem::TYPE_VEHICLE) as $vehicle) {
                            $transport_summ += $vehicle->price * $vehicle->quantity * $vehicle->duration * (1 - $vehicle->discount / 100);
                            ?>

                            <tr>
                                <td></td>
                                <td><?= $form->field($vehicle, '['.$vehicle->id.']name')->textInput()->label(false) ?></td>
                                <td><?= $form->field($vehicle, '['.$vehicle->id.']quantity')->textInput()->label(false) ?></td>
                                <td><?= $form->field($vehicle, '['.$vehicle->id.']duration')->textInput()->label(false); ?></td>
                                <td><?= $form->field($vehicle, '['.$vehicle->id.']price')->textInput()->label(false); ?></td>
                                <td><?= $formatter->asCurrency($vehicle->price * $vehicle->quantity * $vehicle->duration * (1 - $vehicle->discount / 100)); ?></td>
                                <td>
                                    <?php
                                        if ($user->can('menuOffersViewEdit')) {
                                            echo Html::a(Html::icon('trash'), ['/offer/default/delete-extra-item', 'id' =>  $model->id, 'item' => $vehicle->id], ['class' => 'btn-xs btn btn-danger extra_delete']);
                                        } ?>
                                </td>
                            </tr>
                            <?php
                        }
                    ?>
                    <tr class="warning">
                        <td colspan="5"><b><u><?= Yii::t('app', 'Łącznie Transport') ?></u></b></td>
                        <td><?=$formatter->asCurrency($transport_summ)?></td>
                        <td></td>
                    </tr>
                    <tr><td colspan="9"></td></tr>
                   
                <!----------|END TRANSPORT|---------->

                <!----------|START ROLES|---------->
                <?php if (isset($settings['crewColor']))
                        {
                            $style= "style='background-color:".$settings['crewColor']->value.";'";
                        }else{
                            $style = "";
                        } ?>
                    <tr>
                        <td colspan="7" class="newsystem-bg" <?=$style?>><b><u><?= Yii::t('app', 'Obsługa techniczna') ?></u></b><?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Zarządzaj'), ['/offer/role/assign', 'id'=>$model->id], ['class'=>'btn btn-xs pull-left white-button']); ?><div class="pull-right">    <?= Html::dropDownList('schema_id', null,
      ArrayHelper::map(\common\models\OfferRoleSchema::find()->where(['user_id'=>\Yii::$app->user->id])->all(), 'id', 'name'),['class'=>'form-control','style'=>'width:300px; display:inline-block; color:black;', 'id'=>'offer-role-schema-id']) ?>
    <?php echo Html::a(Yii::t('app', 'Załaduj szablon'), ['#'], ['class'=>'btn btn-success btn-sm', 'onclick'=>'loadSchema(); return false;']); ?></div></td>
                </tr>
                <tr>
                    <th style="width:100px"></th>
                    <th><?= Yii::t('app', 'Nazwa') ?></th>
                    <th style="width:100px"><?= Yii::t('app', 'Cena') ?></th>
                    <th style="width:100px"><?= Yii::t('app', 'Liczba') ?></th>
                    <th style="width:100px"><?= Yii::t('app', 'Liczba dni') ?></th>
                    <th colspan="2"><?= Yii::t('app', 'Razem netto') ?></th>
                </tr>
                <?php
                $time_type = "";
                $skills_summ = 0;
                    foreach ($offerForm->roleModels as $id => $rm):
                        /* @var $rm \common\models\OfferRole; */
                        $skills_summ += $rm->getValue();
                        $role = $rm->role;
                        $baseIndex = 'roleModels['.$id.']';
                        if ($time_type!=$rm->time_type)
                        {
                            $time_type = $rm->time_type;
                            echo "<tr><td colspan='8' style='text-align:center; font-weight:bold; background-color:#eee;'>".\common\models\OfferRole::getTimeType()[$rm->time_type]."</td></tr>";
                        }
                ?>

                    <tr>
                        <td></td>
                        <td><?php echo $role->name; ?></td>
                        <td><?php echo  $form->field($offerForm, $baseIndex.'[price]')->textInput()->label(false); ?></td>
                        <td><?php echo  $form->field($offerForm, $baseIndex.'[quantity]')->textInput()->label(false); ?></td>
                        <td><?php echo  $form->field($offerForm, $baseIndex.'[duration]')->textInput()->label(false); ?></td>
                        <td><?php echo $formatter->asCurrency($rm->getValue()); ?></td>
                        <td colspan="2">
                            <?php if ($user->can('menuOffersViewEdit')) {
                                echo Html::a(Html::icon('trash'), ['/offer/default/delete-role', 'role_id' => $rm->role->id, 'offer_id'=>$model->id, 'time_type'=>$rm->time_type], ['class'=>'btn-xs btn btn-danger role_delete','data-role'=>$role['id']]);
                            } ?>
                        </td>
                    </tr>

                <?php endforeach;  ?>
                <?php
                foreach ($model->getExtraItem(OfferExtraItem::TYPE_CREW) as $crew) {
                    $skills_summ += $crew->price * $crew->quantity * $crew->duration * (1 - $crew->discount / 100); ?>

                    <tr>
                        <td></td>
                        <td><?= $form->field($crew, '['.$crew->id.']name')->textInput()->label(false) ?></td>
                        <td><?= $form->field($crew, '['.$crew->id.']price')->textInput()->label(false) ?></td>
                        <td><?= $form->field($crew, '['.$crew->id.']quantity')->textInput()->label(false); ?></td>
                        <td><?= $form->field($crew, '['.$crew->id.']duration')->textInput()->label(false); ?></td>
                        <td><?= $formatter->asCurrency($crew->price * $crew->quantity * $crew->duration * (1 - $crew->discount / 100)); ?></td>
                        <td>
                            <?php if ($user->can('menuOffersViewEdit')) {
                                echo Html::a(Html::icon('trash'), ['/offer/default/delete-extra-item', 'id'=>$model->id, 'item' => $crew->id], ['class' => 'btn-xs btn btn-danger extra_delete']);
                            } ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>

                <tr class="warning">
                    <td colspan="5"><b><u><?= Yii::t('app', 'Łącznie Obsługa techniczna') ?></u></b></td>
                    <td><?php echo $formatter->asCurrency($skills_summ)?></td>
                    <td></td>
                </tr>
                <tr><td colspan="9"></td></tr>
                <!----------|END ROLES|---------->

                <!----------|START CUSTOM FIELDS|---------->
                <?php if (isset($settings['otherColor']))
                        {
                            $style= "style='background-color:".$settings['otherColor']->value.";'";
                        }else{
                            $style = "";
                        } ?>
                    <tr>
                        <td colspan="7" class="newsystem-bg" <?=$style?>><b><u><?= Yii::t('app', 'Inne') ?></u></b><?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/offer/default/offer-custom-items', 'id'=>$model->id], ['class'=>'btn btn-xs pull-left white-button']); ?></td>
                    </tr>
                    <tr>
                        <th></th>
                        <th><?= Yii::t('app', 'Nazwa') ?></th>
                        <th><?= Yii::t('app', 'Cena') ?></th>
                        <th><?= Yii::t('app', 'Liczba') ?></th>
                        <th><?= Yii::t('app', 'Rabat %') ?></th>
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
                            <td><?=$formatter->asCurrency($custom['price']) ?></td>
                            <td><?php echo $custom['diff_count']; ?></td>
                            <td><?php echo $custom['discount']; ?></td>
                            <td><?=$formatter->asCurrency($custom_full_price)?></td>
                            <td><?php if ($user->can('menuOffersViewEdit')) {
                                    echo Html::a(Html::icon('trash'), ['/offer/default/delete-custom-field', 'id' => $model->id, 'offer_id' => $model->id], ['class' => 'btn-xs btn btn-danger custom_field_delete', 'data-custom' => $custom['id']]);
                                } ?></td>
                        </tr>
                    <?php $custom_summ += $custom_full_price; } ?>
                    <tr class="warning">
                        <td colspan="5"><b><u><?= Yii::t('app', 'Łącznie inne') ?></u></b></td>
                        <td><?= $formatter->asCurrency($custom_summ)?></td>
                        <td></td>
                    </tr>
                    <tr><td colspan="9"></td></tr>
                <!----------|END CUSTOM FIELDS|---------->    
                <!----------|TOTAL|---------->
                <?php $total = $total_summ_of_cats+$transport_summ+$skills_summ+$custom_summ; ?>
                <tr class="success">
                    <td colspan="5"><b><u><?= Yii::t('app', 'Podsumowanie') ?></u></b></td>
                    <td><?=$formatter->asCurrency($total)?></td>
                    <td></td>
                </tr>
                <!----------|END TOTAL|---------->
            </tbody>
        </table>
        </div>
    </div>
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
                        <?=$formatter->asCurrency($total_summ_of_cats)?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Koszt transportu') ?>:
                    </td>
                    <td class="text-right">
                        <?=$formatter->asCurrency($transport_summ)?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Koszt obsługi') ?>:
                    </td>
                    <td class="text-right">
                        <?= $formatter->asCurrency($skills_summ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Inne koszty') ?>:
                    </td>
                    <td class="text-right">
                        <?= $formatter->asCurrency($custom_summ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b><?= Yii::t('app', 'Wartość netto po rabacie') ?>:</b>
                    </td>
                    <td class="text-right">
                        <?=$formatter->asCurrency($total)?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Podatek VAT 23%') ?>:
                    </td>
                    <td class="text-right">
                        <?php $vat = $total*0.23; 
                            echo $formatter->asCurrency($vat); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b><?= Yii::t('app', 'Wartość brutto') ?>:</b>
                    </td>
                    <td class="text-right">
                        <?php $brutto = $total + $vat; 
                            echo $formatter->asCurrency($brutto); ?>
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
                        <?php $summ_of_v_in_m_3 = $summ_of_v/1000000; echo round($summ_of_v, 2); ?> <?= Yii::t('app', 'm') ?><sup>3</sup>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Całkowita waga netto') ?>:
                    </td>
                    <td>
                        <?= $summ_of_weight; ?> <?= Yii::t('app', 'kg') ?>
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
</div>
  </div>
</div>
</div>
<?php

$this->registerJs('
    jQuery(document).ready(function($){
        $("form").areYouSure();
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
                location.reload();
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
                location.reload();
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
                location.reload();
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
                location.reload();
            });
            return false;
        });
        
        $(".remove-item").on("click",function(){
            var _this = $(this);
            var data = {};
            
            $.post(_this.attr("href"), data, function(response){
                _this.hide("slow", function(){ _this.closest("tr").remove(); });
                location.reload();
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
    
    '); ?>
<script type="text/javascript">
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

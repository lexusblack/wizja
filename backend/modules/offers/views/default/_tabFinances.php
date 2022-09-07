<?php
use yii\bootstrap\Html;
use kartik\grid\GridView;
use kartik\widgets\ActiveForm;
use yii\web\View;
use yii\helpers\Url;
use yii\bootstrap\Modal;

$currency = $model->priceGroup->currency;


Modal::begin([
    'id' => 'add-cost',
    'header' => Yii::t('app', 'Dodaj koszt'),
    'class' => 'modal',
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

/* @var $model \common\models\Event; */
/* @var $this \yii\web\View */
$formatter = Yii::$app->formatter;
$user = Yii::$app->user;
?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  blue-bg">
                    <h5><?= Yii::t('app', 'Sprzęt wypożyczony')?></h5>
                </div>
                <div class="ibox-content">
                <table class="table">
                <tr>
                	<th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'Koszt')?></th><th><?=Yii::t('app', 'Sztuk')?></th><th><?=Yii::t('app', 'Dni')?></th><th><?=Yii::t('app', 'Suma')?></th>
                </tr>
                <?php
                $total_price_outer = 0;
                foreach ($model->offerOuterGears as $gear){
                    if ($gear->outerGearModel->type!=3)
                    {
                        $price = $gear->outerGearModel->getPrice()*$gear->quantity+$gear->outerGearModel->getPrice()*$gear->quantity*($gear->duration-1)*$gear->first_day_percent/100;
                        $total_price_outer+=$price
                        ?>
                        <tr>
                            <td><?=$gear->outerGearModel->name?></td><td><?=$formatter->asCurrency($gear->outerGearModel->getPrice())?></td><td><?=$gear->quantity?></td><td><?=$gear->duration?></td><td><?=$formatter->asCurrency($price)?></td>
                        </tr>
                    <?php                        
                    }

            	}
                ?>
                <tr class="warning"><td colspan=3></td><td><b><u><?=Yii::t('app', 'Łącznie')?></u></b></td><td><?=$formatter->asCurrency($total_price_outer)?></td></tr>
                </table>
                </div>
               </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  navy-bg">
                    <h5><?= Yii::t('app', 'Materiały eksploatacyjne')?></h5>
                </div>
                <div class="ibox-content">
                <table class="table">
                <tr>
                	<th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'Koszt')?></th><th><?=Yii::t('app', 'Sztuk')?></th><th><?=Yii::t('app', 'Suma')?></th>
                </tr>
                <?php
                $total_price_gear = 0;
                foreach ($model->offerGears as $gear){
                	if ($gear->gear->type==3){
                	$price = $gear->gear->value*$gear->quantity;
                	$total_price_gear+=$price
                	?>
                	<tr>
                		<td><?=$gear->gear->name?></td><td><?=$formatter->asCurrency($gear->gear->value)?></td><td><?=$gear->quantity?></td><td><?=$formatter->asCurrency($price)?></td>
                	</tr>
                <?php
            	}
            	}
                foreach ($model->offerOuterGears as $gear){
                    if ($gear->outerGearModel->type==3)
                    {
                        $price = $gear->outerGearModel->getPrice()*$gear->quantity;
                        $total_price_gear+=$price
                        ?>
                        <tr>
                            <td><?=$gear->outerGearModel->name?></td><td><?=$formatter->asCurrency($gear->outerGearModel->getPrice())?></td><td><?=$gear->quantity?></td><td><?=$formatter->asCurrency($price)?></td>
                        </tr>
                    <?php                        
                    }

                }
                
                ?>
                <tr class="warning"><td colspan=2></td><td><b><u><?=Yii::t('app', 'Łącznie')?></u></b></td><td><?=$formatter->asCurrency($total_price_gear)?></td></tr>
                </table>
                </div>
               </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  navy-bg">
                    <h5><?= Yii::t('app', 'Sprzęty dodatkowe i grupy')?></h5>
                </div>
                <div class="ibox-content">
                <table class="table">
                <tr>
                    <th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'Koszt')?></th><th><?=Yii::t('app', 'Sztuk')?></th><th><?=Yii::t('app', 'Suma')?></th>
                </tr>
                <?php
                $total_price_gear = 0;
                foreach ($model->extraItems as $gear){
                    if ($gear->type==1)
                    {
                    $price = $gear->cost*$gear->quantity;
                    $total_price_gear+=$price
                    ?>
                    <tr>
                        <td><?=$gear->name?></td><td><?=$formatter->asCurrency($gear->cost)?></td><td><?=$gear->quantity?></td><td><?=$formatter->asCurrency($price)?></td>
                    </tr>
                <?php
                }
                }
                ?>
                <tr class="warning"><td colspan=2></td><td><b><u><?=Yii::t('app', 'Łącznie')?></u></b></td><td><?=$formatter->asCurrency($total_price_gear)?></td></tr>
                </table>
                </div>
               </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  navy-bg">
                    <h5><?= Yii::t('app', 'Produkcja')?></h5>
                </div>
                <div class="ibox-content">
                <table class="table">
                <tr>
                    <th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'Koszt')?></th><th><?=Yii::t('app', 'Sztuk')?></th><th><?=Yii::t('app', 'Suma')?></th>
                </tr>
                <?php
                $total_price_gear = 0;
                foreach ($model->extraItems as $gear){
                    if ($gear->type==4)
                    {
                    $price = $gear->cost*$gear->quantity;
                    $total_price_gear+=$price
                    ?>
                    <tr>
                        <td><?=$gear->name?><?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj koszt'), ['/offer-extra-cost/create', 'offer_id'=>$model->id, 'offer_extra_item_id'=>$gear->id], ['class'=>'pull-right add-extra-cost']); ?><?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj mat. eksl.'), ['/outer-warehouse/assign', 'id'=>$model->id, 'type'=>'offer', 'c'=>$gear->category_id,'item' => $gear->id, 'type2'=>"extraGear", 'return'=>'finance'], ['class'=>'pull-right']); ?></td><td><?=$formatter->asCurrency($gear->cost)?></td><td><?=$gear->quantity?></td><td><?=$formatter->asCurrency($price)?></td>
                    </tr>
                <?php
                    foreach ($gear->offerExtraCosts as $cost)
                    {
                        $price = $cost->cost*$cost->quantity;
                        $total_price_gear+=$price
                    ?>
                    <tr>
                        <td style="padding-left:30px"><?="- ".$cost->name?></td><td><?=$formatter->asCurrency($cost->cost)?></td><td><?=$cost->quantity?></td><td><?=$formatter->asCurrency($price)?></td>
                    </tr>
                <?php
                
                     }
                     foreach ($gear->offerOuterGears as $cost)
                    {
                        $price = $cost->outerGearModel->getPrice()*$cost->quantity;
                        $total_price_gear+=$price
                        ?>
                        <tr>
                            <td style="padding-left:30px"><?="- ".$cost->outerGearModel->name?></td><td><?=$formatter->asCurrency($cost->outerGearModel->getPrice())?></td><td><?=$cost->quantity?></td><td><?=$formatter->asCurrency($price)?></td>
                        </tr>
                <?php
                
                     }
                }
                }
                ?>
                <tr class="warning"><td colspan=2></td><td><b><u><?=Yii::t('app', 'Łącznie')?></u></b></td><td><?=$formatter->asCurrency($total_price_gear)?></td></tr>
                </table>
                </div>
               </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  yellow-bg">
                    <h5><?= Yii::t('app', 'Transport')?></h5>
                </div>
                <div class="ibox-content">
                <table class="table">
                <tr>
                	<th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'Koszt')?></th><th><?=Yii::t('app', 'L. Sztuk')?></th><th><?=Yii::t('app', 'Przelicznik')?></th><th><?=Yii::t('app', 'Suma')?></th>
                </tr>
                <?php
                $total_price_transport = 0;
                foreach ($model->offerVehicles as $car){
                		$value = $car->cost;
                		$distance = $car->distance;
                        $quantity = $car->quantity;
                		$price = $value*$distance*$quantity;
                	
                		
                	$total_price_transport+=$price;
                                        ?>
                    <tr>
                        <td><?=$car->vehicle->name?></td><td><?php $form = ActiveForm::begin(); echo  $form->field($car, 'id')->hiddenInput()->label(false);
                            echo $form->field($car, 'cost')->widget(\yii\widgets\MaskedInput::className(), [
                                            'clientOptions'=> [
                                                'alias'=>'decimal',
                                                'rightAlign'=>false,
                                                'digits'=>2,
                                            ], 'options'=>['class'=>'salary_costV']
                                        ])->label(false);

                        
                         ActiveForm::end();
                        ?></td><td><?=$quantity?></td><td><?=$distance?></td><td><?=$formatter->asCurrency($price, $currency)?></td>
                    </tr>
                <?php
                }
                    foreach ($model->extraItems as $gear){
                    if ($gear->type==2)
                    {
                    $price = $gear->cost*$gear->quantity;
                    $total_price_transport+=$price
                    ?>
                    <tr>
                        <td><?=$gear->name?></td><td><?=$formatter->asCurrency($gear->cost, $currency)?></td><td><?=$gear->quantity?></td><td><?=$gear->duration?></td><td><?=$formatter->asCurrency($price, $currency)?></td>
                    </tr>
                <?php
                }
                }

            	
                ?>
                <tr class="warning"><td colspan=3></td><td><b><u><?=Yii::t('app', 'Łącznie')?></u></b></td><td><?=$formatter->asCurrency($total_price_transport, $currency)?></td></tr>
                </table>
                </div>
               </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  grey-bg">
                    <h5><?= Yii::t('app', 'Obsługa')?></h5>
                </div>
                <div class="ibox-content">
                <table class="table">
                <tr>
                	<th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'Koszt')?></th><th><?=Yii::t('app', 'Liczba')?></th><th><?=Yii::t('app', 'Okres')?></th><th><?=Yii::t('app', 'Typ stawki')?></th><th><?=Yii::t('app', 'Suma')?></th>
                </tr>
                <?php
                $total_price_crew = 0;
                foreach ($model->offerRoles as $role){
                        if ($role->salary_type==1)
                        {
                            $p = $role->cost;
                            $price = $role->duration*$role->quantity*$role->cost;
                            $duration = $role->duration." ".Yii::t('app', 'dni');
                        }else{
                            $duration =$model->getPeriodTime($role->time_type);
                            $price = $duration*$role->quantity*$role->cost_hour;
                            $duration = $duration." ".Yii::t('app', 'godzin');
                            $p = $role->cost_hour;
                        }
                	
                	$total_price_crew+=$price;
                	?>
                	<tr>
                		<td><?=$role->role->name." [".\common\models\OfferSchedule::findOne($role->time_type)->name."]"?></td>

                        <td><?php $form = ActiveForm::begin(); echo  $form->field($role, 'id')->hiddenInput()->label(false); echo  $form->field($role, 'offer_id')->hiddenInput()->label(false); echo  $form->field($role, 'role_id')->hiddenInput()->label(false); echo  $form->field($role, 'time_type')->hiddenInput()->label(false); 
                        if ($role->salary_type==1){
                            echo $form->field($role, 'cost')->widget(\yii\widgets\MaskedInput::className(), [
                                            'clientOptions'=> [
                                                'alias'=>'decimal',
                                                'rightAlign'=>false,
                                                'digits'=>2,
                                            ], 'options'=>['class'=>'salary_cost']
                                        ])->label(false);

                        }else{
                            echo $form->field($role, 'cost_hour')->widget(\yii\widgets\MaskedInput::className(), [
                                            'clientOptions'=> [
                                                'alias'=>'decimal',
                                                'rightAlign'=>false,
                                                'digits'=>2,
                                            ], 'options'=>['class'=>'salary_cost']
                                        ])->label(false);
                        }
                         ActiveForm::end();
                        ?>
                            </td>

                        <td><?=$role->quantity?></td>

                        <td><?=$duration?></td><td><?php $form = ActiveForm::begin(); echo  $form->field($role, 'id')->hiddenInput()->label(false); echo  $form->field($role, 'offer_id')->hiddenInput()->label(false); echo  $form->field($role, 'role_id')->hiddenInput()->label(false); echo  $form->field($role, 'time_type')->hiddenInput()->label(false); echo  $form->field($role, 'salary_type')->dropDownList([1=>"Dzienna", 2=>"Godzinowa"], ['class'=>'salary_type'])->label(false); ActiveForm::end(); ?></td><td><?=$formatter->asCurrency($price, $currency)?></td>
                	</tr>
                <?php
            	}
                    foreach ($model->extraItems as $gear){
                    if ($gear->type==3)
                    {
                    $price = $gear->cost*$gear->quantity;
                    $total_price_crew+=$price
                    ?>
                    <tr>
                        <td><?=$gear->name?></td><td><?=$formatter->asCurrency($gear->cost, $currency)?></td><td><?=$gear->quantity?></td><td><?=$gear->duration?></td><td></td><td><?=$formatter->asCurrency($price, $currency)?></td>
                    </tr>
                <?php
                }
                }
                ?>
                <tr class="warning"><td colspan=4></td><td><b><u><?=Yii::t('app', 'Łącznie')?></u></b></td><td><?=$formatter->asCurrency($total_price_crew, $currency)?></td></tr>
                </table>
                </div>
               </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  navy-bg">
                    <h5><?= Yii::t('app', 'Inne koszty')?></h5>
                    <div class="ibox-tools white">
                                    <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj koszt'), ['/offer-extra-cost/create', 'offer_id'=>$model->id], ['class'=>'white-button add-extra-cost']); ?>
                            </div>
                </div>
                <div class="ibox-content">
                <table class="table">
                <tr>
                    <th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'Koszt')?></th><th><?=Yii::t('app', 'Sztuk')?></th><th><?=Yii::t('app', 'Suma')?></th><th></th>
                </tr>
                <?php
                $total_price_other = 0;
                foreach ($model->offerExtraCosts as $gear){
                    $price = $gear->cost*$gear->quantity;
                    $total_price_other+=$price
                    ?>
                    <tr>
                        <td><?=$gear->name?></td><td><?=$formatter->asCurrency($gear->cost)?></td><td><?=$gear->quantity?></td><td><?=$formatter->asCurrency($price)?></td><td><?= Html::a("<i class='fa fa-pencil'></i>", ['/offer-extra-cost/update', 'id' => $gear->id], ['class' => 'btn btn-primary btn-xs  add-extra-cost']) ?> <?= Html::a("<i class='fa fa-trash'></i>", ['/offer-extra-cost/delete', 'id' => $gear->id], [
                'class' => 'btn btn-danger btn-xs',
                'data' => [
                    'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                    'method' => 'post',
                ],
            ])
            ?></td>
                    </tr>
                <?php
                
                }
                foreach ($model->offerCustomItems as $gear){
                    $price = $gear->cost*$gear->quantity;
                    $total_price_other+=$price
                    ?>
                    <tr>
                        <td><?=$gear->name?></td><td><?=$formatter->asCurrency($gear->cost)?></td><td><?=$gear->quantity?></td><td><?=$formatter->asCurrency($price)?></td><td></td>
                    </tr>
                <?php
                
                }
                ?>
                <tr class="warning"><td colspan=2></td><td><b><u><?=Yii::t('app', 'Łącznie')?></u></b></td><td><?=$formatter->asCurrency($total_price_other)?></td></tr>
                </table>
                </div>
               </div>
    </div>
</div>
</div>

<?php
$saveRoleUrl = Url::to(['role/save']);
$saveRoleUrlV = Url::to(['default/save-vehicle']);
$this->registerJs('
    $(".salary_type").change(function(){
        form = $(this).closest("form");
        $.ajax({
          type: "POST",
          url: "'.$saveRoleUrl.'",
          data: form.serialize(),
          success: function(){ window.location.reload(false);}
        });
    });

    $(".salary_cost").change(function(){
        form = $(this).closest("form");
        $.ajax({
          type: "POST",
          url: "'.$saveRoleUrl.'",
          data: form.serialize(),
          success: function(){  window.location.reload(false);
            }
        });
    });

    $(".salary_costV").change(function(){
        form = $(this).closest("form");
        $.ajax({
          type: "POST",
          url: "'.$saveRoleUrlV.'",
          data: form.serialize(),
          success: function(ret){  window.location.reload(false);
            }
        });
    });

    $(".add-extra-cost").click(function(e){
        e.preventDefault();
        $("#add-cost").modal("show").find(".modalContent").load($(this).attr("href"));
    });

    ');
?>
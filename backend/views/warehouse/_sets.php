<?php


use yii\bootstrap\Html;
use kartik\form\ActiveForm;
use yii\helpers\Url;
if (!isset($item)){ $item=null;}
if (!isset($type2)){ $type2=null;}
?>
    <?php if ($gearSet){ ?>
    <div class="row">
    <div class="col-md-12">
        <table class="kv-grid-table table kv-table-wrap">

            <tr class="newsystem-bg">
            <th style="width: 70px;"></th>
            <?php if ($event){ ?>
            <th style="width: 60px;"></th>
            <?php } ?>
            <th style="width: 100px;"></th>
            <th><?= Yii::t('app', 'Zestawy z kategorii ').$category?></th>

            </tr><?php

            foreach ($gearSet as $gs) {

                ?>
                <tr class="gear-row" data-gearid="<?= $gs->id ?>"">
                    <td><?php echo Html::icon('arrow-down', ['class' => 'row-warehouse-out', 'style' => 'cursor: pointer;']); ?>
                    </td>
                    <?php if ($event){ ?>
                    <td>
                        <a href="#" onclick="addGearSet(<?=$gs->id?>); return false;" class="btn btn-xs btn-success"><?=Yii::t('app', 'Dodaj zestaw')?></a>
                    </td>
                    <?php } ?>
                                        <td>
                                            <?php 
                                                if ($gs->photo == null)
                                                {
                                                   echo '';
                                                }else{
                                                    echo Html::img($gs->getPhotoUrl(), ['width'=>'50px']);
                                                }
                                                                    
                                            ?>
                                        </td>
                    <td><?php
                    echo Html::a($gs->name, ['gear-set/view', 'id'=>$gs->id]);
                    ?></td>
                </tr>
                <tr style="display: none;" class="sub_models">
                        <td colspan="4">
                            <table class="kv-grid-table table kv-table-wrap" style="width: 70%; margin: auto;">
                                <thead>
                                <td><?= Yii::t('app', '#') ?></td>
                                <td><?= Yii::t('app', 'Nazwa') ?></td>
                                <td><?= Yii::t('app', 'Liczba sztuk') ?></td>
                                <td><?= Yii::t('app', 'Magazyn') ?></td>
                                </thead>
                                <tbody><?php
                                $i=0;
                                foreach ($gs->gearSetItems as $gsi) {
                                    $i++;
                                        ?>
                                        <tr>
                                        <td><?= $i?></td>

                                        <td><?= $gsi->gear->name ?></td>
                                        <td><?= $gsi->quantity?></td>
                                        <td><?= Yii::t('app', 'Wewnętrzny') ?></td>
                                        <?php
                                } foreach ($gs->gearSetOuterItems as $gsi) {
                                    $i++;
                                        ?>
                                        <tr>
                                        <td><?= $i?></td>

                                        <td><?= $gsi->outerGearModel->name ?></td>
                                        <td><?= $gsi->quantity?></td>
                                        <td><?= Yii::t('app', 'Zewnętrzny') ?></td>
                                        <?php
                                } ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <?php
            }
            ?>


        </table>
    </div>
    </div>
    <?php if ($event){ 
        if ($type=='event'){
        ?>
    <?php $eventGearSetUrl = Url::to(['warehouse/assign-gear-set', 'id'=>$event->id, 'type'=>$type, 'item'=>$item, 'type2'=>$type2, 'packlist'=>$packlist->id]);?>
    <script type="text/javascript">
        function addGearSet(set_id)
        {
            var data = {
            set_id : set_id,
            }
            start = $("#warehouse_start").val();
            end = $("#warehouse_end").val();
            $.post("<?=$eventGearSetUrl?>&start="+start+'&end='+end, data, function(response){
                    if (response.responses) {
                        for (var i = 0; i < response.responses.length; i++) {
                                <?php if ($type=='offer'){ ?>
                                $("body").find("[data-key='" + response.responses[i].id + "']").find("#offergear-quantity").first().val(response.responses[i].total);
                                <?php } ?>
                            if (response.responses[i].success==1)
                            {
                                toastr.success("<?=Yii::t('app', 'Dodano')?> "+response.responses[i].name+" "+response.responses[i].quantity+" <?=Yii::t('app', 'szt.')?> ");
                            }
                            else
                            {
                                 var error = [response.responses[i].error];
                                toastr.error(response.responses[i].name+" "+error);                               
                            }

                        }
                    }                
            });
        }
    </script>
    <?php }else{ ?>
    <?php $eventGearSetUrl = Url::to(['warehouse/assign-gear-set', 'id'=>$event->id, 'type'=>$type, 'item'=>$item, 'type2'=>$type2]);?>
    <script type="text/javascript">
        function addGearSet(set_id)
        {
            var data = {
            set_id : set_id,
            }
            $.post("<?=$eventGearSetUrl?>", data, function(response){
                    if (response.responses) {
                        for (var i = 0; i < response.responses.length; i++) {
                                <?php if ($type=='offer'){ ?>
                                $("body").find("[data-key='" + response.responses[i].id + "']").find("#offergear-quantity").first().val(response.responses[i].total);
                                <?php } ?>
                            if (response.responses[i].success==1)
                            {
                                toastr.success("<?=Yii::t('app', 'Dodano')?> "+response.responses[i].name+" "+response.responses[i].quantity+" <?=Yii::t('app', 'szt.')?> ");
                            }
                            else
                            {
                                 var error = [response.responses[i].error];
                                toastr.error(response.responses[i].name+" "+error);                               
                            }

                        }
                    }                
            });
        }
    </script>
    <?php } ?>
    <?php } ?>
    <?php

    $this->registerJs('

    $("body").on("click", ".row-warehouse-out", function(){
        if ($(this).hasClass("glyphicon-arrow-down")) {
            $(this).parent().parent().next().slideDown();
        }
        else {
            $(this).parent().parent().next().slideUp();
        }
        $(this).toggleClass("glyphicon-arrow-up");
        $(this).toggleClass("glyphicon-arrow-down");
    });');
 } ?>
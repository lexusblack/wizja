<?php
use common\models\GearService;
use common\models\GearItem;
use common\models\Event;
use yii\bootstrap\Html;
use yii\helpers\Url;

?>
    <div class="row">
        <div class="col-md-12">
        <p><?=Yii::t('app', 'Co chcesz zrobić ze zwolnionym sprzętem?')?></p>
        <div class="ibox">
        <div class="ibox-content">

        <?= Html::a(Yii::t('app', 'Zwróć na magazyn'), '#', ['class' => 'btn btn-info', 'onclick'=>'$("#conflicts_modal").modal("hide"); return false;']) ?>
        </div>
    </div>
            </div>
    </div>
<?php 
$eventGearConnectedUrl = Url::to(['warehouse/assign-gear-conflicted', 'type'=>'event']);
?>
<script type="text/javascript">
    function bookNewGear()
    {
        $(".conflict_book").each(function(){
            quantity = $(this).val();
            event_id = $(this).data('event');
            if (!isNaN(quantity)){
                quantity = parseInt(quantity);
                var data = {
                gear_id : <?=$gear_id?>,
                quantity: quantity
                }
                $.post("<?=$eventGearConnectedUrl?>&id="+event_id, data, function(response){
                        if (response.responses) {
                            for (var i = 0; i < response.responses.length; i++) {
                                if (response.responses[i].success==1)
                                {
                                    toastr.success("<?=Yii::t('app', 'Przeniesiono rezerwację')?> "+response.responses[i].name+" "+response.responses[i].quantity+" <?=Yii::t('app', 'szt.')?> ");
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
        });
        $("#conflicts_modal").modal("hide");
    }
</script>
<?php
use common\models\GearService;
use common\models\GearItem;
use common\models\Event;
use yii\bootstrap\Html;
use yii\helpers\Url;
if (isset($conflict))
{
    $quantity = $conflict->added+$conflict->quantity;
}else{
    $quantity = $eg->quantity;
}

if ($type=='event')
{
    $event = $eg->event;
    $event_url = ['event/view', 'id'=>$eg->event_id];
}else{
    $event = $eg->rent;
    $event_url = ['rent/view', 'id'=>$eg->rent_id];
}
?>
    <div class="row">
        <div class="col-md-12">
        <p><?=Yii::t('app', 'Rezerwacja sprzętu na wydarzenie:').$event->name?></p>
        <form id="editConflictForm" class="form-inline">
        <input name="quantity" type="number" value=<?=$quantity?> min="0" max="<?=$quantity?>" class="form-control" id="editBooking"/>

        <?= Html::a(Yii::t('app', 'Zmień rezerwację'), '#', ['class' => 'btn btn-primary btn-xs', 'onclick'=>'changeBooking('.$gear_id.', '.$event_id.'); return false;']) ?>
        <?= Html::a(Yii::t('app', 'Usuń rezerwację'), '#', ['class' => 'btn btn-danger btn-xs', 'onclick'=>'deleteBooking('.$gear_id.', '.$event_id.'); return false;']) ?>
        <?= Html::a(Yii::t('app', 'Pokaż wydarzenie'), $event_url, ['class' => 'btn btn-success btn-xs', 'target'=>'_blank']) ?>
        <?= Html::a(Yii::t('app', 'Anuluj'), '#', ['class' => 'btn btn-default btn-xs', 'onclick'=>'$("#conflict_modal").modal("hide"); return false;']) ?>
                </form>
        </div>
    </div>
<?php
$checkurl = Url::to(['warehouse/check-conflict', 'conflict_id'=>$conflict_id]);
$deleteBookingUrl = Url::to(['warehouse/remove-gear', 'type'=>$type, 'no_item'=>1, 'id'=>$event_id]);
$changeBookingUrl = Url::to(['warehouse/change-booking']);

?>

<script type="text/javascript">
    function deleteBooking(gear, event)
    {
                $.post("<?=$deleteBookingUrl?>", {'itemid':gear, 'name':''}, function(response){
                $("#conflict_modal").modal("hide");
                reloadCalendar();
                <?php 
                    if ((isset($conflict))&&($conflict_id==$conflict->id)){ ?>
                        location.reload();
                    <?php }else{ ?>
                $.post('<?=$checkurl?>', {}, function(response){
                                    if (response.success==1)
                                    {
                                        showResolveConflict();
                                    }
                                     if (response.success==2)
                                    {
                                        showResolvePartial();
                                    } 
                });
                <?php } ?>
        });
    }

    function changeBooking(gear, event)
    {
                $.post("<?=$changeBookingUrl?>", {'gear_id':gear, 'event_id':event, 'quantity':$("#editBooking").val(), 'type':'<?=$type?>'}, function(response){
            <?php 
                    if (isset($conflict)){ ?>
                if (event==<?=$conflict->event_id?>)
                {
                    if (response.noconflict)
                    {
                        location.reload();
                    }
                }
            <?php } ?>
                $("#conflict_modal").modal("hide");
                reloadCalendar();
                $.post('<?=$checkurl?>', {}, function(response){
                                    if (response.success==1)
                                    {
                                        showResolveConflict();
                                    }
                                     if (response.success==2)
                                    {
                                        showResolvePartial();
                                    } 
                });
        });        
    }
</script>

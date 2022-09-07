<?php

use common\models\Customer;
use common\models\Rent;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Event;
use \common\models\OutcomesWarehouse;
use yii\bootstrap\Modal;


Modal::begin([
    'header' => Yii::t('app', 'Czytniki NEIS'),
    'id' => 'readers_modal',
    'class'=>'inmodal inmodal',
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
Modal::begin([
    'header' => Yii::t('app', 'Wystąpiły błędy'),
    'id' => 'errors_modal',
    'class'=>'inmodal inmodal',
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
/* @var $this yii\web\View */
/* @var $model common\models\OutcomesWarehouse */
/* @var $form yii\widgets\ActiveForm */
/* @var $modelsGear backend\models\OutcomesGearGeneral */

\kartik\growl\GrowlAsset::register($this);
\kartik\base\AnimateAsset::register($this);
$user = Yii::$app->user;

if (!$rent) {
    echo Html::a(Yii::t('app', 'Powrót'), ['event/view', 'id' => $event, '#' => 'tab-gear'], ['class' => 'btn btn-warning', 'style' => 'margin-right: 5px;']);
}
else {
    echo Html::a(Yii::t('app', 'Powrót'), ['rent/view', 'id' => $rent], ['class' => 'btn btn-warning', 'style' => 'margin-right: 5px;']);
}
echo Html::a(Yii::t('app', 'Zacznij skanować'), '#', ['class' => 'btn btn-primary', 'style' => 'margin-right: 5px;', 'onclick'=>'$("#code-input").focus(); return false;']);
if ($user->can('gearRfid')){
echo Html::a(Yii::t('app', 'Start NEIS'), '#', ['class' => 'btn btn-success', 'style' => 'margin-right: 5px;', 'onclick'=>'start_rfid(); return false;']);
echo Html::a(Yii::t('app', 'Stop NEIS'), '#', ['class' => 'btn btn-danger', 'style' => 'margin-right: 5px;', 'onclick'=>'stop_rfid(); return false;']);
}
?>

    <div class="outcomes-warehouse-form">

        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']);

        if ($event) {
            $model->event_type = 1;
            $model->event_id = $event;
            $e_id = $packlist_id;
            $type = 'event';


        }
        if ($rent) {
            $model->event_type = 2;
            $model->rent_id = $rent;
            $e_id = $rent;
            $type = 'rent';
        }   

        ?>

        <div id='warehouse_id_wrapper'>
            <?php echo $form->field($model, 'warehouse_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Warehouse::getList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz magazyn...'),

                ],
                'pluginOptions' => [
                    'allowClear' => false,
                    'multiple' => false,
                ],
            ])->label(Yii::t('app', 'Wybierz magazyn...'));
            ?>
        </div>

        <?php

        echo $form->field($model, 'gear')->textInput(['placeholder' => Yii::t('app', 'Szukaj sprzętu'), 'id' => 'code-input'])->label(Yii::t('app', "Wprowadź kod sprzętu"));
         ?>




    </div>

<?php
echo $this->render('_summaryTable',[
    'event' => $event,
    'rent' => $rent,
    'packlist_id'=>$packlist_id,
    'model'=>$model
]);
echo $form->field($model, 'comments')->textarea(['rows' => 6]);
echo   $form->field($model, 'items')->hiddenInput()->label(false);
echo   $form->field($model, 'groups')->hiddenInput()->label(false);
?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Wydaj') , ['class' => $model->isNewRecord ? 'btn btn-success category-menu-link' : 'btn btn-primary category-menu-link', 'id'=>'outcomes-button']) ?>
        </div>

<?php
echo $this->render("assign", ['warehouse' => $warehouse, 'event' => $event, 'rent' => $rent]);

?>


<?php ActiveForm::end();



$outgoingGearAddUnplanned = '';
if (Yii::$app->user->can('gearWarehouseOutcomesAddUnplannedGear')) {
    $outgoingGearAddUnplanned = "
                if (error) {
                var proceed = confirm(alertText);
                if (!proceed) {
                    return false;
                }
            }
    ";
}
else {
    $outgoingGearAddUnplanned = "if (error) { alert('".Yii::t('app', 'Nie masz uprawnień, aby wydać nieplanowany sprzęt')."'); return false; }";
}

$this->registerJs('

    $("#dynamic-form").on("beforeSubmit", function(e){
        if ((items.length==0)&&(groups.length==0))
        {
                alert("'.Yii::t('app', 'Nie dodałeś żadnego sprzętu.').'");
                return false;
        }else{
            $("#outcomeswarehouse-items").val(JSON.stringify(items));
            $("#outcomeswarehouse-groups").val(JSON.stringify(groups));
            $("outcomes-button").hide();
                var error = false;
                
            $.ajax({
                    type: "POST",
                    url: "' . Url::to(['outcomes-warehouse/check-gears', 'type'=>$type, 'event_id'=>$e_id]) . '&w="+$("#outcomeswarehouse-warehouse_id").val(),
                    data: $("#dynamic-form").serialize(),
                    async: false,
                    success: function(data){
                        //tutaj info i odpowiedni modal co zrobić - opcje usuń pozycje i ponów lub anuluj
                        if (data.error==1)
                        {
                            error = true;
                            var modal = $("#errors_modal");
                            if (data.gears.length>0)
                            {
                                modal.find(".modalContent").empty().append("<p>W magazynie brakuje poniższych sprzętów, usuń je z listy do wydania i spróbuj ponownie.</p>");
                                for (var i=0; i<data.gears.length; i++)
                                {
                                    if (data.gears[i].gear.gear.no_items==1)
                                            modal.find(".modalContent").append("<p>"+data.gears[i].name+" "+data.gears[i].missing+" szt.</p>");
                                        else
                                            modal.find(".modalContent").append("<p>"+data.gears[i].name+" numer "+data.gears[i].gear.number+"</p>");
                                }
                            }
                            if (data.unplanned.length>0)
                            {
                                modal.find(".modalContent").append("<p>Ten sprzęt nie był zaplanowany na event, usuń go z listy i spróbuj ponownie</p>");
                                for (var i=0; i<data.unplanned.length; i++)
                                {
                                            modal.find(".modalContent").append("<p>"+data.unplanned[i].name+" "+data.unplanned[i].more+" szt.</p>");
                                }
                            }
                            modal.modal("show");
                            $("outcomes-button").show();

                        }
                    }    
                });
				
        if (error)
                return false;
        }

    });

    $("#code-input")[0].addEventListener("keydown", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
        var value = $("#code-input").val();
            
                var url = "' . Url::to(["outcomes-warehouse/gear-list"]) . '?q=" + value+"&w="+$("#outcomeswarehouse-warehouse_id").val();
            
                $.ajax({
                    url: url,
                    type: "post",
                    async: false,
                    success: function(data) {
                        if (data.error) {
                            $.notify(
                                {
                                    message: data.error,
                                }, 
                                {
                                    type: "danger",
                                }
                            );
                        }
                        if (data.ok) {
                            $.notify(
                                {
                                    message: "Dodano sprzęt: " + data.name,
                                }, 
                                {
                                    type: "success",
                                }
                            );
                            if (data.group) {
                                addGearGroup(data.group);
                                $(".checkbox-group[value="+data.group+"]").prop("checked", true);
                            }
                            if (data.item) {
                                if (data.no_items==1)
                                {
                                    addGearNoItems(data.item, null);
                                }else{
                                    addRowGearItem(data.item);
                                }
                                
                                $(".checkbox-item-id[value="+data.item+"]").prop("checked", true);
                            }
                        }
                    },
                    error: function(data) {
                            $.notify(
                                {
                                    message: "'.Yii::t('app', 'Problem z połączeniem, spróbuj ponownie').'",
                                }, 
                                {
                                    type: "danger",
                                }
                            );
                    }
                });
                $("#code-input").val(null);
            }
            return false;
    });
');

?>
<script type="text/javascript">

    var reader_id_global = "";
    function start_rfid()
    {
                                                $.ajax({
                                                    type: 'POST',
                                                    url: '/admin/rfid-command/get-readers',
                                                    success: function(response) {
                                                       //pokazujemy modal, że trwa szukanie czytników
                                                       //odpalamy funkcję czekania na odpowiedź
                                                       var modal = $("#readers_modal");
                                                        modal.find(".modalContent").empty().append("<?=Yii::t('app', 'Trwa szukanie czytników...')?>");
                                                        modal.modal("show");
                                                        getReadersAnswer(response.id);
                                                    }
                                                });
    }

    function register_reader(reader_id, status)
    {
        reader_id_global = reader_id
        if (status==1)
        {
            start_reading(reader_id);
        }else{
            $.ajax({
                                                    type: 'POST',
                                                    url: '/admin/rfid-command/register-reader?reader='+reader_id,
                                                    success: function(response) {
                                                       var modal = $("#readers_modal");
                                                        modal.find(".modalContent").empty().append("<?=Yii::t('app', 'Trwa rejestracja czytnika...')?>");
                                                        modal.modal("show");
                                                        getRegisterAnswer(response.id);
                                                    }
                                                });
        }
        
    }

    function start_reading(reader_id)
    {
        $.ajax({
                                                    type: 'POST',
                                                    url: '/admin/rfid-command/start-reading?reader='+reader_id,
                                                    success: function(response) {
                                                       var modal = $("#readers_modal");
                                                        modal.find(".modalContent").empty().append("<?=Yii::t('app', 'Trwa włączanie czytnika...')?>");
                                                        modal.modal("show");
                                                        getStartAnswer(response.id);
                                                    }
                                                });
    }

    function stop_rfid()
    {
                                                $.ajax({
                                                    type: 'POST',
                                                    url: '/admin/rfid-command/stop-reading?id='+reader_id_global,
                                                    success: function(response) {
                                                       toastr.success('<?=Yii::t('app', 'Zatrzymano')?>');
                                                    }
                                                });
    }

    function getStartAnswer(id)
    {
        $.ajax({
                                                    type: 'POST',
                                                    url: '/admin/rfid-command/get-readers-answer?id='+id,
                                                    success: function(response) {
                                                       if (response.status>0)
                                                       {
                                                        var modal = $("#readers_modal");
                                                        modal.find(".modalContent").empty();
                                                        if (response.status==2)
                                                        {
                                                             modal.find(".modalContent").append("<?=Yii::t('app', 'Błąd rejestracji czytnika :(')?>");
                                                        }else{
                                                            modal.modal("hide");
                                                            toastr.success('<?=Yii::t('app', 'Mozesz rozpocząć skanowanie.')?>');
                                                        }

                                                       }else{
                                                        sleep(1000).then(() => {
                                                            getStartAnswer(id);
                                                        });
                                                       }
                                                        
                                                    }
                                                });
    }

    function getRegisterAnswer(id)
    {
        $.ajax({
                                                    type: 'POST',
                                                    url: '/admin/rfid-command/get-readers-answer?id='+id,
                                                    success: function(response) {
                                                       if (response.status>0)
                                                       {
                                                        var modal = $("#readers_modal");
                                                        modal.find(".modalContent").empty();
                                                        if (response.status==2)
                                                        {
                                                             //modal.find(".modalContent").append("<?=Yii::t('app', 'Błąd rejestracji czytnika :(')?>");
                                                             start_reading(response.id);
                                                        }else{
                                                            start_reading(response.id);
                                                        }

                                                       }else{
                                                        sleep(1000).then(() => {
                                                            getRegisterAnswer(id);
                                                        });
                                                       }
                                                        
                                                    }
                                                });
    }

    function getReadersAnswer(id)
    {
        $.ajax({
                                                    type: 'POST',
                                                    url: '/admin/rfid-command/get-readers-answer?id='+id,
                                                    success: function(response) {
                                                       if (response.status>0)
                                                       {
                                                        var modal = $("#readers_modal");
                                                        modal.find(".modalContent").empty();
                                                        readers = response.readers;
                                                        if (response.status==2)
                                                        {
                                                             modal.find(".modalContent").append("<?=Yii::t('app', 'Brak podłączonych czytników NEIS lub wystąpił nieoczekiwany błąd')?>");
                                                        }else{
                                                            for (i=0; i<readers.length; i++)
                                                            {
                                                                modal.find(".modalContent").append("<p><a href='#' class='btn btn-success btn-xs' onclick='register_reader(\""+readers[i].id+"\", "+readers[i].status+"); return false;'><?=Yii::t('app', 'Uruchom i zacznij skanować')?></a> "+readers[i].name+" ("+readers[i].id+") </p></br>");
                                                            }
                                                        }

                                                       }else{
                                                        sleep(1000).then(() => {
                                                            getReadersAnswer(id);
                                                        });
                                                       }
                                                        
                                                    }
                                                });
    }

    const sleep = (milliseconds) => {
        return new Promise(resolve => setTimeout(resolve, milliseconds))
    }
</script>


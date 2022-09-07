<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Event;
use common\models\Customer;
use common\models\Rent;

/* @var $this yii\web\View */
/* @var $model common\models\IncomesWarehouse */
/* @var $form yii\widgets\ActiveForm */


\kartik\growl\GrowlAsset::register($this);
\kartik\base\AnimateAsset::register($this);
$eventas = $event;
$rentas = $rent;
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
/*
if (isset($_GET['onlyEvent']) && $_GET['onlyEvent']) {
    echo " " . Html::a(Yii::t('app', 'Sprzęt z wszystkich eventów'), array_merge(['create'], $_GET, ['onlyEvent' => false]), ['class' => 'btn btn-success category-menu-link']);
}
else {
    echo " " . Html::a(Yii::t('app', 'Sprzęt z tego eventu'), array_merge(['create'], $_GET, ['onlyEvent' => true]), ['class' => 'btn btn-success category-menu-link']);
}
*/
?>

    <div class="incomes-warehouse-form">

        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']);

        if ($event) {
            $model->event_type = 1;
            $model->event_id = $event;
            $e = \common\models\Event::findOne($event);
            $packlist = \common\models\Packlist::findOne($packlist_id);
            $end = $packlist->end_time;
        }
        if ($rent) {
            $model->event_type = 2;
            $model->rent_id = $rent;
            $e = \common\models\Rent::findOne($rent);
            $end = $e->getTimeEnd();
        }
        if ($customer) {
            $model->customer_id = $customer;
        }

        $events = Event::find()->all();
        foreach ($events as $event) {
            $event_dropdown[$event->id] = $event->name;
        }

        $rents = Rent::find()->all();
        foreach ($rents as $rent) {
            $rent_dropdown[$rent->id] = $rent->name;
        }

        $customers = Customer::find()->all();
        foreach ($customers as $customer) {
            $customer_dropdown[$customer->id] = $customer->name;
        }
        ?>

        <div style="display: none;">
            <?= $form->field($model, 'event_type')->dropDownList(\common\models\OutcomesWarehouse::getEventType(), ['id' => 'event_type'])->label(Yii::t('app', 'Rodzaj wydarzrenia')); ?>
        </div>

        <div id='event_id_wrapper' style='display: none;'>
            <?= $form->field($model, 'event_id')->widget(Select2::className(), ['data' => $event_dropdown, 'options' => ['placeholder' => Yii::t('app', 'Znajdź event'),],])->label(Yii::t('app', "Wybierz event")); ?>
        </div>

        <div id='rent_id_wrapper' style='display: none;'>
            <?= $form->field($model, 'rent_id')->widget(Select2::className(), ['data' => $rent_dropdown, 'options' => ['placeholder' => Yii::t('app', 'Znajdź wypożyczenie'),]])->label(Yii::t('app', "Wybierz wypożyczenie")); ?>
        </div>

        <div id='customer_id_wrapper' style='display: none;'>
            <?= $form->field($model, 'customer_id')->widget(Select2::className(), ['data' => $customer_dropdown, 'options' => ['placeholder' => Yii::t('app', 'Znajdź klienta'),],])->label(Yii::t('app', "Wybierz klienta")); ?>
        </div>

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
        echo $form->field($model, 'comments')->textarea(['rows' => 6]);
        echo   $form->field($model, 'items')->hiddenInput()->label(false);
        echo   $form->field($model, 'groups')->hiddenInput()->label(false);

        ?>
        <?php 
        if (isset($_GET['onlyEvent']) && $_GET['onlyEvent']) {

            if (date('Y-m-d H:i:s')<$e->getTimeEnd()){ $model->shorten = 1; ?>
                <div class="alert alert-warning">
                    <?=Yii::t('app', 'Zwracasz sprzęt przed końcem rezerwacji - ').$e->getTimeEnd() ?>
                    <?php echo $form->field($model, 'shorten')->dropDownList([1=>Yii::t('app', 'Tak'), 0=>Yii::t('app', 'Nie')])->label(Yii::t('app', 'Czy skrócić rezerwację przyjmowanych sprzętów')) ?>

                </div>
        <?php    }
        } ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Przyjmij') : Yii::t('app', 'Aktualizuj'), ['class' => $model->isNewRecord ? 'btn btn-success category-menu-link' : 'btn btn-primary category-menu-link', 'id'=>'incomes-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>


        <?=  $this->render('_summaryTable',[
            'event' => $eventas,
            'rent' => $rentas,
            'onlyEvent'=>$onlyEvent,
            'packlist_id'=>$packlist_id
        ]); ?>

<?php        

echo $this->render("assign", [ 'event' => $event]); ?>


<?php

$this->registerJs('

    $("#dynamic-form").on("beforeSubmit", function(e){
        $("#incomeswarehouse-items").val(JSON.stringify(items));
        $("#incomeswarehouse-groups").val(JSON.stringify(groups));
        $("incomes-button").prop("disabled", true);
        return true;
    });

        $("#code-input")[0].addEventListener("keydown", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
        var value = $("#code-input").val();
        
            var url = "' . Url::to(["incomes-warehouse/gear-list", 'event_id'=>$eventas, 'rent_id'=>$rentas]) . '&q=" + value;
        
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
                                $(".checkbox-item-id[value="+data.item+"]").prop("checked", true);
                            }
                            
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
    
$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});

');
?>
<script type="text/javascript">
    function start_rfid()
    {
                                                $.ajax({
                                                    type: 'POST',
                                                    url: '/admin/rfid-command/start-reading',
                                                    success: function(response) {
                                                       toastr.success('<?=Yii::t('app', 'Wystartowano')?>');
                                                    }
                                                });
    }

    function stop_rfid()
    {
                                                $.ajax({
                                                    type: 'POST',
                                                    url: '/admin/rfid-command/stop-reading',
                                                    success: function(response) {
                                                       toastr.success('<?=Yii::t('app', 'Zatrzymano')?>');
                                                    }
                                                });
    }
</script>
<?php


use common\models\Customer;
use common\models\OutcomesWarehouse;
use common\models\Rent;
use common\models\Event;
use kartik\widgets\Select2;

\kartik\growl\GrowlAsset::register($this);
\kartik\base\AnimateAsset::register($this);

$this->title = Yii::t('app', 'Wybierz rodzaj eventu');

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

$display_customer = 'none';
$display_event = 'block';
$display_rent = 'none';

?>
    <h3><?= Yii::t('app', 'Wybierz') ?></h3>
    <label for="event_type" style="font-weight: normal;"> <?= Yii::t('app', 'Rodzaj imprezy') ?>:
        <select id="event_type" class="form-control">
            <option value="<?= OutcomesWarehouse::EVENT_TYPE_EVENT ?>"><?= Yii::t('app', 'Event') ?></option>
            <option value="<?= OutcomesWarehouse::EVENT_TYPE_RENT ?>"><?= Yii::t('app', 'Wypożyczenie') ?></option>
        </select>
    </label>

    <div id='event_id_wrapper' style='display: <?= $display_event ?>;'>
        <?= Select2::widget(['name' => 'event_id', 'data' => $event_dropdown, 'id' => 'select_event_id', 'options' => ['placeholder' => Yii::t('app', 'Znajdź event'),],]); ?>
    </div>

    <div id='rent_id_wrapper' style='display: <?= $display_rent ?>;'>
        <?= Select2::widget(['name' => 'rent_id', 'data' => $rent_dropdown, 'id' => 'select_rent_id', 'options' => ['placeholder' => Yii::t('app', 'Znajdź wypożyczenie'),]]); ?>
    </div>

    <div id='customer_id_wrapper' style='display: <?= $display_customer ?>;'>
        <?= Select2::widget(['name' => 'customer_id', 'data' => $customer_dropdown, 'pluginOptions' => ['tags'], 'id' => 'select_customer_id', 'options' => ['placeholder' => Yii::t('app', 'Znajdź klienta'),

        ],]); ?>
    </div>

    <div>
        <Button class="btn btn-success" id="next"><?= Yii::t('app', 'Dalej') ?></Button>
    </div>

<?php

$this->registerJs('

    $("#event_type").change(function(){
        var id = $(this).val();
        if (id == ' . OutcomesWarehouse::EVENT_TYPE_NONE . ') {
            showCustomerId();
        }
        else if (id == ' . OutcomesWarehouse::EVENT_TYPE_EVENT . ') {
            showEventId();
        }
        else if ( id == ' . OutcomesWarehouse::EVENT_TYPE_RENT . ') {
            showRentId();
        }
    });

    var rent_wrapper = $("#rent_id_wrapper");
    var customer_wrapper = $("#customer_id_wrapper");
    var event_wrapper = $("#event_id_wrapper");

    function showEventId() {
        rent_wrapper.slideUp();
        customer_wrapper.slideUp();
        event_wrapper.slideDown();
    }
    function showCustomerId() {
        rent_wrapper.slideUp();
        customer_wrapper.slideDown();
        event_wrapper.slideUp();
    }
    function showRentId() {
        rent_wrapper.slideDown();
        customer_wrapper.slideUp();
        event_wrapper.slideUp();
    }

    $("#next").click(function(){
        var event_type = $("#event_type").val();
        if (event_type == ' . OutcomesWarehouse::EVENT_TYPE_NONE . ') {
            var customer = $("#select_customer_id").val();
            if (customer == "") {
                $.notify({message: "'.Yii::t('app', 'Nie wybrano klienta, dla którego chcesz wydać sprzęt').'",},{type: "danger"});
            }
            else {
                location.href = "create?customer=" + customer;
            }
        }
        if (event_type == ' . OutcomesWarehouse::EVENT_TYPE_EVENT . ') {
            var event = $("#select_event_id").val();
            if (event == "") {
                $.notify({message: "'.Yii::t('app', 'Nie wybrano eventu, dla którego chcesz wydać sprzęt').'",},{type: "danger"});
            }
            else {
                location.href = "create?event=" + event;
            }
        }
        if (event_type == ' . OutcomesWarehouse::EVENT_TYPE_RENT . ') {
            var rent = $("#select_rent_id").val();
            if (rent == "") {
                $.notify({message: "'.Yii::t('app', 'Nie wybrano wypożyczenia, dla którego chcesz wydać sprzęt').'",},{type: "danger"});
            }
            else {
                location.href = "create?rent=" + rent;
            }                
        }
    });

');
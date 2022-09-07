<?php
use yii\bootstrap\Html;
?>

<div ng-controller="flotaController" class="planboard_drug_box" style="height: 600px; overflow-y: scroll; width:170px;">
        <ul class="weekdays_list">
            <li ng-repeat="date in planboard_data_range" class="weekday_name">
                {{ date | formatToShortWeek}}
            </li>
        </ul>
        <ul class="event_list">
            <li ng-repeat="data in planboard_schedules" class="weekday_name" style="width:{{100/planboard_schedules.length}}%; background-color: {{data.color}};">
                {{ data.prefix}}
            </li>
        </ul>
	<ul class="draggable_vehicles" id="draggable_vehicles">
	    <li class="left_panel_flota" ng-repeat="item in flota | filter: vehicle_search" style="{{ item | getDateBG }}" data-carID="{{item.vehicle.id}}" draggablevehicle>
            <div class="day0">
                <div class="day1">
                    <div class="day2">
                        <div class="day3">
                            <div class="day4">
                                <div class="day5">
                                    <div class="day6">
                                        {{item.vehicle.name}} ({{item.vehicle.registration_number}})
                                        <?= Html::a(Html::icon('trash'),'#',['class' => 'pull-right delete_event_vehicle']);?>
                                        <?= Html::a(Html::icon('edit'),'#',['class' => 'pull-right open_vehicle_modal']);?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
	    </li>
	</ul>
</div>

<?php


$this->registerJs('

$("#draggable_vehicles").on("click", "li", function(){
    var id = $(this).data("carid");
    $(".fc-content-skeleton").find("li").each(function(){
        if ($(this).data("carid") == id) {
            $(this).toggleClass("backlight-car");
        }
    });
    $("#visualization").find("li").each(function(){
        if ($(this).data("carid") == id) {
            $(this).toggleClass("backlight-car");
        }
    });
});


');



$this->registerCss('

.backlight-car {
    background-color: #ff5c00 !important;
}
.backlight-car .time_period {
   opacity: 0.6;
}

.activeDayHeader {
    background-color: #4ac74f;
    color: white;
}

.day0.active {
    background: -webkit-linear-gradient(left, red 0%, transparent 1%, transparent 13.29%, red 14.29%, transparent 14.29%);
}
.day1.active {
   background: -webkit-linear-gradient(left, transparent 13.29%, red 14.29%, transparent 14.29%, transparent 27.58%, red 28.58%, transparent 28.58%);
}
.day2.active {
   background: -webkit-linear-gradient(left, transparent 27.58%, red 28.58%, transparent 28.58%, transparent 41.87%, red 42.87%, transparent 42.87%);
}
.day3.active {
   background: -webkit-linear-gradient(left, transparent 41.87%, red 42.87%, transparent 42.87%, transparent 56.16%, red 57.16%, transparent 57.16%);
}
.day4.active {
   background: -webkit-linear-gradient(left, transparent 56.16%, red 57.16%, transparent 57.16%, transparent 70.45%, red 71.45%, transparent 71.45%);
}
.day5.active {
   background: -webkit-linear-gradient(left, transparent 70.45%, red 71.45%, transparent 71.45%, transparent 84.74%, red 85.74%, transparent 85.74%);
}
.day6.active {
   background: -webkit-linear-gradient(left, transparent 84.74%, red 85.74%, transparent 85.74%, transparent 99%, red 100%);
}

.backlight-user {
    background-color: lime !important;
}

.tab_ekipa_form {
    display: block;
    width: 100%;
    background-image: none;
}

');
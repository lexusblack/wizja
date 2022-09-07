<?php
use yii\bootstrap\Html;

?>
<a href="#" class="legend-show btn btn-xs btn-default"><i class="fa fa-angle-double-down"></i><?=Yii::t('app', 'Legenda')?></a>
<div class="row legend" style="display:none;"><div class="col-sm-12">
<p><span class="label" style="background-color:#4ac74f;"> </span>  <?=Yii::t('app', 'Dostępny')?></br/>
<span class="label" style="background-color:#c4edc6;"> </span>  <?=Yii::t('app', 'Zajęty pow. 4h')?><br/>
<span class="label" style="background-color:#cccccc;"> </span>  <?=Yii::t('app', 'Zajęty')?><br/>
<span class="label" style="background-color:#ffff00;"> </span>  <?=Yii::t('app', 'Wniosek urlopowy')?><br/>
<span class="label" style="background-color:#ff0f17;"> </span>  <?=Yii::t('app', 'Urlop')?></p>
</div></div>

    <input class="tab_ekipa_form" type="text" ng-model="user_query">

    <select class="tab_ekipa_form" style="max-width:170px;" ng-model="ekipa_filter.type" ng-options="item.type as item.name for item in user_type_option"></select>

    <select class="tab_ekipa_form"  style="max-width:170px;" ng-model="ekipa_filter.departments.id" ng-options="item.id as item.name for item in departments"></select>

    <select class="tab_ekipa_form"  style="max-width:170px;"  ng-model="ekipa_filter.skills.id" ng-options="item.id as item.name for item in skills"></select>

    <div ng-controller="ekipaController" class="planboard_drug_box" style="height: 600px; overflow-y: auto;">
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
        <ul class="draggable_users" id="draggable_users">
            <li class="left_panel_users"
                ng-repeat="data in ekipa | filter:ekipa_filter:true | filter:user_search | filter:user_available"
                style="{{ data | getDateBG }}" data-userID="{{data.id}}" draggableuser>
                <div class="day0">
                    <div class="day1">
                        <div class="day2">
                            <div class="day3">
                                <div class="day4">
                                    <div class="day5">
                                        <div class="day6">
                                            {{data.last_name}} {{data.first_name}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div style="height: 500px; width: 50px;"></div>
    </div>

<?php


$this->registerJs('

$("#draggable_users").on("click", "li", function(){
    var id = $(this).data("userid");
    $(".fc-content-skeleton").find("li").each(function(){
        if ($(this).data("userid") == id) {
            $(this).toggleClass("backlight-user");
        }
    });
    $("#visualization").find("li").each(function(){
        if ($(this).data("userid") == id) {
            $(this).toggleClass("backlight-user");
        }
    });
});


$("body").on("click", ".weekday_name", function() {
    $(this).toggleClass("activeDayHeader");
    var thisHtml = $(this).html();
    $(this).parent().children().each(function(index){
        if ($(this).html() == thisHtml) {
            $(".day" + index).each(function(){
                $(this).toggleClass("active");
            });
        }
    });
});

$(".legend-show").on("click", function(){
    if ($(this).hasClass("showed"))
    {
        $(".legend").hide();
    }else{
        $(".legend").show();
    }
    $(this).toggleClass("showed")
});

');


$this->registerCss('

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
.backlight-user .time_period {
   opacity: 0.6;
}

.tab_ekipa_form {
    display: block;
    width: 100%;
    background-image: none;
}


');
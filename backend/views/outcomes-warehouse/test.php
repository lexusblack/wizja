<?php
use common\helpers\Url;
use common\models\Event;
use kartik\tabs\TabsX;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;

\kartik\cmenu\ContextMenuAsset::register($this);
\common\assets\VisJsAsset::register($this);
\common\assets\PlanboardAsset::register($this);
\kartik\datetime\DateTimePickerAsset::register($this);

?>
<div id="loader">
    <div class="loader-spin"></div>
</div>

<script type="text/javascript">
    window.addEventListener('load', function (event) {
        $('#loader').fadeOut('slow');
        $('.ibox-content').fadeIn('slow');
        $('#planboardApp').fadeIn('slow');
    });
</script>
<div class="ibox">
<div class="ibox-content">
<!-- contextMenu --- User --- -->
<ul id="userMenu" class="dropdown-menu" role="menu" style="display:none" >
    <li><?=Html::a(Html::icon('edit').' '.Yii::t('app',"Edytuj"),[Url::to("planboard/user-form")],['class' => 'open_ekipa_modal']);?></li>
    <li class="divider"></li>
    <li><?=Html::a(Html::icon('trash').' '.Yii::t('app',"Usuń"), [Url::to('crew/assign-user')], ['class' => 'delete_event_user']);?></li>
</ul>

<!-- contextMenu --- Event --- -->
<ul id="eventMenu" class="dropdown-menu" role="menu" style="display:none" >
    <li><?=Html::a(Html::icon('pencil').' '.Yii::t('app',"Wydarzenie"),[Url::to('event/view')], ['class' => 'open_event']);?></li>
    <li><?=Html::a(Html::icon('pause').' '.Yii::t('app',"Przerwy"), [Url::to("planboard/event-breaks-form")], ['class' => 'open_breakes_modal']);?></li>
    <li><?=Html::a(Html::icon('film').' '.Yii::t('app',"Niestandardowe godziny pracy"), [Url::to("planboard/event-custom-working-hours-form")], ['class' => 'open_custom_hours_modal']);?></li>
    <li><?=Html::a(Html::icon('fullscreen').' '.Yii::t('app',"Pełen ekran"), null, ['class' => 'fullscreen']);?></li>
</ul>

<!-- contextMenu --- Vehicle --- -->
<ul id="vehicleMenu" class="dropdown-menu" role="menu" style="display:none" >
    <li><?=Html::a(Html::icon('edit').' '.Yii::t('app',"Edytuj"), [Url::to("planboard/vehicle-form")], ['class' => 'open_vehicle_modal']);?></li>
    <li><?=Html::a(Html::icon('trash').' '.Yii::t('app',"Usuń"), [Url::to('vehicle/assign-vehicle')], ['class' => 'delete_event_vehicle']);?></li>
</ul>


<?php

// --- Ekipa modal ---
Modal::begin([
    'header' => Yii::t('app', 'Ekipa'),
    'id' => 'ekipa_modal',
    'class'=>'inmodal inmodal',
    'size' => 'modal-lg',
]);
echo "<div class='modalContent'></div>";
Modal::end();

// --- Vehicle modal ---
Modal::begin([
    'header' => Yii::t('app', 'Flota'),
    'id' => 'vehicle_modal',
    'class' => 'modal'
]);
echo "<div class='modalContent'></div>";
Modal::end();

// --- Event breaks modal ---
Modal::begin([
    'header' => Yii::t('app', 'Przerwy'),
    'id' => 'event_breaks_modal',
    'class' => 'modal'
]);
echo "<div class='modalContent'></div>";
Modal::end();

// --- Event custom working hours modal ---
Modal::begin([
    'header' => Yii::t('app', 'Niestandardowe godziny pracy'),
    'id' => 'event_custom_working_hours_modal',
    'class' => 'modal'
]);
echo "<div class='modalContent'></div>";
Modal::end();

?>

<!-- Angular - menu -->
<div ng-app="planboardApp" id="planboardApp">

       <div class="row">
    <div class="col-sm-2" style="padding-right: 0; padding-left:0">
        <div   style="background-color:white;"  class="affix">
            <div class="row">
                <div class="col-sm-12">
                    <div class="glyphicon glyphicon-repeat reset" style="border: 1px solid black; padding: 2px; float: right; cursor: pointer;" ></div>
                    <div style="width: 5px; float: right; height: 5px; "></div>
                    <!--<div class="glyphicon glyphicon-move" style="border: 1px solid black; padding: 2px; float: right; cursor: pointer;" id="btn-move-panel"></div>-->
                </div>
            </div>
            <?php
            $items = [
                [
                    'label'=>Yii::t('app', 'Ekipa'),
                    'content'=> $this->render('@common/widgets/views/_planboardTabEkipa'),
                    'active'=>true,
                    'linkOptions' => ['class' => 'tab_link'],
                ],
                [
                    'label'=>Yii::t('app', 'Flota'),
                    'content'=> $this->render('@common/widgets/views/_planboardTabFlota'),
                    'linkOptions' => ['class' => 'tab_link'],
                ]
            ];

            echo TabsX::widget([
                'items'=>$items,
                'position'=>TabsX::POS_ABOVE,
                'encodeLabels'=>false
            ]); ?>
        </div>
    </div>

    <div class="col-sm-10" style="padding-left: 0; padding-right: 0;">

        <button id="zoom_in" class="btn btn-xs"><?=  Yii::t('app', 'Zoom in') ?></button>
        <button id="zoom_out" class="btn btn-xs"><?=  Yii::t('app', 'Zoom out') ?></button>

        <button id="move_left" class="btn btn-xs"><<<</button>

        <select id="period_number">
            <?php
                for($i = 1; $i <= 30; $i++) { ?>
                    <option value="<?= $i ?>"><?= $i ?></option><?php
                }
            ?>
        </select>
        <select id="period_text">
            <option value="1"><?=  Yii::t('app', 'Dzień') ?></option>
            <option value="2"><?=  Yii::t('app', 'Tydzień') ?></option>
            <option value="3"><?=  Yii::t('app', 'Miesiąc') ?></option>
        </select>

        <button id="move_right" class="btn btn-xs">>>></button>
<!--        <button id="toggle_time_helper">TimeHelper</button>-->
        <button id="toggle_time_helper2" class="on btn btn-xs"><?=  Yii::t('app', 'TimeHelper') ?></button>
        <div class="line-block">
            <div class="line-block">
                <?=  Yii::t('app', 'Przesuwanie') ?>:
            </div>
            <div class="onoffswitch">
                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="moveable">
                <label class="onoffswitch-label" for="moveable">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
            </div>
            <?php
            if (false){
                $display = 'none';
                if (Yii::$app->settings->get('eventNotifications', 'main') == Event::NOTIFICATIONS_OFF) {
                    $display = 'inline-block';
                }
                echo Html::a(Yii::t('app', 'Wyślij wszystkie powiadomienia'), ['event/send-all-events-notifications'], ['class' => 'btn btn-success btn-xs send-noti', 'style' => 'display: '.$display.';']);
            ?>
            <label>
                <input type="checkbox" class="notification-checkbox" <?php if (Yii::$app->settings->get('eventNotifications', 'main') == Event::NOTIFICATIONS_ON) { echo "checked"; } ?>> <?= Yii::t('app', 'Wysyłanie powiadomień dla eventów'); ?>
            </label>
            <?php } ?>
        </div>

        <!-- Timeline -->
        <div id="visualization">
            <!-- Time helper -->
            <div id="time_help_line"></div>
        </div>
    </div>
</div>


<div id="custom-time-bar-tooltip"></div>
</div>
</div>

<?php

$this->registerJs('
$(".send-noti").click(function(e){
    e.preventDefault();
    var el = $(this);
    $.post($(this).prop("href"), null, function(){
        alert("Powiadomienia zostały wysłane");
        el.hide("slow");
    });
}); 
$(".notification-checkbox").change(function(){
    var noti = 0;
    if ($(this).prop("checked")) {
        noti = 1;
    }
    if (!$(this).prop("checked")) {
        $(".send-noti").slideDown("slow");
    }
    else {
        $(".send-noti").slideUp("slow");
    }

    var data = { main: { eventNotifications: noti } };
    $.post("'.Url::toRoute(['setting/change-event-notifications']).'", data);
});
');

$this->registerCss("
    .ibox-content, #planboardApp { display: none; }
    
    .loader-spin {
        border: 16px solid gainsboro; 
        border-top: 16px solid #3498db; 
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
        margin: auto;
        margin-top: 100px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .vis-custom-time { width: 1px; }
    #custom-time-bar-tooltip { width: 120px; height: 30px; margin-left: -120px; top: -55px; left: 24%; padding: 5px; background-color: white; border: 1px solid black; position: absolute; z-index: 900; }

    .nav.nav-tabs a, .nav-tabs > li > a {padding: 10px 5px 10px 5px; margin-left: 5px; margin-top: 5px;}

    .vis-text.vis-major.vis-h0-h4.active_menu_days, .vis-text.vis-major.vis-h0.active_menu_days, .vis-text.vis-minor.active_menu_days { background-color: rgb(153, 204, 255);}
    .vis-text.vis-minor, .vis-text.vis-major { font-size: 8px;  text-align:center;}
    
    .col-lg-12 { padding-left: 0; }

    #planboardApp {margin-top: -15px}
    #visualization {margin-top: 5px;}

    #toggle_time_helper, #toggle_time_helper2 {border: 0; background-color: red; color: white;}
    #toggle_time_helper.on, #toggle_time_helper2.on {background-color: lime; color: black;}
    
    #time_help_line {position: absolute; left:0; top:5px; bottom:0; border-left:1px solid blue; z-index: 101; display:none;}
    #time_help_line.on {display:block;}

    .custom-time-bar { z-index: 900; }
    .custom-time-bar.off { display: none; }

    .vis-item.ui-sortable-helper { height: 0 !important; }
    .vis-item.ui-sortable-placeholder { display: none; }
    
    .vis-item-content { display: none !important; }
    .vis-item.vis-range { border: 0; }
    .vis-item .vis-item-content{background-color: #005AA0; color: white; padding: 0;}
    
    .event_time_wrapper { position:relative; height: 21px; width: 100%; background-color: #D9DDE2; border-bottom: 1px solid rgb(137,137,137); border-top: 1px solid rgb(137,137,137); overflow: hidden; }
    .time_period { position: absolute; height: 100%; font-size: 7px; line-height: 9px; } 
    .packing_period {background-color: #C7CACE; border-right: 1px solid rgb(137,137,137);}
    .montage_period {background-color: #C7CACE; border-right: 1px solid rgb(137,137,137);}
    .event_period { background-color: #C7CACE; border-right: 1px solid rgb(137,137,137); }
    .work_period { background-color: #aaa; }
    .work_period.second-work-period { background-color: #E7EAEE; padding:2px; overflow: hidden; text-overflow: ellipsis; }
    .break_period { background-color: #F06C5F; line-height: 14px; text-align: center; color: white; }
    .disessembly_period { background-color: #C7CACE; border-right: 1px solid rgb(137,137,137); }
    .left_border { border-left: 1px solid rgb(137,137,137); }
    .event_timeline_letter { padding-left: 2px; }
    .event_title { z-index: 2; position: absolute; padding-left: 20px; line-height: 21px; }
    .error { color: red; }
    div.vis-tooltip { font-size: 11px; background-color: black; color: white; }
    .vehicle_box { background-color: white; }
    .toggle_box_general, .toggle_box_details { background-color: white; }
    .toggle_box_general { display: none; }
    .space-div { height: 2px; width: 100%; background-color: white; }
    .user-department-box {position: absolute; left: 0; top: 0; height: 10px; width: 10px; z-index: 5;}
    .fc_user { position: relative; }
    .sortable_item_wrap { text-align: left; }
    .sortable_users li { background-color: white; }
    .sortable_users .sortable_item_wrap .time_bg { position: absolute; top: 0; left:0; width: 100%; height: 100%; background-color: transparent; }
    .user_span { z-index: 7; position: relative; }
    .role-div > li > ul {margin: 0;}
    .role-div {text-align: center; font-size: 10px; padding-left: 0; list-style-type:none; margin-bottom: 0;}
    .role-empty-row {height: 5px;}
    .user_confirm_plantimeline{ border-left:5psx solid #1ab394; }
    
");


// switche kolorowe
$this->registerCss("

    .line-block {
        display: inline-block;
        margin-bottom: 5px;
    }
    .onoffswitch {
        position: relative; width: 25px;
        -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
        display: inline-block;
        margin-right: 10px;
        margin-left: 5px;
    }
    .onoffswitch-checkbox {
        display: none;
    }
    .onoffswitch-label {
        display: block; overflow: hidden; cursor: pointer;
        border: 2px solid #FFFFFF; border-radius: 50px;
    }
    .onoffswitch-inner {
        display: block; width: 200%; margin-left: -100%;
        transition: margin 0.3s ease-in 0s;
    }
    .onoffswitch-inner:before, .onoffswitch-inner:after {
        display: block; float: left; width: 50%; height: 5px; padding: 0; line-height: 5px;
        font-size: 10px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
        box-sizing: border-box;
    }
    .onoffswitch-inner:before {
        content: '';
        padding-left: 5px;
        background-color: #9E9E9E; color: #26FF00;
    }
    .onoffswitch-inner:after {
        content: '';
        padding-right: 5px;
        background-color: #9E9E9E; color: #F20000;
        text-align: right;
    }
    .onoffswitch-switch {
        display: block; width: 15px; margin: -3px;
        background: #00E600;
        position: absolute; top: 0; bottom: 0;
        right: 16px;
        border: 0; border-radius: 50px;
        transition: all 0.3s ease-in 0s; 
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
        margin-left: 0;
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
        right: 0px; 
        background-color: #F52323; 
    }
    .onoffswitch-label {
        margin-bottom: 0;
    }

");


// Angular
$this->registerJsFile('//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js' ,['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js' ,['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('//ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.min.js' ,['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('//ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular-sanitize.js' ,['position' => \yii\web\View::POS_HEAD]);

?>

<script>
    window.date_start = "<?= date("Y-m-d") ?>";

    var user_types = null;
    var skills = null;
    var departments = null;
    $.ajax({
        url : "get-user-types",
        async: false,
        success: function (resp) {
            user_types = resp;
        }
    });
    $.ajax({
        url : "get-skills",
        async: false,
        success: function (resp) {
            skills = resp;
        }
    });
    $.ajax({
        url : "get-departments",
        async: false,
        success: function (resp) {
            departments = resp;
        }
    });


    var planboardApp = angular.module("planboardApp", ["ngSanitize"]).run(function($rootScope,$window,$timeout,restService) {
        $window.planboard_data_range = [
            $window.date_start,
            moment($window.date_start).add(1, "days").format("YYYY-MM-DD"),
            moment($window.date_start).add(2, "days").format("YYYY-MM-DD"),
            moment($window.date_start).add(3, "days").format("YYYY-MM-DD"),
            moment($window.date_start).add(4, "days").format("YYYY-MM-DD"),
            moment($window.date_start).add(5, "days").format("YYYY-MM-DD"),
            moment($window.date_start).add(6, "days").format("YYYY-MM-DD")
        ];

        $rootScope.user_type_option = user_types;
        $rootScope.skills = skills;
        $rootScope.departments = departments;
        $rootScope.ekipa_filter = {
            type: 1
        };
        $rootScope.planboard_data_range = $window.planboard_data_range;

        $rootScope.user_search = function (row) {
            return (angular.lowercase(row.first_name).indexOf(angular.lowercase($rootScope.user_query) || "") !== -1 ||
                angular.lowercase(row.last_name).indexOf(angular.lowercase($rootScope.user_query) || "") !== -1);
        };

        $rootScope.user_available = function (row) {
            return !(typeof window.event_filter !== "undefined" && parseInt(window.event_filter[row.id]) === 0);

        };

        $rootScope.vehicle_search = function (row) {
            return (angular.lowercase(row.vehicle.name).indexOf(angular.lowercase($rootScope.vehicle_query) || "") !== -1 ||
                angular.lowercase(row.vehicle.registration_number).indexOf(angular.lowercase($rootScope.vehicle_query) || "") !== -1);
        };

        $rootScope.updateTabs = function(){

            restService.getUserTab()
                .then(
                    function( data ) {
                        for (var i = 0; i < data.length; i++) {
                            data[i].type = parseInt(data[i].type);
                            for (var j = 0; j < data[i].skills.length; j++) {
                                data[i].skills[j].id = parseInt(data[i].skills[j].id);
                            }
                            for (var k = 0; k < data[i].departments.length; k++) {
                                data[i].departments[k].id = parseInt(data[i].departments[k].id);
                            }
                        }

                        $rootScope.ekipa = data;
                    },
                    function( obj ) {
                    }
                );

            restService.getVehicleTab()
                .then(
                    function( data ) {
                        $rootScope.flota = data;
                    },
                    function( obj ) {
                    }
                );
        };

        $rootScope.changeUserPanel = function(firstDay) {
            $window.planboard_data_range = [
                moment(firstDay).format("YYYY-MM-DD"),
                moment(firstDay).add(1, "days").format("YYYY-MM-DD"),
                moment(firstDay).add(2, "days").format("YYYY-MM-DD"),
                moment(firstDay).add(3, "days").format("YYYY-MM-DD"),
                moment(firstDay).add(4, "days").format("YYYY-MM-DD"),
                moment(firstDay).add(5, "days").format("YYYY-MM-DD"),
                moment(firstDay).add(6, "days").format("YYYY-MM-DD"),
            ];

        };

        (function checkWindow(){
            if (
                $rootScope &&
                $rootScope.ekipa_filter &&
                !$rootScope.ekipa_filter.type
            ) {
                delete $rootScope.ekipa_filter.type;
            }

            if (
                $rootScope &&
                $rootScope.ekipa_filter &&
                $rootScope.ekipa_filter.departments &&
                !$rootScope.ekipa_filter.departments.id
            ) {
                delete $rootScope.ekipa_filter.departments;
            }

            if (
                $rootScope &&
                $rootScope.ekipa_filter &&
                $rootScope.ekipa_filter.skills &&
                !$rootScope.ekipa_filter.skills.id
            ) {
                delete $rootScope.ekipa_filter.skills;
            }

            if(
                $window.planboard_data_range.length > 0 &&
                !(
                    $window.planboard_data_range.length === $rootScope.planboard_data_range.length &&
                    $window.planboard_data_range[0] === $rootScope.planboard_data_range[0]
                )
            ){
                $rootScope.planboard_data_range = $window.planboard_data_range;
            }
            $timeout(checkWindow,1000);
        })();
    });

    planboardApp.controller("ekipaController", function($scope, $rootScope, $http, $timeout) {

    });

    planboardApp.controller("flotaController", function($scope, $rootScope, $http, $timeout) {

    });

    planboardApp.service( "restService",
        function( $http, $q, $rootScope ) {

            return({
                getUserTab: getUserTab,
                getVehicleTab: getVehicleTab
            });

            function getUserTab( binder, count ) {
                var request = $http({
                    method: "GET",
                    url: "user-tab"
                });

                return(
                    request.then(
                        function successCallback(res) {
                            return res.data
                        },
                        function( response ) {
                            return( response );
                        }
                    )
                );

            }

            function getVehicleTab( binder, count ) {
                var request = $http({
                    method: "GET",
                    url: "vehicle-tab"
                });

                return(
                    request.then(
                        function successCallback(res) {
                            return res.data
                        },
                        function( response ) {
                            return( response );
                        }
                    )
                );

            }

        }
    );

    planboardApp.filter("formatToShortWeek",function(){
        return function (data) {
            return moment(data).format("ddd");
        }
    });

    planboardApp.directive("draggableuser", function() {
        return {
            restrict:"A",
            controller: function($scope) {
                $("#draggable_users").find("li" ).draggable({
                    connectToSortable: "body .sortable_users",
                    helper: "clone",
                    revert: "invalid"
                });
            }
        };
    });

    planboardApp.directive("draggablevehicle", function() {
        return {
            restrict:"A",
            controller: function($scope) {
                $("#draggable_vehicles ").find("li").draggable({
                    connectToSortable: "body .sortable_vehicles",
                    helper: "clone",
                    revert: "invalid"
                });
            }
        };
    });

    planboardApp.filter("getDateBG", function ($rootScope) {
        return function (data) {

            if($rootScope.planboard_data_range.length === 0){
                return;
            }

            var vacation = data.vacations,
                eventUsers = data.eventUsers || data.events;

            var user_events_week = [
                {
                    "confirmed": 0,
                    "not_confirmed": 0,
                    "event": 0,
                    "half_event": 0
                },
                {
                    "confirmed": 0,
                    "not_confirmed": 0,
                    "event": 0,
                    "half_event": 0
                },
                {
                    "confirmed": 0,
                    "not_confirmed": 0,
                    "event": 0,
                    "half_event": 0
                },
                {
                    "confirmed": 0,
                    "not_confirmed": 0,
                    "event": 0,
                    "half_event": 0
                },
                {
                    "confirmed": 0,
                    "not_confirmed": 0,
                    "event": 0,
                    "half_event": 0
                },
                {
                    "confirmed": 0,
                    "not_confirmed": 0,
                    "event": 0,
                    "half_event": 0
                },
                {
                    "confirmed": 0,
                    "not_confirmed": 0,
                    "event": 0,
                    "half_event": 0
                },
            ];


            var week_start_date_obj = new Date($rootScope.planboard_data_range[0].split("-")[0],$rootScope.planboard_data_range[0].split("-")[1],$rootScope.planboard_data_range[0].split("-")[2]);
            var week_end_date_obj = new Date($rootScope.planboard_data_range[6].split("-")[0],$rootScope.planboard_data_range[6].split("-")[1],$rootScope.planboard_data_range[6].split("-")[2]);
            var one_size = 14.285714;
            var vacation_s_confirmed = "red";
            var vacation_s_not_confirmed = "yellow";
            var half_event_color = "#c4edc6";
            var event_color = "#ccc";
            var none_color = "#4ac74f";
            var bg_str = "background: -webkit-linear-gradient(left,";

            if(vacation !== undefined) {

                vacation.forEach(function(element, index, array){

                    if(element.start_date === null || element.end_date === null){
                        return "";
                    }

                    var start = element.start_date,
                        end = element.end_date;

                    if($.inArray(start, $rootScope.planboard_data_range) !== -1){
                        var start_index = $rootScope.planboard_data_range.indexOf(start);
                        if(parseInt(element.status) === 10){
                            user_events_week[start_index]["confirmed"] = 1;
                        } else {
                            user_events_week[start_index]["not_confirmed"] = 1;
                        }

                        if($.inArray(end, $rootScope.planboard_data_range) === -1){
                            for (var j = start_index; j < user_events_week.length; j++) {
                                if(parseInt(element.status) === 10){
                                    user_events_week[j]["confirmed"] = 1;
                                } else {
                                    user_events_week[j]["not_confirmed"] = 1;
                                }
                            }
                        }

                    }

                    if($.inArray(end, $rootScope.planboard_data_range) !== -1){
                        var end_index = $rootScope.planboard_data_range.indexOf(end);
                        if(parseInt(element.status) === 10){
                            user_events_week[end_index]["confirmed"] = 1;
                        } else {
                            user_events_week[end_index]["not_confirmed"] = 1;
                        }

                        if($.inArray(start, $rootScope.planboard_data_range) === -1){
                            for (var k = end_index-1; k >= 0; k--) {
                                if(parseInt(element.status) === 10){
                                    user_events_week[k]["confirmed"] = 1;
                                } else {
                                    user_events_week[k]["not_confirmed"] = 1;
                                }
                            }
                        }
                    }

                    if($.inArray(start, $rootScope.planboard_data_range) !== -1 && $.inArray(end, $rootScope.planboard_data_range) !== -1){
                        for (var m = start_index; m <= end_index; m++) {
                            if(parseInt(element.status) === 10){
                                user_events_week[m]["confirmed"] = 1;
                            } else {
                                user_events_week[m]["not_confirmed"] = 1;
                            }
                        }
                    }

                    var stard_date_obj = new Date(start.split("-")[0],start.split("-")[1],start.split("-")[2]);
                    var end_date_obj = new Date(end.split("-")[0],end.split("-")[1],end.split("-")[2]);


                    if(stard_date_obj.getTime() < week_start_date_obj.getTime() && end_date_obj.getTime() > week_end_date_obj.getTime() ){
                        for (var i=0; i <= 6; i++) {
                            if(parseInt(element.status) === 10){
                                user_events_week[i]["confirmed"] = 1;
                            } else {
                                user_events_week[i]["not_confirmed"] = 1;
                            }
                        }
                    }

                });
            }


            // event albo half_event
            var workingTimes = data.eventUserPlannedWrokingTimes;
            var workAtLeast = 4 * 60; //minutes

            var workingMinutesInDay = [0, 0, 0, 0, 0, 0, 0];

            if(workingTimes !== undefined){
                workingTimes.forEach(function(element, index, array){
                    if(element.start_time === null || element.end_time === null){
                        return "";
                    }


                    var start = moment(element.start_time);
                    var end = moment(element.end_time);

                    // jeżeli jest start, a nie ma końca
                    if( $.inArray(start.format("YYYY-MM-DD"), $rootScope.planboard_data_range) !== -1 && $.inArray(end.format("YYYY-MM-DD"), $rootScope.planboard_data_range)  === -1 ){
                        var start_index = $rootScope.planboard_data_range.indexOf(start.format("YYYY-MM-DD"));

                        workingMinutesInDay[start_index] += 24*60-start.diff(start.format("YYYY-MM-DD"), "minutes");

                        for (var i = start_index+1; i < user_events_week.length; i++) {
                            workingMinutesInDay[i] += 24 * 60;
                        }

                    }

                    // jeżeli jest koniec, a nie ma startu
                    if( $.inArray(start.format("YYYY-MM-DD"), $rootScope.planboard_data_range) === -1 && $.inArray(end.format("YYYY-MM-DD"), $rootScope.planboard_data_range)  !== -1 ){
                        var end_index = $rootScope.planboard_data_range.indexOf(end.format("YYYY-MM-DD"));

                        workingMinutesInDay[end_index] += end.diff(end.format("YYYY-MM-DD"), "minutes");
                        for (var i = end_index-1; i >= 0; i--) {
                            workingMinutesInDay[i] += 24 * 60;
                        }
                    }


                    // jeżeli jest start i koniec
                    if( $.inArray(start.format("YYYY-MM-DD"), $rootScope.planboard_data_range) !== -1 && $.inArray(end.format("YYYY-MM-DD"), $rootScope.planboard_data_range)  !== -1 ){
                        var start_index = $rootScope.planboard_data_range.indexOf(start.format("YYYY-MM-DD"));
                        var end_index = $rootScope.planboard_data_range.indexOf(end.format("YYYY-MM-DD"));

                        // jeżeli start i end to różne dni
                        if (start.format("YYYY-MM-DD") !== end.format("YYYY-MM-DD")) {

                            workingMinutesInDay[start_index] += 24*60-start.diff(start.format("YYYY-MM-DD"), "minutes");
                            workingMinutesInDay[end_index] += end.diff(end.format("YYYY-MM-DD"), "minutes");

                            for ( var i = start_index+1; i <= end_index-1; i++) {
                                workingMinutesInDay[i] += 24*60;
                            }

                        }
                        else {
                            workingMinutesInDay[end_index] += end.diff(start, "minutes");
                        }
                    }
                });
            }

            for (var i = 0; i < workingMinutesInDay.length; i++) {
                if (workingMinutesInDay[i]>0 && workingMinutesInDay[i] <= workAtLeast) {
                    user_events_week[i]["half_event"] = 1;
                }
                if (workingMinutesInDay[i] > workAtLeast) {
                    user_events_week[i]["event"] = 1;
                }
            }

            user_events_week.forEach(function(element, index, array){
                var from = index*one_size;
                var to = (index+1)*one_size;

                if(element.event === 1){
                    bg_str += event_color+" "+from+"%,"+event_color+" "+to+"%,";
                } else if(element.not_confirmed === 1 && element.confirmed === 0){
                    bg_str += vacation_s_not_confirmed+" "+from+"%,"+vacation_s_not_confirmed+" "+to+"%,";
                } else if(element.confirmed === 1) {
                    bg_str += vacation_s_confirmed+" "+from+"%,"+vacation_s_confirmed+" "+to+"%,";
                } else if(element.half_event === 1) {
                    bg_str += half_event_color+" "+from+"%,"+half_event_color+" "+to+"%,";
                } else {
                    bg_str += none_color+" "+from+"%,"+none_color+" "+to+"%,";
                }
            });
            bg_str = bg_str.slice(0, -1);
            bg_str += ");";
            return bg_str;
        };
    });
</script>

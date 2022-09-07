<?php
/* @var $this \yii\web\View */

use common\assets\PlanboardAsset;
use kartik\daterange\DateRangePicker;
use yii\helpers\Json;
use kartik\tabs\TabsX;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\bootstrap\Html;

$asset = PlanboardAsset::register($this);
\kartik\cmenu\ContextMenuAsset::register($this);
\sammaye\qtip\QtipAsset::register($this);


Modal::begin([
    'header' => Yii::t('app', 'Ekipa'),
    'id' => 'ekipa_modal'
]);
echo "<div class=\"modalContent\"></div>";
Modal::end(); 

Modal::begin([
    'header' => Yii::t('app', 'Flota'),
    'id' => 'vehicle_modal'
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();


Modal::begin([
    'header' => Yii::t('app', 'Przerwy'),
    'id' => 'event_breaks_modal'
]);
echo "<div class=\"modalContent\"></div>";
Modal::end(); ?>

<div ng-app="planboardApp" id="planboardApp">

<div class="row">
    <div class="col-sm-2">
        <div class="planboard_drug_box" id="draggable-left-panel" style="z-index: 100; background-color: white; position: fixed; left:210px;">
        <div class="row">
        <div class="col-sm-12">
                <div class="glyphicon glyphicon-repeat reset" style="border: 1px solid black; padding: 2px; float: right; cursor: pointer;" ></div>
                <div style="width: 5px; float: right; height: 5px; "></div>
                <div class="glyphicon glyphicon-move" style="border: 1px solid black; padding: 2px; float: right; cursor: pointer;" id="btn-move-panel"></div>
                </div>
        </div>
                <?php
                $items = [
                    [
                        'label'=>'<i class="glyphicon glyphicon-user"></i> '.Yii::t('app', 'Ekipa'),
                        'content'=> $this->render('_planboardTabEkipa'),
                        'active'=>true,
                        'linkOptions' => ['class' => 'tab_link'],
                    ],
                    [
                        'label'=>'<i class="glyphicon glyphicon-dashboard"></i> '.Yii::t('app', 'Flota'),
                        'content'=> $this->render('_planboardTabFlota'),
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
    <div class="col-sm-10">
        <div class="disabled btn btn-xs btn-primary" id="removeUserForEventFilter" style="position: absolute;left: 15vw;">Wyłącz filtr osób</div>
        <div id="calendar"></div>

        <!-- contextMenu -->
        <ul id="contextMenu" class="dropdown-menu" role="menu" style="display:none" >
            <li><?=Html::a(Html::icon('edit').' '.Yii::t('app',"Edytuj"),'#',['class' => 'open_ekipa_modal']);?></li>
            <li class="divider"></li>
            <li><?=Html::a(Html::icon('trash').' '.Yii::t('app',"Usuń"),'#',['class' => 'delete_event_user']);?></li>
        </ul>

        <!-- contextMenu -->
        <ul id="contextEventMenu" class="dropdown-menu" role="menu" style="display:none" >
            <li><?=Html::a(Html::icon('pencil').' '.Yii::t('app',"Wydarzenie"),'#',['class' => 'open_event_event']);?></li>
            <li><?=Html::a(Html::icon('pause').' '.Yii::t('app',"Przerwy"),'#',['class' => 'open_event_breakes_modal']);?></li>
        </ul>
    </div>
</div>

    <?php
// ContextMenu::end();

$this->registerJs('


$(".tab_link").click(function(){
    $(".activeDayHeader").removeClass("activeDayHeader");
    $(".active").removeClass("active");

});

$( "#draggable-left-panel" ).draggable({
    handle: "#btn-move-panel",
    zIndex: 999,
});


$("#draggable").data({
    "originalLeft": $("#draggable-left-panel").css("left"),
    "origionalTop": $("#draggable-left-panel").css("top")
});

$( "#draggable-left-panel" ).find(".reset").click(function(){
    $("#draggable-left-panel").css("left", "15px");
    $("#draggable-left-panel").css("top", "70px");
});



(function ($, window) {

    $.fn.contextMenu = function (settings) {

        return this.each(function () {

            // Open context menu
            $(this).on("contextmenu", function (e) {
                // return native menu if pressing control
                if (e.ctrlKey) return;
                
                //open menu
                var $menu = $(settings.menuSelector);
                $menu.data("invokedOn", $(e.target))
                    .show()
                    .css({
                        position: "absolute",
                        left: e.clientX - $(settings.parentContainer).offset().left,
                        top: e.clientY - $(settings.parentContainer).offset().top+$menu.height()/2 + $(document).scrollTop()
                    })
                    .off("click")
                    .on("click", "a", function (e) {
                        $menu.hide();
                
                        var $invokedOn = $menu.data("invokedOn");
                        var $selectedMenu = $(e.target);
                        
                        settings.menuSelected.call(this, $invokedOn, $selectedMenu);
                    });
                
                return false;
            });
    
            $("body").click(function () {
                $(settings.menuSelector).hide();
            });
        });
    };
})(jQuery, window);



$("#calendar").fullCalendar({
    lang:"pl",
    height:'.$height.',
    defaultDate:"'.$defaultDate.'",
    events:"'.Url::to(["planboard/events"]).'",
    allDayDefault: false,
    nextDayThreshold: "00:00:00",
    defaultView: "basicWeek",
    firstDay: new Date().getDay(),
    columnFormat: "ddd",
    eventStartEditable: true,
    eventOrder: "-order",
    customButtons: {
        timeHelper: {
            text: "TimeHelper",
            click: function() {
                toogleTimeHelperLine();
            }
        },
    },
    views: {
        agendaWeeks: {
            type: "basicWeek",
            duration: { weeks: 3 },
            buttonText: "Widok 2",
        },
    },
    header: {
        left: "basicWeek, agendaWeeks",
        center: "title",
        right: "prev,next today timeHelper"
    },
    loading: function(isLoading, view){
            
    },
    eventRender:function(event, element) {

        element.attr("data-start", moment(event.start).format("YYYY-MM-DD HH:mm"));
        element.attr("data-end", moment(event.end).format("YYYY-MM-DD HH:mm"));
        element.attr("data-id", event.id);
        element.attr("id", "planboard_event_"+event.id);
        var content = "";
        var outContent = "";
        element.addClass("event-border");

        if(event.departaments !== undefined){
           outContent += event.departaments;
        }
        if(event.title !== undefined){
           outContent += event.title;
        }
        if(event.packing !== undefined){
            content += event.packing;
        } 
        if(event.montage !== undefined){
           content += event.montage;
        } 
        if(event.event !== undefined){
           content += event.event;
        } 
        if(event.disassembly !== undefined){
           content += event.disassembly;
        } 


        element.html(outContent +"<div class=\'event_timeline\'><div class=\'inner\'>"+content+"</div></div>");
        var _inner = element.find(".inner");
        var _event_timeline = element.find(".event_timeline");
         _event_timeline.css("position", "absolute");
         _event_timeline.css("top", "0");
         _event_timeline.css("bottom", "0");
        _inner.css("position", "relative");
        _inner.css("height", "100%");


		if (element.hasClass("fc-start"))
		{
		    _event_timeline.css("left", event.left + "%");
		} else {
            _event_timeline.css("left", "0");
        }
		
		if (element.hasClass("fc-end"))
		{
		    _event_timeline.css("right", event.right + "%");
		} else {
            _event_timeline.css("right", "0");
        }

         // ********** info jak się najeżdza nad event (tooltip) *****************
		if (event.info != undefined)
		{
		    element.qtip({
                content: event.info,
                show: {
                    solo: true
                },
                position: {
                    target: "mouse",
                    viewport: $(window),
                    adjust: {
                        mouse:true,
                        x: 5,
                        y: 5,
                    }
                },
                style: {
                    classes: "qtip-tipsy"
                }
            });
		}
        
		if (event.line_bg != undefined){
            _inner.css("background", event.line_bg);
        }

        if(event.base_bg != undefined){
            element.css("background", event.base_bg);
        } else {
            element.css("background", "transparent");
        }
        
		if (event.textColor !== undefined)
		{
		    element.css("color", event.textColor);
		}
    },
    
    eventAfterRender: function(event, element, view) 
    {
        checkDate(event, element);

        $(element).contextMenu({
            menuSelector: "#contextEventMenu",
            parentContainer: "#calendar .fc-scroller > .fc-day-grid",
            menuSelected: function (invokedOn, selectedMenu) {
                if(selectedMenu.hasClass("open_event_breakes_modal")){
                    openEventBreakesModal(event.id);                        
                } 
                if (selectedMenu.hasClass("open_event_event")) {
                    var win = window.open("'.Url::to(["event/view"]).'?id=" + event.id+"#tab-crew","_blank");
                    win.focus();
                }
            }
        });
    
        element.after(event.event_space);
        element.after(event.vehicles);
        element.after(event.crew_details);
        element.after(event.users);

        var sortable_users = element.parent().find( ".sortable_users" );
        var sortable_vehicles = element.parent().find( ".sortable_vehicles" );
        var sortable_roles = element.parent().find( ".user-role-sortable" );

        sortable_users.sortable({
          	revert: true,
          	helper: "clone",
            receive: function(ev, ui) {
                var event_id = $(ev.target).data("id");
                var event_start = $(ev.target).data("eventstart");
                var event_end = $(ev.target).data("eventend");
                var user_id = ui.item.data("userid");
                var old_event_id = ui.item.data("eventid");
                var user_in_list_count = $(ev.target).find("li[data-userid=\""+user_id+"\"]").length;
                var alert12h = false;
                var vacation = false;
                var planned_vacation = false;
             
                if (old_event_id) {
                    assignUser(user_id,old_event_id);
                }

                $.ajax({
                    type: "POST",
                    url: "'.Url::to(["crew/is-working-in-close-range"]).'?user_id=" + user_id + "&start=" + event_start + "&end=" + event_end,
                    async:false,
                    success: function(response) {
                        if (response[0] == 1) {
                            alert12h = true;
                        }
                       if (response[1] == 1) {
                            vacation = true;
                            alert12h = true;
                        }
                       if (response[2] == 1) {
                            planned_vacation = true;
                            alert12h = true;
                        }
                    },
                    fail: function() {
                        console.log("error");
                    }
                });

                if(user_in_list_count > 1){
                    $(ev.target).find(".ng-binding.ng-scope").remove();
                    alert("Ten pracowik jest już przypisany do tego eventu");
                } else {
                
                    var can_assign = true;
                    $.ajax({
                        type: "POST",
                        url: "'.Url::to(["crew/is-available"]).'?user_id=" + user_id + "&start=" + event_start + "&end=" + event_end,
                        async:false,
                        success: function(response) {
                            if (response != 1) {
                                can_assign = false;
                            }
                        },
                        fail: function() {
                            console.log("error");
                        }
                        
                    });
                    if (can_assign) {
                        if (alert12h) {
                            var alertText = "";
                            if (vacation) {
                                alertText = "W tym czasie pracownik ma zaplanowany urlop, czy kontynuować?";
                            }
                            else if (planned_vacation){ 
                                 alertText = "W tym czasie pracownik ma zaplanowany urlop, czy kontynuować?";
                            }
                            else {
                                alertText = "Ten pracownik ma zaplanowany event w przedziale 12h, czy na pewno dodać go do tego wydarzenia?";
                            }
                        
                        
                            if ( confirm(alertText) ) {
                                assignUser(user_id,event_id,1, function(){ openUserDetailsModal(user_id,event_id, true, 0);});
                            }
                            else {
                                $("#calendar").fullCalendar("refetchEvents")
                            }
                        }
                        else {
                            assignUser(user_id,event_id,1, function(){ openUserDetailsModal(user_id,event_id, true, 0);});
                        }
                    }
                    else {
                        alert("Ta osoba pracuje już w czasie trwania tego eventu");
                        $("#calendar").fullCalendar("refetchEvents")
                    }
                }
            },
            stop: function(event, ui) {
                var event_id = $(ui.item).parent().data("id");
                $.ajax({
                    type: "POST",
                    url: "'.Url::to(["planboard/delete-all-order-event-general-user"]).'?event_id=" + event_id,
                    async:false
                });
                var i = 0;
                $(event.target).children().each(function(){
                    var user_id = $(this).data("userid");
                    $.post("'.Url::to(["planboard/update-order-event-general-user"]).'?event_id=" + event_id + "&event_user=" + user_id + "&order_key=" + i);                                    
                    i++;
                });
            }
        });
        
        sortable_users.find("li").draggable({
          connectToSortable: ".sortable_users",
          drag: function( event, ui ) {
          
          },
          stop: function( event, ui ) {

          }
        });

        
       sortable_roles.sortable({
            revert: true,
            stop: function(event, ui) {
                var event_id = $(ui.item).data("eventid");
                $.ajax({
                    type: "POST",
                    url: "'.Url::to(["planboard/delete-all-order-event-role"]).'?event_id=" + event_id,
                    async:false
                });
                var i = 0;
                $(event.target).children().each(function(){
                    var role_id = $(this).data("role");
                    $.post("'.Url::to(["planboard/update-order-event-role"]).'?event_id=" + event_id + "&role_id=" + role_id + "&order_key=" + i);
                    i++;
                });   
            }
        });
        
       var sortable_users_details = element.parent().find(".sortable_users_details");
       sortable_users_details.sortable({
            revert: true,
            over: function(event, ui) {
                ui.placeholder.parent().parent().parent().find("li").first().css("background-color", "yellow");
            },
            out: 
            function(event, ui) {
                ui.placeholder.parent().parent().parent().find("li").first().css("background-color", "white");
            },
            receive: function(ev, ui) {
                var target = $(ev.target);
                var role_id = target.data("role");
                var event_id = target.data("id");
                var user_id = ui.item.data("userid");
                var user_in_list_count = $(ev.target).find("li[data-userid=\""+user_id+"\"]").length;
                var event_start = $(ev.target).data("eventstart");
                var event_end = $(ev.target).data("eventend");
                var old_event_id = ui.item.data("eventid");
                var alert12h = false;
                var vacation = false;
                var planned_vacation = false;

                if (old_event_id) {
                    assignUser(user_id,old_event_id);
                }

                $.ajax({
                    type: "POST",
                    url: "'.Url::to(["crew/is-working-in-close-range"]).'?user_id=" + user_id + "&start=" + event_start + "&end=" + event_end,
                    async:false,
                    success: function(response) {
                        if (response[0] == 1) {
                            alert12h = true;
                        }
                       if (response[1] == 1) {
                            vacation = true;
                            alert12h = true;
                        }
                       if (response[2] == 1) {
                            planned_vacation = true;
                            alert12h = true;
                        }
                    },
                    fail: function() {
                        console.log("error");
                    }
                });

                if (user_in_list_count == 1) {
                
                    var can_assign = true;
                    $.ajax({
                        type: "POST",
                        url: "'.Url::to(["crew/is-available"]).'?user_id=" + user_id + "&start=" + event_start + "&end=" + event_end,
                        async:false,
                        success: function(response) {
                            if (response != 1) {
                                can_assign = false;
                            }
                        },
                        fail: function() {
                            console.log("error");
                        }
                    });
                
                    if (can_assign) {
                        if (alert12h) {
                            var alertText = "";
                            if (vacation) {
                                alertText = "W tym czasie pracownik ma zaplanowany urlop, czy kontynuować?";
                            }
                            else if (planned_vacation){ 
                                 alertText = "W tym czasie pracownik ma zaplanowany urlop, czy kontynuować?";
                            }
                            else {
                                alertText = "Ten pracownik ma zaplanowany event w przedziale 12h, czy na pewno dodać go do tego wydarzenia?";
                            }
                        
                            if ( confirm(alertText) ) {
                                $.ajax({
                                    type: "GET",
                                    url: "'.Url::to(["planboard/is-user-assigned-to-event"]).'?event_id=" + event_id + "&user_id="+user_id,
                                    async:false,
                                    success: function(result) {
                                        if (result == 0) {
                                            
                                        }
                                        else {
                                            openUserDetailsModal(user_id,event_id, true, role_id);
                                        }
                                    }
                                });
                                assignUserToRole(user_id, event_id, role_id, 1);
                                openUserDetailsModal(user_id,event_id, true, role_id);
                            }
                            else {
                                $("#calendar").fullCalendar("refetchEvents")
                            }
                        }
                        else {
                            assignUserToRole(user_id, event_id, role_id, 1);
                            openUserDetailsModal(user_id,event_id, true, role_id);
                        }
                    }
                    else {
                        alert("Ta osoba pracuje już w czasie trwania tego eventu");
                        $("#calendar").fullCalendar("refetchEvents")
                    }
                }
                else {
                    alert("Ten pracownik jest już przypisany do tego zadania");
                    $(ev.target).find(".ng-binding.ng-scope").remove();
                }
            },
            stop: function(event, ui) {
                var elLi = $(ui.item);
                var event_id = elLi.parent().data("id");
                var role_id = elLi.data("role");
                $.ajax({
                    type: "POST",
                    url: "'.Url::to(["planboard/delete-all-order-event-role-users"]).'?event_id=" + event_id + "&role_id=" + role_id,
                    async:false
                });
                var i = 0;
                $(event.target).children().each(function(){
                    var event_user = $(this).data("userid");
                    $.post("'.Url::to(["planboard/update-order-event-role-user"]).'?event_id=" + event_id + "&role_id=" + role_id + "&event_user=" + event_user + "&order_key=" + i);
                    i++;
                });   
            },
        });
        
       set_time_bg_for_users_in_event(sortable_users,event);
       set_time_bg_for_users_in_event(sortable_vehicles,event);
    
       sortable_vehicles.sortable({
            revert: true,
            receive: function(ev, ui) {
                var event_id = $(ev.target).data("id");
                var car_id = ui.item.data("carid");
                var car_in_list_count = $(ev.target).find("li[data-carid=\""+car_id+"\"]").length;
                var list_item = $(ev.toElement);
                if(car_in_list_count > 1){
                    $(ev.toElement).remove();
                } else {
                    list_item.removeAttr("style");
                    assignVehicle(car_id,event_id,1,function(){ openVehicleDetailsModal(car_id,event_id); });
                    appendVehicleActionsToEl(list_item);
                }
            },
            stop: function(event, ui) {
                var elLi = $(ui.item);
                var event_id = elLi.parent().data("id");
                $.ajax({
                    type: "POST",
                    url: "'.Url::to(["planboard/delete-all-vehicle-order"]).'?event_id=" + event_id,
                    async:false
                });
                var i = 0;
                $(event.target).children().each(function(){
                    var vehicle_id = $(this).data("carid");
                    $.post("'.Url::to(["planboard/update-order-vehicle"]).'?event_id=" + event_id + "&vehicle_id=" + vehicle_id + "&order_key=" + i);
                    i++;
                });   
            }
        });


       element.draggable( {
            containment: "#calendar",
            cursor: "move",
            stop: function( event, ui ) {

                var element = $(ui.helper.context);
                var element_offset = element.offset().top;

                var window_start_date = moment(window.planboard_data_range[0]).startOf("day").unix();
                var window_end_date = moment(window.planboard_data_range[window.planboard_data_range.length-1]).endOf("day").unix();

                var ev_start = moment(element.data("start")).startOf("day").unix();
                var ev_end = moment(element.data("end")).endOf("day").unix();
                var start = ev_start >= window_start_date ? ev_start : window_start_date;
                var end = ev_end <= window_end_date ? ev_end : window_end_date;
                var id = element.data("id");


                var dayEvents = $("#calendar").fullCalendar( "clientEvents", function(event, index){
                    var eventStart = moment(event.start).startOf("day").unix();
                    var eventEnd = moment(event.end).endOf("day").unix();

                    if (id == event.id) return true;
                    if (eventStart >= start && eventStart <= end) return true;
                    if (eventEnd >= start && eventEnd <= end) return true;
                    if (eventStart <= start && eventEnd >= end) return true;
                    return false;
                     
                });
                
                dayEvents.sort(function(a, b){
                    var el_a = $("#planboard_event_"+a.id);
                    var el_b = $("#planboard_event_"+b.id);

                    var keyA = el_a[0].offsetTop,
                        keyB = el_b[0].offsetTop;

                    if(keyA < keyB) return -1;
                    if(keyA > keyB) return 1;
                    return 0;
                });

                var id_order_arr = [];
                var num_order = 0;
                dayEvents.forEach(function(value,index,array){
                    id_order_arr.push({id: value.id, order: num_order});
                    num_order ++;
                });

                element.remove();

                var data = {
                    data: id_order_arr
                };

                $.post("'.Url::to(["planboard/update-order"]).'", data, function(response){
                    $("#calendar").fullCalendar("refetchEvents");
                });
            }
       } );
		
        $( "ul, li" ).disableSelection();

        onWindowResize();
    },
    
    dayRender: function(date, cell) {
        
        if (date.isBetween(moment().add(-1, "days"), moment().add(6, "days"))) {
            var htmlDay = "<table class=\"day-names current-week-big-calendar\"><tr><td>"+date.format("ddd")+"</td></tr></table>";
        }
        else {
            var htmlDay = "<table class=\"day-names\"><tr><td>"+date.format("ddd")+"</td></tr></table>";
        }


        var htmlHours = "<table class=\"day-hours\"><tr>";
        for (var i=0; i<24; i++)
        {
            var htmlHour = "<td>" + i + "</td>";
            htmlHours = htmlHours + htmlHour;
        }
        htmlHours  += "</tr></table>";
        if(date.format("DD") == moment().format("DD")){
            var diff = (moment().unix() - moment().startOf("day").unix())*100/(24*3600);

            htmlHours  += "<div class=\"fc_help_line_box\"><div class=\"fc_help_line\" style=\"left:"+diff+"%\"></div></div>";
        }
        htmlHours  += "<div class=\"fc_cell_day_date\">"+date.format("DD")+"</div>";
        
        cell.append(htmlDay);
        cell.append(htmlHours);
    	
		
    },

    eventAfterAllRender: function(view){
        
        $(".fc-agendaWeeks-button").click(function(){
            $("#calendar").fullCalendar("gotoDate", moment().subtract(7, "days"));
        });
        
        $("body").find(".sortable_users li").each(function(){
            appendActionsToEl($(this));
        });

        $("body").find(".sortable_vehicles li").each(function(){
            // sortowanie dla samochodów na boardzie
            appendVehicleActionsToEl($(this));
        });


        // opcje na duży kalendarz
        // 1. zdefiniować swój widok == Coding a View From Scratch
        // 2. niszczyć i tworzyć nowy kalendarz z agendaWeek i długością zależną od wprowadzonych dat
        // 3. przesunąć kalendarz z widoku agendaWeeks do odpowiedniej daty, sztywna długość
        
        $("#calendar-daterange").change(function(){
            var range = $(this).val().split(" ");
            var start = moment(range[0]);
            var end = moment(range[2]);
            
            
            $("#calendar").fullCalendar("destroy");
            $("#calendar").fullCalendar({
            
            
            
            });
            
            
        });

        $("#calendar .fc-body .fc-widget-content .fc-day-grid").append("<div id=\"time_help_line\"></div>");
        angular.element(document.getElementById("planboardApp")).scope().updateTabs();

        // dla małego kalendarza
        $(".fc-day-header.fc-widget-header").click(function(){
            var day = $(this).data("date");
            
            $(".fc-day-header").each(function(){
                $(this).removeClass("active-filter-day");
            });
            
            var this_cell = $(this);
            
            for (var i=1; i<=7; i++) {
                this_cell.addClass("active-filter-day");
                this_cell = this_cell.next(".fc-day-header");
            }
            
            angular.element(document.getElementById("planboardApp")).scope().changeUserPanel(day);
        
        });
        // dla dużego kalendarza
         $(".fc-agendaWeeks-view .day-names td").click(function(){
            date_cell =  $(this).parent().parent().parent().parent();
            var day = date_cell.data("date");
            
            $(".day-names").each(function(){
                $(this).removeClass("active-filter-day");
            });
            
            var  i = 1;
            var active_cell_index = 0;
            $(".day-names").each(function(){
            
                if ($(this).parent().data("date") == day) {
                    active_cell_index = 1;
                }
                if (active_cell_index != 0 && active_cell_index <= 7) {
                    active_cell_index++;
                    $(this).addClass("active-filter-day");
                }   
                i++;
            });
            
            angular.element(document.getElementById("planboardApp")).scope().changeUserPanel(day);
        });
        
        


    },

    // dayClick: function(date, jsEvent, view) {
    //     alert("day");
    // },
     eventClick: function(calEvent, jsEvent, view) {

        $(jsEvent.currentTarget).parent().find(".toggle_box_general").toggle();
        $(jsEvent.currentTarget).parent().find(".toggle_box_details").toggle();
        

        $.ajax({
            type: "POST",
            url: "'.Url::to(["event/check-availability-for-event"]).'?event_id=" + calEvent.id,
            success: function(resp) {
                user_availability = JSON.parse(resp);
                window.event_filter = user_availability;
                $("#removeUserForEventFilter").removeClass("disabled");
            },
            error: function() {
                console.log("error");
            }
        });

        return false;


     }
});

$("#removeUserForEventFilter").click(function(){ 
    if (!$(this).hasClass("disabled")) {
        delete window.event_filter;
        $(this).addClass("disabled");
    }
});


    function sortEventByCustomParam(prevEventObj, nextEventObj) {
        if(prevEventObj.eventOrder < nextEventObj.eventOrder){
            return -1; 
        }
        else{
            return 1; 
        }
    }

    function checkDate(event, element)
    {     
        // element = nagłówek z informacją o evencie (nazwa itd)
        var $el = $(element);
        
        var dates = new Array(); 
        $el.closest(".fc-row").find("[data-date]").each(function(index, el) {
        if ($.inArray($(el).data("date"), dates) == -1) {
                dates.push($(el).data("date"));
            }
        });
        window.planboard_data_range = dates;

        if ($el.offset() == undefined) return false; 

	    if(event.end == null) {
	        return false;
	    }
	    if(event.start == null) {
	        return false;
	    }
        if(event.start._i == undefined) {
            return;
        }
	    var start = event.start._i.split(" ")[0];
	    var end = event.end._i.split(" ")[0];

	    var _inner = element.find(".inner");
	    var _event_timeline = element.find(".event_timeline");

	    


	    var oneDay = 24*60*60*1000; // hours*minutes*seconds*milliseconds
	    var split_start = start.split("-");
	    var split_end = end.split("-");
	    var firstDate = new Date(split_start[0],split_start[1],split_start[2]);
	    var secondDate = new Date(split_end[0],split_end[1],split_end[2]);

	    var event_days = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));


        if ($.inArray(start, dates) == -1 && $.inArray(end, dates) == -1) {
            var first_date_for_event_in_a_row = dates[0]+" 00:00:00";;
			_event_timeline.css("left","0");
			_event_timeline.css("right","0");
			updateLinesInElement(_inner,dates,7*oneDay,first_date_for_event_in_a_row);
        }
        else if ($.inArray(start, dates) == -1)
        {
        	var first_date_in_row = dates[0];
			var split_row_first_date = first_date_in_row.split("-");

			firstDate = new Date(split_row_first_date[0],split_row_first_date[1],split_row_first_date[2]);
			secondDate = new Date(split_end[0],split_end[1],split_end[2]);
	        var days_count = Math.round(Math.abs((secondDate.getTime() - firstDate.getTime())/(oneDay))) +1;

			var days_count_in_time =  days_count*oneDay;
			var end_time = 0;
			var end_time_srt = event.end._i.split(" ")[1];

			if(end_time_srt !== undefined){
				var h = parseInt(end_time_srt.split(":")[0]);
				var m = parseInt(end_time_srt.split(":")[1]);
				var s = parseInt(end_time_srt.split(":")[2]);
				end_time = oneDay - (h*3600+m*60+s)*1000;
			}
			var result = days_count_in_time - end_time;
			var right = 100 - ((days_count_in_time - end_time)*100/days_count_in_time);
            _event_timeline.css("right", right+"%");
            _event_timeline.css("left","0");

            var first_date_for_event_in_a_row = dates[0]+" 00:00:00";

			updateLinesInElement(_inner,dates,result,first_date_for_event_in_a_row);
        } 
        else if ($.inArray(end, dates) == -1)
        {
			
			var last_date_in_row = dates[6];
			var split_row_end_date = last_date_in_row.split("-");
            var end_time_srt = event.end._i.split(" ")[1];

			firstDate = new Date(split_row_end_date[0],split_row_end_date[1],split_row_end_date[2]);
			secondDate = new Date(split_start[0],split_start[1],split_start[2]);
	        var days_count =  firstDate.getTime() - secondDate.getTime() + oneDay;

			var start_time = 0;
			var start_time_srt = event.start._i.split(" ")[1];

			if(start_time_srt !== undefined){
				var h = parseInt(start_time_srt.split(":")[0]);
				var m = parseInt(start_time_srt.split(":")[1]);
				var s = parseInt(start_time_srt.split(":")[2]);
				start_time = days_count - (h*3600+m*60+s)*1000;
			}

			var left = (days_count - start_time)*100/days_count;
        	_event_timeline.css("left", left+"%");
        	_event_timeline.css("right","0");
			updateLinesInElement(_inner,dates,start_time,event.start._i);
		}	
        
    }

    function updateLinesInElement(_inner,row_dates,time_in_a_row,first_date_for_event_in_a_row){
		
		var oneDay = 24*60*60*1000; // hours*minutes*seconds*milliseconds

		_inner.find("div").each(function(){
			var _this = $(this),
			start_date_srt = _this.data("start"),
			end_date_srt = _this.data("end"),
			split_start = start_date_srt.split(" ")[0],
			split_end = end_date_srt.split(" ")[0];

            //first date in a row obj
            var first_date_in_row = row_dates[0].split("-");
            var first_date_in_a_row_obj = new Date(first_date_in_row[0],first_date_in_row[1],first_date_in_row[2]);
            
            //last date in a row obj
            var last_date_in_row = row_dates[6].split("-");
            last_date_in_row_obj = new Date(last_date_in_row[0],last_date_in_row[1],last_date_in_row[2]);
            
            //end date for block
            var end_date_obj = new Date(split_end.split("-")[0],split_end.split("-")[1],split_end.split("-")[2]);
            
            //start date for block
            var start_date_obj = new Date(split_start.split("-")[0],split_start.split("-")[1],split_start.split("-")[2]);

            var end_time = 0;
            var end_time_srt_split = end_date_srt.split(" ")[1];

            if(end_time_srt_split !== undefined){
                var h = parseInt(end_time_srt_split.split(":")[0]);
                var m = parseInt(end_time_srt_split.split(":")[1]);
                var s = parseInt(end_time_srt_split.split(":")[2]);
                end_time = (h*3600+m*60+s)*1000;
            }

            var start_time = 0;
            var start_time_srt_split = start_date_srt.split(" ")[1];

            if(start_time_srt_split !== undefined){
                var h = parseInt(start_time_srt_split.split(":")[0]);
                var m = parseInt(start_time_srt_split.split(":")[1]);
                var s = parseInt(start_time_srt_split.split(":")[2]);
                start_time = (h*3600+m*60+s)*1000;
            }


			if ($.inArray(split_start, row_dates) == -1 && $.inArray(split_end, row_dates) == -1 && first_date_in_a_row_obj.getTime() > start_date_obj.getTime() && last_date_in_row_obj.getTime() < end_date_obj.getTime()) {
				
                _this.css("right", "0");
                _this.css("left", "0");
                _this.css("width", "100%");

			} else if ($.inArray(split_start, row_dates) == -1 && $.inArray(split_end, row_dates) == -1){
                $(this).remove();
            } else if ($.inArray(split_start, row_dates) == -1) {

		        var days_count = end_date_obj.getTime() - first_date_in_a_row_obj.getTime();
				end_time += days_count;
				
				var width = end_time*100/time_in_a_row;
				_this.css("left", "0");
				_this.css("width", width+"%");

			} else if ($.inArray(split_end, row_dates) == -1) {

		        var days_count = last_date_in_row_obj.getTime() - start_date_obj.getTime() + oneDay;
                start_time = days_count - start_time;

 				var width = start_time*100/time_in_a_row;
				_this.css("right", "0");
				_this.css("left", "auto");
				_this.css("width", width+"%");

			} else {

                var days_count = end_date_obj.getTime() - start_date_obj.getTime();
                var time_count = end_time - start_time;
                var event_time = days_count+time_count;

                var width = event_time*100/time_in_a_row;

                var date = first_date_for_event_in_a_row.split(" ")[0];
                var time = first_date_for_event_in_a_row.split(" ")[1];
                fdfeiar = new Date(date.split("-")[0],date.split("-")[1],date.split("-")[2]);
                var fdfeiar_h = parseInt(time.split(":")[0]);
                var fdfeiar_m = parseInt(time.split(":")[1]);
                var fdfeiar_s = parseInt(time.split(":")[2]);
                fdfeiar_time = (fdfeiar_h*3600+fdfeiar_m*60+fdfeiar_s)*1000;               
                var time_diff = start_date_obj.getTime() - fdfeiar.getTime() + start_time - fdfeiar_time;

                var left = time_diff*100/time_in_a_row;
                _this.css("width", width+"%");
                _this.css("right", "auto");
                _this.css("left", left+"%");

			}
		});
    }

    function set_time_bg_for_users_in_event(element,event){
 
        var dates = window.planboard_data_range;
        element.find("li").each(function(){
                        
            var _this = $(this);
            var time_bg = _this.find(".time_bg");
            time_bg.css("background", "transparent");
            time_bg.css("left", 0);
            time_bg.css("right", 0);
            
            // szary = rgba(0,0,0,0.3) = praca
            // czarwony = rgba(255,0,0,0.3) = przerwa
            
            // na time_bg wymalujemy godziny pracy i przerwy
            // time_bg = pasek calego eventu + kawalek do poczatku dnia przed startem eventu i kawalek od konca eventu do konca tego dnia
                    
            var event_start = _this.data("eventstart");
            var event_end = _this.data("eventend");
            var end_split = event_end.split(" ");
            if (end_split[1] == "00:00:00") {
                event_end = moment(end_split[0]).subtract(1, "days").format("YYYY-MM-DD") + " 23:59";
            }
            
            var week_start = dates[0] + " 00:00";
            var week_end = dates[6] + " 23:59";
           
/********* ### Przypadek 1 ### *********/            
            // jeżeli początek i koniec eventu jest w tym tygodniu
            // to początek i koniec eventu jest taki jak w data atrybutach
            if ($.inArray(event_start.split(" ")[0], dates) != -1 && $.inArray(event_end.split(" ")[0], dates) != -1) {
                // początek pierwszego dnia, w ktorym zaczyna sie event i koniec ostatniego dnia, w ktorym konczy sie event
                // czyli caly pasek eventu (time_bg) - od 00:01 pierwszego dnia do 23:59 ostatniego dnia
                var first_day_start_event = moment(event_start).startOf("day").format("YYYY-MM-DD HH:mm:ss");
                var last_day_end_event = moment(event_end).endOf("day").format("YYYY-MM-DD HH:mm:ss");
            }
/********* ### Przypadek 2 ### *********/            
            // jeżeli początek i koniec nie jest w tym tygodniu, to event ciągnie się przez cały tydzień
            // i początek i koniec paska eventu w tym wypadku to początek i koniec tego tygodnia -> do wyświetlenia czasu pracy
            if ($.inArray(event_start.split(" ")[0], dates) == -1 && $.inArray(event_end.split(" ")[0], dates) == -1) {
                var first_day_start_event = moment(week_start).startOf("day").format("YYYY-MM-DD HH:mm:ss");
                var last_day_end_event = moment(week_end).endOf("day").format("YYYY-MM-DD HH:mm:ss");
                event_start = first_day_start_event;
                event_end = last_day_end_event;
            }
/********* ### Przypadek 3 ### *********/
            // jeżeli początek nie jest w tym tygodniu, a koniec jest w tym tygodniu
            if ($.inArray(event_start.split(" ")[0], dates) == -1 && $.inArray(event_end.split(" ")[0], dates) != -1) {
                var first_day_start_event = moment(week_start).startOf("day").format("YYYY-MM-DD HH:mm:ss");
                var last_day_end_event = moment(event_end).endOf("day").format("YYYY-MM-DD HH:mm:ss");
                event_start = first_day_start_event;
            }
/********* ### Przypadek 4 ### *********/       
            // jeżeli początek jest w tym tygodniu, a koniec nie jest w tym tygodniu 
            if ($.inArray(event_start.split(" ")[0], dates) != -1 && $.inArray(event_end.split(" ")[0], dates) == -1) {
                var first_day_start_event = moment(event_start).startOf("day").format("YYYY-MM-DD HH:mm:ss");
                var last_day_end_event = moment(week_end).endOf("day").format("YYYY-MM-DD HH:mm:ss");
                event_end = last_day_end_event;
            }
            
            var print = true;
            // rysujemy pasek eventu:
            // dlugosc trwania (pasek) eventu na pasku wszystkich dnia/dni w ktorych jest event
            time_bg.append(elementOnTimeLine(
                first_day_start_event, 
                last_day_end_event, 
                event_start, 
                event_end, 
                "event_zone",
                null,
                print
            ));

            var event_zone = time_bg.first();
            
            // rysujemy godziny pracy
            var arr = $(this).data("workinghours");
            if (arr) {
                for (var i = 0; i < arr.length; i++) {
                    print = true;
                    
                    // jeżeli dni są poza zakresem z lewej strony to nie rysujemy
                    if ( moment(arr[i][0]).isBefore(first_day_start_event) &&  moment(arr[i][1]).isBefore(first_day_start_event) ) {
                        print = false;
                    }
                    // jeżeli dni są poza zakresem z prawej strony to nie rysujemy
                    if ( moment(arr[i][0]).isAfter(last_day_end_event) &&  moment(arr[i][1]).isAfter(last_day_end_event) ) {
                        print = false;
                    }
                    // jeżeli start jest poza lewą stroną
                    if (moment(arr[i][0]).isBefore(first_day_start_event)) {
                        arr[i][0] = first_day_start_event;
                    }
                    // jeżeli konniec jest poza prawą stroną
                    if ( moment(arr[i][1]).isAfter(last_day_end_event) ) {
                        arr[i][1] = last_day_end_event;
                    }
                    
                    event_zone.append(elementOnTimeLine(
                        first_day_start_event, 
                        last_day_end_event, 
                        arr[i][0], 
                        arr[i][1], 
                        "work_zone",
                        null,
                        print
                    ));
                }
            }
            
          
            // rysujemy godziny odpoczynku
            var arr = $(this).data("breakhours");
            if (arr) {
                for (var i = 0; i < arr.length; i++) {
                    print = true;
                    var icon = null;
                    
                    // jeżeli dni są poza zakresem z lewej strony to nie rysujemy
                    if ( moment(arr[i][0]).isBefore(first_day_start_event) &&  moment(arr[i][1]).isBefore(first_day_start_event) ) {
                        print = false;
                    }
                    // jeżeli dni są poza zakresem z prawej strony to nie rysujemy
                    if ( moment(arr[i][0]).isAfter(last_day_end_event) &&  moment(arr[i][1]).isAfter(last_day_end_event) ) {
                        print = false;
                    }
                    // jeżeli start jest poza lewą stroną
                    if (moment(arr[i][0]).isBefore(first_day_start_event)) {
                        arr[i][0] = first_day_start_event;
                    }
                    // jeżeli konniec jest poza prawą stroną
                    if ( moment(arr[i][1]).isAfter(last_day_end_event) ) {
                        arr[i][1] = last_day_end_event;
                    }
                    
                    if (arr[i][2] != null) {
                        icon = arr[i][2];
                    }
                    event_zone.append(elementOnTimeLine(
                        first_day_start_event, 
                        last_day_end_event, 
                        arr[i][0], 
                        arr[i][1], 
                        "break_zone",
                        icon,
                        print
                    ));
                }
            }
        });    
    }

    function elementOnTimeLine(event_callendar_start, event_callendar_end, work_start, work_end, class_name, icon, print) {
        if (!print) {
            return null;
        }
    
        var work_start1 = work_start;
        var work_end2 = work_end;
    
        var start = moment(event_callendar_start);
        var end = moment(event_callendar_end);
        
        var work_start = moment(work_start);
        var work_end = moment(work_end);
        
                

        // dlugosc paska w minutach
        var full_length = end.diff(start, "minutes");

    
    
        // full_length  - pełna długośc paska święta w tym tygodniu ( w minutach)
        // start_div    - długoś w minutach od poczatku paska calego eventu do poczatku czasu pracy 
        // end_div_from_start_of_bande - długosc w minutach od poczatku paska calego eventu do konca czasu pracy
        // end_div_from_end_of_bande - dlugosc w minutach od konca paska calego eventu do konca czasu pracy

    
        // poczatek paska z praca i koniec tego paska
        var start_div = work_start.diff(start, "minutes");      
        var end_div_from_start_of_bande = work_end.diff(start, "minutes");
        var end_div_from_end_of_bande = full_length - end_div_from_start_of_bande;
        
        var percentage_start = start_div / full_length * 100;
        var percentage_end = end_div_from_end_of_bande / full_length * 100;
    
        var div = $("<div style=\'left: "+ percentage_start +"%; right: "+ percentage_end +"%;\' class=\'zone "+class_name+"\'></div>");
        
        if (icon != null) {
            div.append("<span class=\'glyphicon glyphicon-" + icon + "\'></span> ");
        }
        
        return div;
    }


    function toogleTimeHelperLine(){
        var time_help_line = $("#time_help_line");

        if(time_help_line.hasClass("on")){
            time_help_line.removeClass("on"); 
        } else {
            time_help_line.addClass("on");
        }

        $("#calendar .fc-body .fc-widget-content .fc-day-grid").on("mousemove", function(e){
            var calendar_l_o = $("#calendar").offset().left;
            var scroll_left = $("#calendar .fc-scroller").scrollLeft();
            $("#time_help_line").css({
               left:  e.pageX-calendar_l_o + scroll_left
            });
        });
    }




');


$this->registerJs('
    onWindowResize();
    $(window).resize(function()
    {
        onWindowResize();
    });
    
    function onWindowResize()
    {
        var $w = $(window);
        var width = $w.width();
        if (width>=1400)
        {
            showEvery(2);
        }
        else if (width>=800 && width < 1300)
        {
            showEvery(4);
        }
        else
        {
            showEvery(4);
        }

        var height = parseInt($(window).height()) - parseInt($("#calendar .fc-body").offset().top) - parseInt($("footer").outerHeight());

        if(height < 400) {
            height = 400;
        }
    
        $("#calendar").fullCalendar("option", "contentHeight", height);

    }
    
    function showEvery(number)
    {
        $("table.day-hours td").each(function(index,element){
                var $e = $(element);
                if (index%number == 0)
                {
                    $e.show();
                }
                else
                {
                    $e.hide();
                }
        });
    }

    function assignUserToRole(user_id, event_id, role_id, add) {
        $.post("'.Url::to(["crew/assign-user-to-role"]).'?user_id="+user_id+"&event_id="+event_id+"&role_id="+role_id+"&add="+add, null, function(response){
            $("#calendar").fullCalendar("refetchEvents").done(function(){
               $("#planboard_event_"+event_id).click();
            });
        });
    }

    function assignUser(id, event_id, add, callback = function(response){})
    {
        var data = {
            itemId : id,
            add : add ? 1 : 0
        }
        
       $.post("'.Url::to(["crew/assign-user"]).'?id="+event_id, data, function(response){
            $("#calendar").fullCalendar("refetchEvents").done(function(){
              //  $("#planboard_event_"+event_id).click();
            });
            callback(response);
       });
    }

    function assignVehicle(id, event_id, add, callback = function(response){})
    {
        var data = {
            itemId : id,
            add : add ? 1 : 0
        }
        $.post("'.Url::to(["vehicle/assign-vehicle"]).'?id="+event_id, data, function(response){
            $("#calendar").fullCalendar("refetchEvents").done(function(){
                $("#planboard_event_"+event_id).click();
            });;
            callback(response);
            
        });
    }

    function openUserDetailsModal(user_id,event_id, first_time, role){

        var modal = $("#ekipa_modal");
        if (first_time) {
            modal.find(".modalContent").load("'.Url::to(["planboard/user-form"]).'?event_id="+event_id+"&user_id="+user_id+"&just_assigned=1&role="+role);
        }
        else {
            modal.find(".modalContent").load("'.Url::to(["planboard/user-form"]).'?event_id="+event_id+"&user_id="+user_id+"&role="+role);
        }
        modal.modal("show");

    }

    function openVehicleDetailsModal(vehicle_id,event_id){

        var modal = $("#vehicle_modal");
        modal.find(".modalContent").load("'.Url::to(["planboard/vehicle-form"]).'?event_id="+event_id+"&vehicle_id="+vehicle_id);
        modal.modal("show");
    }

    function deleteUserFromEvent(user_id,event_id,element)
    {
        assignUser(user_id,event_id,0, function(res){ element.remove(); });
    }

    function deleteUserFromEventRole(user_id, event_id, role_id) {
        assignUserToRole(user_id, event_id, role_id, 0);
    }

    function deleteVehicleFromEvent(vehicle_id,event_id,element)
    {
        assignVehicle(vehicle_id,event_id,0, function(res){ element.remove(); });
    }

    function appendActionsToEl(el) {
        var list = el.closest(".sortable_users");
        var event_id = list.data("id");
        var user_id = el.data("userid");

        //console.log(user_id);

        el.contextMenu({
            menuSelector: "#contextMenu",
            parentContainer: "#calendar .fc-scroller > .fc-day-grid",
            menuSelected: function (invokedOn, selectedMenu) {

                if(selectedMenu.hasClass("open_ekipa_modal")){
                    openUserDetailsModal(user_id,event_id, false, 0);                        
                } else {
                    var role_id = $(el[0]).data("role");
                    if(confirm("'.Yii::t("app","Czy chcesz usunąć pracownika?").'"))
                        if (role_id) {
                            deleteUserFromEventRole(user_id,event_id,role_id);
                        }
                        else {
                            deleteUserFromEvent(user_id,event_id,$(el[0]));
                        }
                }
            }
        });
    }

    function appendVehicleActionsToEl(el) {
        var list = el.closest(".sortable_vehicles");
        var event_id = list.data("id");
        var vehicle_id = el.data("carid");

        el.contextMenu({
            menuSelector: "#contextMenu",
            parentContainer: "#calendar .fc-scroller > .fc-day-grid",
            menuSelected: function (invokedOn, selectedMenu) {

                if(selectedMenu.hasClass("open_ekipa_modal")){
                    openVehicleDetailsModal(vehicle_id,event_id);                        
                } else {
                    if(confirm("'.Yii::t("app","Czy chcesz usunąć samochód?").'"))
                        deleteVehicleFromEvent(vehicle_id,event_id,$(el[0]));
                }
            }
        });
    }

    function openEventBreakesModal(event_id) {
        var modal = $("#event_breaks_modal");
        modal.find(".modalContent").load("'.Url::to(["planboard/event-breaks-form"]).'?event_id="+event_id);
        modal.modal("show");
    }
');

$this->registerCss('body .fc-content-skeleton {padding-top:35px;} 
    body .fc-agendaWeeks-view .fc-content-skeleton {padding-top:54px;} 
    .fc_cell_day_date{text-align:right;padding:0 5px;} #draggable {z-index:9999;} 
    .fc td.fc-today{position:relative; z-index:100;} 
    .fc_help_line{border-left:1px solid #000; position: absolute; top:0;height:100%; z-index:100;}
    .sortable_users li.highlight,.sortable_vehicles li.highlight {box-shadow: 0px 0px 3px 2px rgba(54, 163, 247, 0.8);}
    #time_help_line {position: absolute; left:0; top:0; bottom:0; border-left:1px solid blue; z-index: 101; display:none;}
    #time_help_line.on {display:block;}
    .fc-agendaWeeks-view .fc-content-skeleton thead, .fc-agendaWeeks-view .fc-head {display:none;}
    .fc-agendaWeeks-view .fc-scroller {overflow-x:auto !important; }
    .fc-agendaWeeks-view .fc-scroller>.fc-day-grid { width: auto; display:flex; }
    .fc-agendaWeeks-view .fc-day-grid .fc-row.fc-week.fc-widget-content {min-width:1000px; border-left: 1px solid #ccc;}
    .fc-agendaWeeks-view .fc-scroller>.fc-day-grid > div:first-child {border-left: none;}
    .fc-agendaWeeks-view .day-names {width:100%; height: auto; font-weight:bold; text-align:center; border:none;}
    .fc-agendaWeeks-view .day-names td {border:none;}
    .fc-agendaWeeks-view .fc-bg .fc-day {border-top:none;}
    .fc-basicWeek-view .day-names {display:none;}
    .current-week-big-calendar {background-color:#9400D3;color:white;}

    .event-border {border-radius: 0; border-color: #3a87ad;}
    .fc_assigned_users.sortable_users.event-ekipa-list.ui-sortable li:last-child { margin-bottom: 0; }
    
    /* align pracownika do lewej w evencie */
    .sortable_item_wrap {text-align: left;}
    
    
    .fc-day-grid-event {margin-left: 0; margin-right:0;}
    .ekipa-border, .flota-border { padding: 10px 0 10px 0; }
    .event-ekipa-list, .event-flota-list, .ekipa-border, .flota-border, .role-div, .user-details-row {margin: 0 0 0 0;}
    .event-flota-list {border-bottom: 1px solid #3a87ad;}
    
    .role-div {text-align: center; font-size: 10px; padding-left: 0; list-style-type:none;}
    .role-empty-row {height: 5px;}
    
    .role-div > li > ul {margin: 0;}
    .user-details-row { font-size: 10px; padding-left: 8px;}
    
    
    .sortable_users_details {min-height: 0;}
    
    
    .event-border-right {border-right: 1px solid #3a87ad;}
    .event-border-left {border-left: 1px solid #3a87ad;}
    .event-border-top {border-top: 1px solid #3a87ad;}
    .event-border-bottom {border-bottom: 1px solid #3a87ad;}
    .error {color: red;}
    .break-date-time-picker {width: 110px !important; padding: 5px; font-size: 12px;}
    
    .user_orange_color {color: #FF9100; }
    .user_span {position: relative; z-index: 3;}
    .fc_user {position: relative;}
    .user-department-box {position: absolute; left: 0; top: 0; height: 10px; width: 10px; z-index: 13;}
    
    .zone {height:100%; position:absolute; text-align: center; line-height: 23px; font-size:8px; color:white;}
    .event_zone {background-color:transparent;}
    .work_zone {z-index:1; background-color:rgb(199,202,206);}
    .break_zone {z-index: 2; background-color:rgb(240,108,95); line-height:14px;}
    
    .sortable_users li, .vehicle_box {background-color: white;}
    
    .backlight-user .work_zone { background-color: lime; }
    
    .toggle_box_details { display: none;}
    
    .left_panel_users, .left_panel_flota {width: 100% !important;}
    .toggle_box_details, .toggle_box_general, .user-role-sortable {background-color: white;}
    .space-div {height: 2px; background-color: white; width: calc(100%-2px);}
    .space-between-events {height: 10px; border-top: 1px solid #3a87ad;; width: 100%;}
    body .fc-agendaWeeks-view .fc-content-skeleton {padding-top: 35px; margin-top: 24px;}
    .active-filter-day { background-color: #99CCFF;}
    
   
}
    
    
    ');

$this->registerJsFile('//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js' ,['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js' ,['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('//ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.min.js' ,['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('//ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular-sanitize.js' ,['position' => \yii\web\View::POS_HEAD]);

$userTabUrl = Url::to(['/planboard/user-tab']);
$vehicleTabUrl = Url::to(['/planboard/vehicle-tab']);

$angularJS = '
    window.planboard_data_range = [];
    var planboardApp = angular.module("planboardApp", ["ngSanitize"]).run(function($rootScope,$window,$timeout,restService) {
        $rootScope.user_type_option = '.Json::encode($user_types).';
        $rootScope.skills = '.Json::encode($skills).';
        $rootScope.departments = '.Json::encode($departments).';
        $rootScope.ekipa_filter = {};
        $rootScope.planboard_data_range = $window.planboard_data_range;

        $rootScope.user_search = function (row) {
          return (angular.lowercase(row.first_name).indexOf(angular.lowercase($rootScope.user_query) || "") !== -1 ||
            angular.lowercase(row.last_name).indexOf(angular.lowercase($rootScope.user_query) || "") !== -1);
        };

        $rootScope.user_available = function (row) {
            if ( typeof window.event_filter != "undefined" && window.event_filter[row.id] == 0 ) {
                return false;
            }
            return true;
        }

        $rootScope.vehicle_search = function (row) {
          return (angular.lowercase(row.vehicle.name).indexOf(angular.lowercase($rootScope.vehicle_query) || "") !== -1 ||
            angular.lowercase(row.vehicle.registration_number).indexOf(angular.lowercase($rootScope.vehicle_query) || "") !== -1);
        };

        $rootScope.updateTabs = function(){
        
            restService.getUserTab()
                .then(
                    function( data ) {
                        $rootScope.ekipa = data;
                    },
                    function( obj ) {
                        //console.log(obj);
                    }
                );

            restService.getVehicleTab()
                .then(
                    function( data ) {
                        $rootScope.flota = data;
                    },
                    function( obj ) {
                     //   console.log(obj);
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
            if(
                $window.planboard_data_range.length > 0 && 
                !(
                    $window.planboard_data_range.length == $rootScope.planboard_data_range.length && 
                    $window.planboard_data_range[0] == $rootScope.planboard_data_range[0]
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
                getVehicleTab: getVehicleTab,
            });

            function getUserTab( binder, count ) {
                var request = $http({
                    method: "GET",
                    url: "'.$userTabUrl.'"
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
                    url: "'.$vehicleTabUrl.'"
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
          $( "#draggable_users li" ).draggable({
            connectToSortable: "body .sortable_users",
            helper: "clone",
            revert: "invalid",
          });
        },
      };
    });

    planboardApp.directive("draggablevehicle", function() {
      return {
        restrict:"A",
        controller: function($scope) {
          $( "#draggable_vehicles li" ).draggable({
            connectToSortable: "body .sortable_vehicles",
            helper: "clone",
            revert: "invalid",
          });
        },
      };
    });

    planboardApp.filter("getDateBG", function ($rootScope) {

        return function (data) {
    
            if($rootScope.planboard_data_range.length == 0){
                return;
            }

            var vacation = data.vacations, 
            eventUsers = data.eventUsers || data.events;

            var user_events_week = [ 
                {
                    "confirmed": "0", 
                    "not_confirmed": 0, 
                    "event": 0,
                    "half_event": 0,
                },
                {
                    "confirmed": "0", 
                    "not_confirmed": 0, 
                    "event": 0,
                    "half_event": 0,
                }, 
                {
                    "confirmed": "0", 
                    "not_confirmed": 0, 
                    "event": 0,
                    "half_event": 0,
                }, 
                {
                    "confirmed": "0", 
                    "not_confirmed": 0, 
                    "event": 0,
                    "half_event": 0,
                }, 
                {
                    "confirmed": "0", 
                    "not_confirmed": 0, 
                    "event": 0,
                    "half_event": 0,
                }, 
                {
                    "confirmed": "0", 
                    "not_confirmed": 0, 
                    "event": 0,
                    "half_event": 0,
                }, 
                {
                    "confirmed": "0", 
                    "not_confirmed": 0, 
                    "event": 0,
                    "half_event": 0,
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

                    if(element.start_date == null || element.end_date == null){
                        return "";
                    }

                    var start = element.start_date,
                    end = element.end_date;
                    
                    if($.inArray(start, $rootScope.planboard_data_range) !== -1){
                        var start_index = $rootScope.planboard_data_range.indexOf(start);
                        if(element.status == 10){
                            user_events_week[start_index]["confirmed"] = 1;
                        } else {
                            user_events_week[start_index]["not_confirmed"] = 1;
                        }

                        if($.inArray(end, $rootScope.planboard_data_range) == -1){
                            for (var i = start_index; i < user_events_week.length; i++) { 
                                if(element.status == 10){
                                    user_events_week[i]["confirmed"] = 1;
                                } else {
                                    user_events_week[i]["not_confirmed"] = 1;
                                }
                            }
                        }
                        
                    }

                    if($.inArray(end, $rootScope.planboard_data_range) !== -1){
                        var end_index = $rootScope.planboard_data_range.indexOf(end);
                        if(element.status == 10){
                            user_events_week[end_index]["confirmed"] = 1;
                        } else {
                            user_events_week[end_index]["not_confirmed"] = 1;
                        }

                        if($.inArray(start, $rootScope.planboard_data_range) == -1){
                            for (var i = end_index-1; i >= 0; i--) { 
                                if(element.status == 10){
                                    user_events_week[i]["confirmed"] = 1;
                                } else {
                                    user_events_week[i]["not_confirmed"] = 1;
                                }
                            }
                        }
                    }

                    if($.inArray(start, $rootScope.planboard_data_range) !== -1 && $.inArray(end, $rootScope.planboard_data_range) !== -1){ 
                        for (var i=start_index; i <= end_index; i++) { 
                            if(element.status == 10){
                                user_events_week[i].confirmed = 1;
                            } else {
                                user_events_week[i].not_confirmed = 1;
                            }
                        }
                    }

                    var stard_date_obj = new Date(start.split("-")[0],start.split("-")[1],start.split("-")[2]);
                    var end_date_obj = new Date(end.split("-")[0],end.split("-")[1],end.split("-")[2]);
                    

                    if(stard_date_obj.getTime() < week_start_date_obj.getTime() && end_date_obj.getTime() > week_end_date_obj.getTime() ){
                        for (var i=0; i <= 6; i++) { 
                            if(element.status == 10){
                                user_events_week[i].confirmed = 1;
                            } else {
                                user_events_week[i].not_confirmed = 1;
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
                    if(element.start_time == null || element.end_time == null){
                        return "";
                    }
                    
                   

                    var start = moment(element.start_time);
                    var end = moment(element.end_time);
                    
                    // jeżeli jest start, a nie ma końca
                    if( $.inArray(start.format("YYYY-MM-DD"), $rootScope.planboard_data_range) !== -1 && $.inArray(end.format("YYYY-MM-DD"), $rootScope.planboard_data_range)  == -1 ){
                        var start_index = $rootScope.planboard_data_range.indexOf(start.format("YYYY-MM-DD"));
                     
                        workingMinutesInDay[start_index] += 24*60-start.diff(start.format("YYYY-MM-DD"), "minutes");
                     
                        for (var i = start_index+1; i < user_events_week.length; i++) {
                            workingMinutesInDay[i] += 24 * 60;
                        }
                        
                    }
                    
                    // jeżeli jest koniec, a nie ma startu
                    if( $.inArray(start.format("YYYY-MM-DD"), $rootScope.planboard_data_range) == -1 && $.inArray(end.format("YYYY-MM-DD"), $rootScope.planboard_data_range)  !== -1 ){
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
                        if (start.format("YYYY-MM-DD") != end.format("YYYY-MM-DD")) {
                        
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

                if(element.event == 1){
                    bg_str += event_color+" "+from+"%,"+event_color+" "+to+"%,";
                } else if(element.not_confirmed == 1 && element.confirmed == 0){
                    bg_str += vacation_s_not_confirmed+" "+from+"%,"+vacation_s_not_confirmed+" "+to+"%,";
                } else if(element.confirmed == 1) {
                    bg_str += vacation_s_confirmed+" "+from+"%,"+vacation_s_confirmed+" "+to+"%,";
                } else if(element.half_event == 1) {
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





';

$this->registerJs($angularJS,\yii\web\View::POS_HEAD);

?>

</div>


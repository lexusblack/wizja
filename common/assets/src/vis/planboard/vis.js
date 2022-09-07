var loadedEvents = [];
var activeEvent = false;
var schedules = [];
$(function(){
    var body = $("body");



    // context menu initialing function
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
                            e.preventDefault();
                            $menu.hide();

                            var $invokedOn = $menu.data("invokedOn");
                            var $selectedMenu = $(e.target);
                            var $event_id = settings.event_id;
                            var $user_id = settings.user_id;
                            var $vehicle_id = settings.vehicle_id;
                            var $start = settings.start;
                            var $end = settings.end;

                            settings.menuSelected.call(this, $invokedOn, $selectedMenu, $event_id, $user_id, $vehicle_id, $start, $end);
                        });

                    return false;
                });

                $("body").click(function () {
                    $(settings.menuSelector).hide();
                });
            });
        };
    })(jQuery, window);

    // podświetlanie dnia na angularze (menu), resetowanie przy zmianie zakładki
    $(".tab_link").click(function(){
        $(".activeDayHeader").removeClass("activeDayHeader");
        $(".active").removeClass("active");

    });

    // poruszsanie lewym panelem
    var draggable_left_panel = $("#draggable-left-panel");
    draggable_left_panel.draggable({
        handle: "#btn-move-panel",
        zIndex: 999
    });
    draggable_left_panel.data({
        "originalLeft": draggable_left_panel.css("left"),
        "originalTop": draggable_left_panel.css("top")
    });
    draggable_left_panel.find(".reset").click(function(){
        draggable_left_panel.css("left", draggable_left_panel.data("originalLeft"));
        draggable_left_panel.css("top", draggable_left_panel.data("originalTop"));
    });
    // koniec poruszania lewym panelem

    // DOM element where the Timeline will be attached
    var container = document.getElementById('visualization');
    var items = new vis.DataSet();

    // pobieramy dane
    fetchData();
    // Configuration for the Timeline
    if (window.innerWidth>1500)
    {
        d=10;
    }else{
        d = 7;
    }
    var options = {
        start: momentToDate(moment(window.date_start + " 00:00")),
        end: momentToDate(moment(window.date_start + " 00:00").add(d, 'days')),
        minHeight: 500,
        height:window.innerHeight-50,
        zoomMin: 1000*60*60*24*2, // 3 dni
        zoomMax: 1000*60*60*24*30*3, // 3 miesiące
        order: function(a, b) {
            return a.order - b.order;
        },
        dataAttributes: ['id', 'order'],
        visibleFrameTemplate: function (item) {
            if (item.visibleFrameTemplate) {
                return item.visibleFrameTemplate;
            }
        },
        orientation: { axis: 'top', item:'top' },
        tooltip: {
            followMouse: false
        },
        moveable: true,
        zoomable: false,
        format: {
            majorLabels: {
                hour: 'DD-MM-YYYY dd'
            },
            minorLabels: {
                hour: 'H'
            }
        },
        timeAxis: {
            scale: 'hour',
            step: 2
        }
    };

    // Create a Timeline
    var timeline = new vis.Timeline(container, items, options);

            $("#close-all-button").click(function(e){
        e.preventDefault();
        if ($(this).hasClass("details"))
        {
            
            $(this).removeClass("details");
            $(".event_time_wrapper").each(function(){ 
            $(this).parent().find(".toggle_box_details").hide();
            $(this).parent().find(".toggle_box_general").show();
            
            });
            timeline.redraw();
        }else{
            $(this).addClass("details");
            $(".event_time_wrapper").each(function(){ 
            $(this).parent().find(".toggle_box_details").show();
            $(this).parent().find(".toggle_box_general").hide();
            
            });
            timeline.redraw();
        }


    });

    // TimeHelper2
    $("#toggle_time_helper2").click(function(){
        $(this).toggleClass("on");
        $(".custom-time-bar").toggleClass("off");
    });

    // Tooltip for TimeHelper2
    var tooltip = $("#custom-time-bar-tooltip");
    tooltip.html(moment(window.date_start).add(36, "hours").format("HH:mm DD-MM-YYYY"));
    timeline.addCustomTime(moment(window.date_start).add(36, "hours").format("YYYY-MM-DD HH:mm"), 'custom-time-bar');

    timeline.on("timechange", function (properties) {
        tooltip.html(moment(properties.time).format("HH:mm DD-MM-YYYY"));
    });


    setSortableElements();
    setContextMenu();
    changeVisMajorCellWidth();
    highlightCurrentWeek();

    timeline.on("rangechanged", function (prop) {
        var day = prop.start.getDate();
        var month = prop.start.getMonth()+1;
        var year = prop.start.getFullYear();
        if (month < 10) {
            month = "0" + month;
        }
        if (day < 10) {
            day = "0" + day;
        }

        window.date_start = year + "-" + month + "-" + day;
        changeAngularMenuFirstDay(year + "-" + month + "-" + day);
                activeEvent = false;
        changeAngularMenuEvent(activeEvent);
        fetchAndRedraw();
    });

    // tooltip only over header of event
    body.on("mouseover", ".event_content", function(){
        $(".vis-tooltip").css("display", "none");

    });
    body.on("mouseout", ".event_content", function(){
        $(".vis-tooltip").css("display", "block");
    });

    // switch - moveable on/off
    body.on("change", "#moveable", function(){

        timeline.setOptions({ moveable: !$(this).prop("checked"), orientation: {item:'top'} });

    });

    // rozwijanie i zwijanie eventów
    body.on("click", ".event_time_wrapper", function() {
        // rozwijanie eventów
        $(this).parent().find(".toggle_box_details").toggle();
        $(this).parent().find(".toggle_box_general").toggle();
        activeEvent = $(this).data("eventid");
        changeAngularMenuEvent(items.get(activeEvent));
        // przesunięcie eventów, które sa poniżej do dołu (żeby nie zasłaniać ich rozwiniętym eventem)
        for (var i = $(this).closest(".vis-item").data("order")+1; i <= $(".vis-item").length; i++) {
            var prevIndex = i - 1;
            var prevElement = $(".vis-item[data-order='"+prevIndex+"'] ");
            $(".vis-item[data-order='"+i+"'] ").css("top", parseInt(prevElement.css("top")) + parseInt(prevElement.css("height"))+10+"px");
        }
        timeline.redraw();
    });

    body.on("click", ".ekipa_link", function() {
        // rozwijanie eventów
        $(".toggle_box_details").show();
        $(".toggle_box_general").hide();
        $(".vehicle_box").hide();
        activeEvent = $(this).data("eventid");
        timeline.redraw();
    });

    body.on("click", ".flota_link", function() {
        // rozwijanie eventów
        $(".toggle_box_details").hide();
        $(".toggle_box_general").hide();
        $(".vehicle_box").show();
        activeEvent = $(this).data("eventid");
        timeline.redraw();
    });

    body.on("click", ".assign-users-button", function(e) {
        // rozwijanie eventów
        e.preventDefault();
        var index = loadedEvents.indexOf($(this).data("eventid"));
        if (index !== -1) loadedEvents.splice(index, 1);
        var modal = $("#ekipa_modal");
        modal.find(".modalContent").empty().load($(this).attr("href"));
        modal.modal("show"); 
    });

    body.on("click", ".assign-vehicles-button", function(e) {
        // rozwijanie eventów
        e.preventDefault();
        var index = loadedEvents.indexOf($(this).data("eventid"));
        if (index !== -1) loadedEvents.splice(index, 1);
        var modal = $("#vehicle_modal");
        modal.find(".modalContent").empty().load($(this).attr("href"));
        modal.modal("show"); 
    });

    var zoom_level = 3;
    // oddalanie widoku == zoom out
    $("#zoom_out").click(function () {
        var current_start = getTimelineDateStart();
        var current_end = getTimelineDateEnd();
        var new_start, new_end;

        switch (zoom_level) {
            // z 3 dni robimy 5
            case 0:
                timeline.setOptions({
                    format: {
                        minorLabels: {
                            hour: 'H'
                        }
                    }
                });
                new_start = moment(current_start).subtract(1, "day").format("YYYY-MM-DD");
                new_end = moment(current_end).format("YYYY-MM-DD");

                showPeriod(new_start, current_end);
                zoom_level = 1;
                break;
            // z 3 dni robimy 5
            case 1:
                timeline.setOptions({
                    format: {
                        minorLabels: {
                            hour: 'H'
                        }
                    }
                });
                new_start = moment(current_start).subtract(1, "day").format("YYYY-MM-DD");
                new_end = moment(current_end).add(1, "day").format("YYYY-MM-DD");
                showPeriod(new_start, new_end);
                zoom_level = 2;
                break;
            // z 5 dni robimy 7
            case 2:
                timeline.setOptions({
                    format: {
                        minorLabels: {
                            hour: 'H'
                        }
                    },
                    timeAxis: {
                        scale: 'hour',
                        step: 3
                    }
                });
                new_start = moment(current_start).subtract(1, "day").format("YYYY-MM-DD");
                new_end = moment(current_end).add(1, "day").format("YYYY-MM-DD");
                showPeriod(new_start, new_end);
                zoom_level = 2;
                break;
            // z 7 dni robimy 14
            case 3:
                timeline.setOptions({
                    timeAxis: {
                        scale: 'day'
                    },
                    format: {
                        majorLabels: {
                            day: ''
                        },
                        minorLabels: {
                            day: 'DD-MM-YYYY dd'
                        }
                    }
                });
                new_start = moment(current_start).subtract(3, "day").format("YYYY-MM-DD");
                new_end = moment(current_end).add(4, "day").format("YYYY-MM-DD");
                showPeriod(new_start, new_end);
                zoom_level = 4;
                break;
            // z 14 dni robimy 28 - miesiąc
            case 4:
                timeline.setOptions({
                    timeAxis: {
                        step: 2
                    }
                });
                new_start = moment(current_start).subtract(7, "day").format("YYYY-MM-DD");
                new_end = moment(current_end).add(7, "day").format("YYYY-MM-DD");
                showPeriod(new_start, new_end);
                zoom_level = 5;
                break;
            // z miesiąca (28 dni) robimy 2 miesiące (56 dni)
            case 5:
                new_start = moment(current_start).subtract(14, "day").format("YYYY-MM-DD");
                new_end = moment(current_end).add(14, "day").format("YYYY-MM-DD");
                showPeriod(new_start, new_end);
                zoom_level = 6;
                break;
            // z 2 miesięcy (56 dni) robimy 3 miesiące (84 dni)
            case 6:
                new_start = moment(current_start).subtract(14, "day").format("YYYY-MM-DD");
                new_end = moment(current_end).add(14, "day").format("YYYY-MM-DD");
                showPeriod(new_start, new_end);
                zoom_level = 7;
                break;
        }
    });

    // przyblizanie widoku == zoom in
    $("#zoom_in").click(function () {
        var current_start = getTimelineDateStart();
        var current_end = getTimelineDateEnd();
        var new_start, new_end;
        switch (zoom_level) {
            // z 3 dni robimy 2
            case 1:
                timeline.setOptions({
                    format: {
                        minorLabels: {
                            hour: 'H'
                        }
                    }
                });
                new_start = moment(current_start).add(1, "day").format("YYYY-MM-DD");
                new_end = moment(current_end).subtract(1, "day").format("YYYY-MM-DD");
                showPeriod(new_start, new_end);
                zoom_level = 0;
                break;
            // z 5 dni robimy 3
            case 2:
                timeline.setOptions({
                    format: {
                        minorLabels: {
                            hour: 'H'
                        }
                    }
                });
                new_start = moment(current_start).add(1, "day").format("YYYY-MM-DD");
                new_end = moment(current_end).subtract(1, "day").format("YYYY-MM-DD");
                showPeriod(new_start, new_end);
                zoom_level = 1;
                break;
            // z 7 dni robimy 5
            case 3:
                timeline.setOptions({
                    format: {
                        minorLabels: {
                            hour: 'H'
                        }
                    }
                });
                new_start = moment(current_start).add(1, "day").format("YYYY-MM-DD");
                new_end = moment(current_end).subtract(1, "day").format("YYYY-MM-DD");
                showPeriod(new_start, new_end);
                zoom_level = 2;
                break;
            // z 14 dni robimy 7
            case 4:
                timeline.setOptions({
                    format: {
                        majorLabels: {
                            hour: 'DD-MM-YYYY dd'
                        },
                        minorLabels: {
                            hour: 'H'
                        }
                    },
                    timeAxis: {
                        scale: 'hour',
                        step: 2
                    }
                });
                new_start = moment(current_start).add(3, "day").format("YYYY-MM-DD");
                new_end = moment(current_end).subtract(4, "day").format("YYYY-MM-DD");
                showPeriod(new_start, new_end);
                zoom_level = 3;
                break;
            // z 1 miesiące (28 dni) robimy 2 tygodnie (14 dni)
            case 5:
                new_start = moment(current_start).add(7, "day").format("YYYY-MM-DD");
                new_end = moment(current_end).subtract(7, "day").format("YYYY-MM-DD");
                showPeriod(new_start, new_end);
                zoom_level = 4;
                break;
            // z 2 miesięcy (56 dni) robimy 1 miesiąc (28 dni)
            case 6:
                new_start = moment(current_start).add(14, "day").format("YYYY-MM-DD");
                new_end = moment(current_end).subtract(14, "day").format("YYYY-MM-DD");
                showPeriod(new_start, new_end);
                zoom_level = 5;
                break;
            // z 3 miesięcy (84 dni) robimy 2 miesiące (56 dni)
            case 7:
                new_start = moment(current_start).add(14, "day").format("YYYY-MM-DD");
                new_end = moment(current_end).subtract(14, "day").format("YYYY-MM-DD");
                showPeriod(new_start, new_end);
                zoom_level = 6;
                break;
        }
    });

    // przesuwanie w lewo kalendarza
    $("#move_left").click(function () {
        var current_start = getTimelineDateStart();
        var current_end = getTimelineDateEnd();
        var number = $("#period_number").val();
        var period = "day";
        var period_text = $("#period_text");
        if (parseInt(period_text.val()) === 2) {
            period = "weeks"
        }
        if (parseInt(period_text.val()) === 3) {
            period = "months"
        }

        var new_start = moment(current_start).subtract(number, period).format("YYYY-MM-DD");
        var new_end = moment(current_end).subtract(number, period).format("YYYY-MM-DD");
        showPeriod(new_start, new_end);
    });

    // przesuwanie w prawo kalendarza
    $("#move_right").click(function () {
        var current_start = getTimelineDateStart();
        var current_end = getTimelineDateEnd();
        var number = $("#period_number").val();
        var period = "day";
        var period_text = $("#period_text");
        if (parseInt(period_text.val()) === 2) {
            period = "weeks"
        }
        if (parseInt(period_text.val()) === 3) {
            period = "months"
        }

        var new_start = moment(current_start).add(number, period).format("YYYY-MM-DD");
        var new_end = moment(current_end).add(number, period).format("YYYY-MM-DD");
        showPeriod(new_start, new_end);
    });

    // kliknięcie dnia na kalendarzu
    body.on("click", ".vis-minor", function () {
        if ($(this).text().length >= 10) {
            var day = $(this).text().substr(0, 2);
            var month = $(this).text().substr(3, 2);
            var year = $(this).text().substr(6, 4);
            changeAngularMenuFirstDay(year + "-" + month + "-" + day);
            highlightCurrentWeek();
        }
    });
    body.on("click", ".vis-major", function () {
        if ($(this).text().length >= 10) {
            var day = $(this).text().substr(0, 2);
            var month = $(this).text().substr(3, 2);
            var year = $(this).text().substr(6, 4);
            changeAngularMenuFirstDay(year + "-" + month + "-" + day);
            highlightCurrentWeek();
        }
    });

    window.addEventListener("resize", function () {
        changeVisMajorCellWidth();
    });

    // refresh timelina po zamknięciu modala
    $(".modal").on("hide.bs.modal", function () {
        angular.element(document.getElementById("planboardApp")).scope().updateTabs();
        fetchAndRedraw();
    });

    $("button#toggle_time_helper").click(toogleTimeHelperLine);

    // *********** Functions below ********** //

    function highlightCurrentWeek() {
        $(".active_menu_days").removeClass("active_menu_days");

        first_day = moment(window.date_start).format("YYYY-MM-DD");
        second_day = moment(window.date_start).add(1, "day").format("YYYY-MM-DD");
        third_day = moment(window.date_start).add(2, "day").format("YYYY-MM-DD");
        fourth_day = moment(window.date_start).add(3, "day").format("YYYY-MM-DD");
        fifth_day = moment(window.date_start).add(4, "day").format("YYYY-MM-DD");
        sixth_day = moment(window.date_start).add(5, "day").format("YYYY-MM-DD");
        seventh_day = moment(window.date_start).add(6, "day").format("YYYY-MM-DD");
        setTimeout(function () {
            body.find(".vis-text.vis-minor").each(function () {
                if ($(this).text().length >= 10) {
                    var day = $(this).text().substr(0, 2);
                    var month = $(this).text().substr(3, 2);
                    var year = $(this).text().substr(6, 4);
                    if (
                        moment(year + "-" + month + "-" + day).format("YYYY-MM-DD") === first_day ||
                        moment(year + "-" + month + "-" + day).format("YYYY-MM-DD") === second_day ||
                        moment(year + "-" + month + "-" + day).format("YYYY-MM-DD") === third_day ||
                        moment(year + "-" + month + "-" + day).format("YYYY-MM-DD") === fourth_day ||
                        moment(year + "-" + month + "-" + day).format("YYYY-MM-DD") === fifth_day ||
                        moment(year + "-" + month + "-" + day).format("YYYY-MM-DD") === sixth_day ||
                        moment(year + "-" + month + "-" + day).format("YYYY-MM-DD") === seventh_day
                    ) {
                        $(this).addClass("active_menu_days");
                    }
                }
            });
            body.find(".vis-text.vis-major").each(function () {
                if ($(this).text().length >= 10) {
                    var day = $(this).text().substr(0, 2);
                    var month = $(this).text().substr(3, 2);
                    var year = $(this).text().substr(6, 4);
                    if (
                        moment(year + "-" + month + "-" + day).format("YYYY-MM-DD") === first_day ||
                        moment(year + "-" + month + "-" + day).format("YYYY-MM-DD") === second_day ||
                        moment(year + "-" + month + "-" + day).format("YYYY-MM-DD") === third_day ||
                        moment(year + "-" + month + "-" + day).format("YYYY-MM-DD") === fourth_day ||
                        moment(year + "-" + month + "-" + day).format("YYYY-MM-DD") === fifth_day ||
                        moment(year + "-" + month + "-" + day).format("YYYY-MM-DD") === sixth_day ||
                        moment(year + "-" + month + "-" + day).format("YYYY-MM-DD") === seventh_day
                    ) {
                        $(this).addClass("active_menu_days");
                    }
                }
            });
        }, 400);
    }

    // żeby dzień na górnej belce miał długość całej kolumny (gdy godziny są widoczne)
    function changeVisMajorCellWidth() {
        setTimeout(function () {
            $(".vis-time-axis.vis-foreground").find(".vis-text.vis-major.vis-h0").each(function () {
                var width = $(this).prevAll(".vis-minor").first().css("width").replace(/[^-\d\.]/g, '') * 12;
                $(this).css("width", width);
            });
            $(".vis-time-axis.vis-foreground").find(".vis-text.vis-major.vis-h0-h4").each(function () {
                var width = $(this).prevAll(".vis-minor").first().css("width").replace(/[^-\d\.]/g, '') * 6;
                $(this).css("width", width);
            });
        }, 900);
    }

    // change timeline start and end
    function showPeriod(start, end) {
        timeline.setOptions({ start: momentToDate(moment(start + " 00:00:00")), end: momentToDate(moment(end+" 00:00:00")), orientation: {item:'top'} });
    }

    function changeAngularMenuFirstDay(day) {
        window.date_start = day;
        angular.element(document.getElementById("planboardApp")).scope().changeUserPanel(window.date_start);
    }

    function changeAngularMenuEvent(id){
            angular.element(document.getElementById("planboardApp")).scope().changeUserPanelById(id);
    }


    // dzień, który jest aktualnie początkiem timelina
    function getTimelineDateStart() {
        return getTimelineDate(timeline.getWindow().start);
    }

    // dzień, który jest aktualnie końcem timelina
    function getTimelineDateEnd() {
        return getTimelineDate(timeline.getWindow().end);
    }

    function getTimelineDate(date) {
        var day = date.getDate();
        var month = date.getMonth()+1;
        var year = date.getFullYear();
        if (month < 10) {
            month = "0" + month;
        }
        if (day < 10) {
            day = "0" + day;
        }
        return year + "-" + month + "-" + day;
    }


    function fetchData() {
        //items.clear();
        var end = moment(window.date_start).add(7, 'days').format("YYYY-MM-DD");

        if (timeline) {
            var day = timeline.getWindow().end.getDate();
            var month = timeline.getWindow().end.getMonth()+1;
            var year = timeline.getWindow().end.getFullYear();
            if (month < 10) {
                month = "0" + month;
            }
            if (day < 10) {
                day = "0" + day;
            }
            end = year + "-" + month + "-" + day;
        }
        $.ajax({
            type: 'post',
            url: "get-events?start=" + window.date_start + "&end=" + end,
            data: {ids: JSON.stringify(loadedEvents)},
            dataType: 'json',
            async: false,
            success: function (events) {
                for (var i = 0; i < events.length; i++) {
                    loadedEvents.push( events[i].id);
                    items.remove({id:events[i].id});
                    items.add({
                        id: events[i].id,
                        order: events[i].order,
                        start: momentToDate(moment(events[i].start)),
                        end: momentToDate(moment(events[i].end)),
                        content: events[i].name,
                        visibleFrameTemplate: events[i].content,
                        title: events[i].tooltip,
                        schedules: events[i].schedules,
                    });
                }
            }
        });
    }

    function fetchAndRedraw() {
        fetchData();
        setSortableElements();
        setContextMenu();
        changeVisMajorCellWidth();
        highlightCurrentWeek();

        tooltip.html(moment(window.date_start).add(36, "hours").format("HH:mm DD-MM-YYYY"));
        timeline.removeCustomTime('custom-time-bar');
        timeline.addCustomTime(moment(window.date_start).add(36, "hours").format("YYYY-MM-DD HH:mm"), 'custom-time-bar');
    }

    // set menu pod prawym przyciskiem
    function setContextMenu() {
        // event menu
        $(".event_time_wrapper").each(function() {
            $(this).contextMenu({
                menuSelector: "#eventMenu",
                parentContainer: "#visualization",
                event_id: $(this).data("eventid"),
                start: $(this).data("start"),
                end: $(this).data("end"),
                menuSelected: function (invokedOn, selectedMenu, event_id, user_id, vehicle_id, start, end) {
                    if(selectedMenu.hasClass("open_breakes_modal")){
                        openEventBreakesModal(selectedMenu.attr("href")+"?event_id="+event_id);
                    }
                    if (selectedMenu.hasClass("open_event")) {
                        var win = window.open(selectedMenu.attr("href")+"?id="+event_id, "_blank");
                        win.focus();
                    }
                    if (selectedMenu.hasClass("fullscreen")) {
                        showPeriod(start.substring(0, 10), end.substring(0, 10));
                    }
                    if(selectedMenu.hasClass("open_custom_hours_modal")){
                        openEventCustomWorkingTimeModal(selectedMenu.attr("href")+"?event_id="+event_id);
                    }
                }
            });
        });

        // user menu
        $(".fc_user").each(function () {
            $(this).contextMenu({
                menuSelector: "#userMenu",
                parentContainer: "#visualization",
                event_id: $(this).data("eventid"),
                user_id: $(this).data("userid"),
                menuSelected: function (invokedOn, selectedMenu, event_id, user_id) {
                    if(selectedMenu.hasClass("open_ekipa_modal")){
                        var index = loadedEvents.indexOf(event_id);
                        if (index !== -1) loadedEvents.splice(index, 1);
                        openUserModal(user_id, event_id, false, 0);
                    }
                    if (selectedMenu.hasClass("delete_event_user")) {
                        if(confirm("Czy chcesz usunąć pracownika?")) {
                            var data = {
                                itemId: user_id,
                                add: 0
                            };
                            $.post(selectedMenu.attr("href")+"?id="+event_id, data, function () {
                                var index = loadedEvents.indexOf(event_id);
                                if (index !== -1) loadedEvents.splice(index, 1);
                                fetchAndRedraw();
                            });
                        }
                    }
                }
            });
        });

        // vehicle menu
        $(".fc_vehicles").each(function () {
            $(this).contextMenu({
                menuSelector: "#vehicleMenu",
                parentContainer: "#visualization",
                event_id: $(this).data("eventid"),
                user_id: null,
                vehicle_id: $(this).data("carid"),
                menuSelected: function (invokedOn, selectedMenu, event_id, user_id, vehicle_id) {
                    if(selectedMenu.hasClass("open_vehicle_modal")){
                        var index = loadedEvents.indexOf(event_id);
                        if (index !== -1) loadedEvents.splice(index, 1);
                        openVehicleModal(event_id, vehicle_id, null, false, 0);
                    }
                    if (selectedMenu.hasClass("delete_event_vehicle")) {
                        if(confirm("Czy chcesz usunąć pojazd?")) {
                            var data = {
                                itemId: vehicle_id,
                                add: 0
                            };
                            $.post(selectedMenu.attr("href")+"?id="+event_id, data, function () {
                                var index = loadedEvents.indexOf(event_id);
                                if (index !== -1) loadedEvents.splice(index, 1);
                                fetchAndRedraw();
                            });
                        }
                    }
                }
            });
        });
    }

    function setSortableElements() {
        setSortableEvents();
        setSortableRoles();
        setSortableVehicles();
        setSortableUsersGeneral();
        setSortableUsersDetails();
    }

    // Open modal - user details
    function openUserModal(user_id, event_id, just_assigned, role){
        var modal = $("#ekipa_modal");
        modal.find(".modalContent").empty();
        modal.modal("show");

        if (!role) {
            role = 0;
        }

        if (just_assigned) {
            modal.find(".modalContent").load("user-form?user_id=" + user_id + "&event_id=" + event_id + "&just_assigned=1&role=" + role);
        }
        else {
            modal.find(".modalContent").load("user-form?user_id=" + user_id + "&event_id=" + event_id);
        }
        
    }

    // Open modal - event breaks
    function openEventBreakesModal(url) {
        var modal = $("#event_breaks_modal");
        modal.find(".modalContent").load(url);
        modal.modal("show");
    }

    function openEventCustomWorkingTimeModal(url) {
        var modal = $("#event_custom_working_hours_modal");
        modal.find(".modalContent").load(url, function () {
            $('.new_start').datetimepicker({format: 'yyyy-mm-dd hh:ii', autoclose: true});
            $('.new_end').datetimepicker({format: 'yyyy-mm-dd hh:ii', autoclose: true});
        });
        modal.modal("show");
    }

    // Open modal - vehicle
    function openVehicleModal(event_id, vehicle_id, vehicle_model_id, update_event_vehicle_data, just_assigned){
        var modal = $("#vehicle_modal");
        modal.find(".modalContent").load("vehicle-form?event_id="+event_id+"&vehicle_id="+vehicle_id+"&vehicle_model_id="+vehicle_model_id);
        modal.modal("show");
    }

    // Sortable events
    function setSortableEvents() {
        $(".vis-group").sortable({
            revert: true,
            helper: "clone",
            stop: function (event, ui) {
                var arr_order = [];
                $($(".vis-itemset").find(".vis-item").get().reverse()).each(function (i) {
                    var event_id = $(this).data("id");
                    items.update({id: event_id, order: i});
                    arr_order.push({id: event_id, order: i})
                });
                $.post("update-order", {data: arr_order});
            }
        });
    }

    // Sortable roles
    function setSortableRoles() {
        $(".user-role-sortable").sortable({
            revert: true,
            stop: function (event, ui) {
                var event_id = $(ui.item).data("eventid");
                $.post("delete-all-order-event-role?event_id=" + event_id, null, function () {
                    var i = 0;
                    $(event.target).children().each(function () {
                        var role_id = $(this).data("role");
                        if (role_id !== 0) {
                            $.post("update-order-event-role?event_id=" + event_id + "&role_id=" + role_id + "&order_key=" + i);
                            i++;
                        }
                    });
                    fetchAndRedraw();
                });
            }
        });
    }

    // assign vehicle to event
    function assignVehicle(id, event_id, add, callback) {
        var data = {
            itemId : id,
            add : add ? 1 : 0
        };
        $.post("/admin/vehicle/assign-vehicle?id="+event_id, data, function(response){
            callback(response);
        });
    }

    // sortable vehicles
    function setSortableVehicles() {
        var sortable_vehicles = $(".sortable_vehicles");
        sortable_vehicles.sortable({
            revert: true,
            receive: function(ev, ui) {
                var event_id = $(ev.target).data("eventid");
                var vehicle_id = $(ev.target).data("vehicle");
                var car_id = ui.item.data("carid");
                var car_in_list_count = $(ev.target).find("li[data-carid='"+car_id+"']").length;
                var old_event_id = $(ui.item).data("eventid");

                if(car_in_list_count > 1){
                    $(ev.toElement).remove();
                } else {
                    assignVehicle(car_id,old_event_id,0, function () {});
                    assignVehicle(car_id,event_id,1,function() {
                        openVehicleModal(event_id, car_id, vehicle_id);
                    });
                }
            },
            stop: function (event, ui) {
                var event_id = $(ui.item).data("eventid");
                $.post("delete-all-vehicle-order?event_id=" + event_id, null, function () {
                    var i = 0;
                    $(event.target).children().each(function () {
                        var vehicle_id = $(this).data("carid");
                        $.post("update-order-vehicle?event_id=" + event_id + "&vehicle_id=" + vehicle_id + "&order_key=" + i);
                        i++;
                    });
                    fetchAndRedraw();
                });
            }
        });
        sortable_vehicles.find("li").draggable({
            revert: true,
            connectToSortable: ".sortable_vehicles"
        });
    }

    // Sortable users - general
    function setSortableUsersGeneral() {
        $(".sortable_users").sortable({
            revert: true,
            helper: "clone",
            stop: function (event, ui) {
                var event_id = $(ui.item).data("eventid");
                $.post("delete-all-order-event-general-user?event_id=" + event_id, null, function () {
                    var i = 0;
                    $(event.target).children().each(function () {
                        var user_id = $(this).data("userid");
                        $.post("update-order-event-general-user?event_id=" + event_id + "&event_user=" + user_id + "&order_key=" + i);
                        i++;
                    });
                    fetchAndRedraw();
                });
            },
            receive: function(ev, ui) {
                var event_id = $(ev.target).data("id");
                var event_start = $(ev.target).data("eventstart");
                var event_end = $(ev.target).data("eventend");
                var user_id = ui.item.data("userid");
                var user_in_list_count = $(ev.target).find("li[data-userid='"+user_id+"']").length;
                var alert12h = false;
                var vacation = false;
                var planned_vacation = false;

                var old_event_id = ui.item.data("eventid");
                if (old_event_id) {
                    assignUser(user_id, old_event_id, 0, function () {});
                }

                $.ajax({
                    type: "POST",
                    url: "/admin/crew/is-working-in-close-range?user_id=" + user_id + "&start=" + event_start + "&end=" + event_end,
                    async:false,
                    success: function(response) {
                        if (parseInt(response[0]) === 1) {
                            alert12h = true;
                        }
                        if (parseInt(response[1]) === 1) {
                            vacation = true;
                            alert12h = true;
                        }
                        if (parseInt(response[2]) === 1) {
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
                            assignUser(user_id,event_id,1, function(){ openUserModal(user_id, event_id, true, 0);});
                        }
                    }
                    else {
                        assignUser(user_id, event_id, 1, function(){ openUserModal(user_id, event_id, true, 0);});
                    }
                }
            }
        });
    }

    // Assign User to Event
    function assignUser(id, event_id, add, callback) {
        var data = {
            itemId : id,
            add : add ? 1 : 0
        };

        $.post("/admin/crew/assign-user?id="+event_id, data);
        callback();
    }

    // Sortable users - details
    function setSortableUsersDetails() {
        $(".sortable_users_details").sortable({
            revert: true,
            helper: "clone",
            stop: function (event, ui) {
                var event_id = $(ui.item).data("eventid");
                var role_id = $(ui.item).data("role");
                $.post("delete-all-order-event-role-users?event_id=" + event_id + "&role_id=" + role_id, null, function () {
                    var i = 0;
                    $(event.target).children().each(function () {
                        var user_id = $(this).data("userid");
                        $.post("update-order-event-role-user?event_id=" + event_id + "&role_id=" + role_id + "&event_user=" + user_id + "&order_key=" + i);
                        i++;
                    });
                    fetchAndRedraw();
                });
            },
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
                var user_in_list_count = $(ev.target).find("li[data-userid='"+user_id+"']").length;
                var event_start = $(ev.target).data("eventstart");
                var event_end = $(ev.target).data("eventend");
                var alert12h = false;
                var vacation = false;
                var planned_vacation = false;

                var old_event_id = ui.item.data("eventid");
                if (old_event_id) {
                    assignUser(user_id,old_event_id);
                }

                $.ajax({
                    type: "POST",
                    url: "/admin/crew/is-working-in-close-range?user_id=" + user_id + "&start=" + event_start + "&end=" + event_end,
                    async:false,
                    success: function(response) {
                        if (parseInt(response[0]) === 1) {
                            alert12h = true;
                        }
                        if (parseInt(response[1]) === 1) {
                            vacation = true;
                            alert12h = true;
                        }
                        if (parseInt(response[2]) === 1) {
                            planned_vacation = true;
                            alert12h = true;
                        }
                    },
                    fail: function() {
                        console.log("error");
                    }
                });

                if (user_in_list_count === 1) {
                    var can_assign = true;
                    $.ajax({
                        type: "POST",
                        url: "/admin/crew/is-available?user_id=" + user_id + "&start=" + event_start + "&end=" + event_end,
                        async:false,
                        success: function(response) {
                            if (parseInt(response) !== 1) {
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

                            if (confirm(alertText) ) {
                                assignUserToRole(user_id, event_id, role_id, 1);
                                openUserModal(user_id, event_id, true, role_id);
                            }
                        }
                        else {
                            assignUserToRole(user_id, event_id, role_id, 1);
                            openUserModal(user_id, event_id, true, role_id);
                        }
                    }
                    else {
                        alert("Ta osoba pracuje już w czasie trwania tego eventu");
                    }
                }
                else {
                    alert("Ten pracownik jest już przypisany do tego zadania");
                 }
            }
        });
        $(".sortable_users").find("li").draggable({
            revert: true,
            connectToSortable: ".sortable_users"
        });
    }


    function assignUserToRole(user_id, event_id, role_id, add) {
        $.post("/admin/crew/assign-user-to-role?user_id=" + user_id + "&event_id=" + event_id + "&role_id=" + role_id + "&add=" + add);
    }

    // show/hide time line helper
    function toogleTimeHelperLine(){
        $("#time_help_line").toggleClass("on");
        $("#toggle_time_helper").toggleClass("on")

        $("#visualization").on("mousemove", function(e){
            var calendar_l_o = $("#visualization").offset().left;
            var scroll_left = $("#visualization").scrollLeft();
            $("#time_help_line").css({
                left:  e.pageX-calendar_l_o + scroll_left
            });
        });
    }

    function momentToDate(moment) {
        return new Date(moment.get("year"), moment.get("month"), moment.get("date"), moment.get("hour"), moment.get("minute"));
    }

    angular.element(document.getElementById("planboardApp")).scope().updateTabs();
});


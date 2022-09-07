<?php
/* @var $this \yii\web\View */

use yii\helpers\Json;
use kartik\cmenu\ContextMenu;
use yii\bootstrap\Html;
use kartik\helpers\Enum;

\common\assets\FullcalendarAsset::register($this);
\kartik\cmenu\ContextMenuAsset::register($this);
\sammaye\qtip\QtipAsset::register($this);
//\common\assets\JuiAsset::register($this);

$user = Yii::$app->user;
$months = ["01"=>Yii::t('app', 'Styczeń'), "02"=>Yii::t('app', 'Luty'),"03"=>Yii::t('app', 'Marzec'),"04"=>Yii::t('app', 'Kwiecień'),"05"=>Yii::t('app', 'Maj'),"06"=>Yii::t('app', 'Czerwiec'),"07"=>Yii::t('app', 'Lipiec'),"08"=>Yii::t('app', 'Sierpień'),"09"=>Yii::t('app', 'Wrzesień'),"10"=>Yii::t('app', 'Październik'),"11"=>Yii::t('app', 'Listopad'),"12"=>Yii::t('app', 'Grudzień'),];
$select = Html::dropDownList('year', $year, Enum::yearList(2016, 2025, true), ['class'=>'form-control date-drop form-inline', 'id'=>'year', 'style'=>"float:left;"]);
$select2 = Html::dropDownList('month',$month, $months, ['class'=>'form-control date-drop form-inline', 'style'=>"float:left;", 'id'=>'month']);
$select = preg_replace( "/\r|\n/", "", $select." ".$select2  );
if ($user->can('eventsEventAdd')) {
    $item[] = ['label'=>Yii::t('app', 'Wydarzenie'), 'url'=>['event/create']];
}
if ($user->can('eventMeetingAdd')) {
    $item[] = ['label'=>Yii::t('app', 'Spotkanie'), 'url'=>['meeting/create']];
}
if ($user->can('eventsMeetingsPrivate')) {
    $item[] = ['label'=>Yii::t('app', 'Wydarzenie prywatne'), 'url'=>['personal/create']];
}
if ($user->can('eventRentsAdd')) {
    $item[] = ['label'=>Yii::t('app', 'Wypożyczenie'), 'url'=>['rent/create']];
}
$item[] = '<li class="divider"></li>';
if ($user->can('eventVacationsAdd')) {
    $item[] = ['label'=>Yii::t('app', 'Urlop'), 'url'=>['vacation/create']];
}


$script = <<< 'JS'

function (e, element, target) {
    // $(this).data("date", 'asd');
    var t = $(e.target);
    e.preventDefault();
    
    if (t.closest('div').hasClass("fc-content-skeleton"))
    {
        var index = t.closest('tr').find('td').index(t);
        var date = t.closest('.fc-content-skeleton').prev('.fc-bg').find('td.fc-day').eq(index).data('date');
        t.data('date', date);
    }
    
    
    
    if(t.data('date')==undefined && t.closest('[data-date]').data('date')==undefined)
    {
        e.preventDefault();
        this.closemenu();
        return false;
    }
    
    if(t.data('date')==undefined)
    {
        $(this).data("date", t.closest('[data-date]').data('date'));
    }
    else {
        $(this).data("date", t.data('date'));
    }
    return true;
}
JS;
ContextMenu::begin([
    'items'=>$item,
    'options'=>['tag'=>'div'],
    'pluginOptions'=>[
        'before'=>$script,
        'target' => '.fc-view-container td',
        'onItem'=>'function(context, e)
        {
            
            var date = $(this).data("date")
           
            
            window.location = e.target.href + "?start=" + date;
            return false;
                    
       

        }',
    ]
]);
?>

<?php
if ($user->can('calendarFilters')) {
    echo $this->render('_filters', ['model' => $model]);
} ?>
<div id="calendar">
</div>
<?php
ContextMenu::end();

$this->registerJs('



$("#calendar").fullCalendar({
    lang:"pl",
    schedulerLicenseKey: "CC-Attribution-NonCommercial-NoDerivatives",
    height:'.$height.',
    defaultDate:"'.$defaultDate.'",
    events:'.Json::encode($events, JSON_NUMERIC_CHECK).',
    allDayDefault: false,
    nextDayThreshold: "00:00:00",
    eventOrder: function (a, b, c) {
        return 0;
    },
    eventClick: function(event) {
        if (event.url) {
            window.open(event.url);
            return false;
        }
    },
    eventRender:function(event, element) {
        var content = "";
        var outContent = "";

        if(event.departaments !== undefined){
           outContent += event.departaments;
        }
        if (event.schedules !== undefined)
        {
            for(var i=0; i< event.schedules.length; i++)
            {
                content += event.schedules[i];
            }
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

        if(event.type == "vacation")
        {
            element.css("line-height", "1");
        }

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
                        
//                        my: "center center",
//                        at: "center center",
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
        if (event.border != undefined){
            element.css("border", event.border);
		}
        
		if (event.textColor !== undefined)
		{
		    element.css("color", event.textColor);
		}
    },
    
    eventAfterRender: function(event, element, view)  {
        checkDate(event, element);

    },
    eventAfterAllRender: function() {
        addTimeTable();
        $(".fc-scroller.fc-day-grid-container").append("<div style='."'height:".$height."px;'".'></div>");
        if ($(".fc-today").length){
            $(".fc-day-grid-container").animate({
            scrollTop: $(".fc-today").offset().top-250
            }, 500);
        }
        $(".fc-scroller.fc-day-grid-container").css("overflow-y", "scroll");
    },
    
    dayRender:function(date, cell) 
    {
        var htmlHours = "<table class=\"day-hours\"><tr>";
        for (var i=0; i<24; i=i+2)
        {
            var htmlHour = "<td>" + i + "</td>";
            htmlHours = htmlHours + htmlHour;
        }
        htmlHours  = htmlHours + "</tr></table>";
        cell.append(htmlHours);
    	
		
    }
});

    function checkDate(event, element)
    {
        var $el = $(element);
        
        var dates = new Array(); 
        $el.closest(".fc-row").find("[data-date]").each(function(index, el) {
            dates.push($(el).data("date"));
        });
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
            var first_date_in_a_row_obj = new Date(first_date_in_row[0],first_date_in_row[1]-1,first_date_in_row[2]);
            
            //last date in a row obj
            var last_date_in_row = row_dates[6].split("-");
            last_date_in_row_obj = new Date(last_date_in_row[0],last_date_in_row[1]-1,last_date_in_row[2]);
            
            //end date for block
            var end_date_obj = new Date(split_end.split("-")[0],split_end.split("-")[1]-1,split_end.split("-")[2]);
            //start date for block
            var start_date_obj = new Date(split_start.split("-")[0],split_start.split("-")[1]-1,split_start.split("-")[2]);


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

		        var days_count = last_date_in_row_obj.getTime() - start_date_obj.getTime();
                start_time = days_count - start_time+oneDay;

 				var width = start_time*100/time_in_a_row;
				_this.css("right", "0");
				_this.css("left", "auto");
				_this.css("width", width+"%");

			} else {
                //end date for block
                var end_date_obj = new Date(split_end.split("-")[0],split_end.split("-")[1],split_end.split("-")[2]);
                //start date for block
                var start_date_obj = new Date(split_start.split("-")[0],split_start.split("-")[1],split_start.split("-")[2]);
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

$(".fc-button-group").html("<button class=\'my-prev\'><b><</b></button><button class=\'my-next\'><b>></b></button>");
$(".fc-center").append(\'<form class="form-inline">'.$select.'</form>\');

$("#year").change(function(e){
    window.location.href="/admin/site/calendar?year="+$("#year").val()+"&month="+$("#month").val();
});
$("#month").change(function(e){
    window.location.href="/admin/site/calendar?year="+$("#year").val()+"&month="+$("#month").val();
});
$("button.my-prev").click(function(e){
    window.location.href="/admin/site/calendar'.$prevLink.'";
    
});

$("button.my-next").click(function(e){
    window.location.href="/admin/site/calendar'.$nextLink.'";
});

$(".fc-today-button.fc-button").click(function(e){
    window.location.href="/admin/site/calendar";
});

');


$this->registerJs('
    onWindowResize();
    $(window).resize(function()
    {
        onWindowResize();
    });
    
    function onWindowResize()
    {
        var height = parseInt($(window).height()) - 156;

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
    
    function addTimeTable() {
        $(".fc-day-header").each(function() {
            $(this).append("<table class=\"day-hours\"><tr><td>0</td><td>2</td><td>4</td><td>6</td><td>8</td><td>10</td><td>12</td><td>14</td><td>16</td><td>18</td><td>20</td><td>22</td></tr></table>");
        });
    }

');


$this->registerCss('

.fc-bg > table > tbody > tr .day-hours {
    display:none;
}
.fc-content-skeleton {
    padding-top: 0;
}
.day-hours tr td {
    font-weight: initial;
}

.fc-unthemed .fc-row {
    border-right-color: black;
}

');



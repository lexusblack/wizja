<?php
namespace common\widgets;

use common\helpers\ArrayHelper;
use common\models\Event;
use common\models\form\CalendarSearch;
use common\models\Meeting;
use common\models\Personal;
use common\models\Rent;
use common\models\Vacation;
use DateTime;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\Url;
use Yii;


/**
 * Class CalendarWidget
 * @package common\widgets
 *
 * Długość paska powinna być uwarunkowana datą i godziną montażu oraz datą i godziną demontażu.
 * Jeśli uzupełnione jest pole pakowanie to pasek powinien być wydłużony do pakowania ale odróżniać się od pozostałej części paska
 * (np może być wyszarzony i zakreskowany z napisem pakowanie)
 * Dodatkowo na paskach powinien widnieć inicjał Project Managera,
 * żeby każdy wiedział z kim się kontaktować odnośnie danego eventu.

 */
class CalendarWidget extends Widget
{
    const HOUR_RATIO = 10;

    public $userId;
	public $height = '"auto"';
	public $defaultDate;
	public $currentModelId;
	public $nextLink;
	public $prevLink;
	public $year;
	public $month;

    /**
     * @var CalendarSearch
     */
    protected $_searchModel;

    public $events = [];
    public $event_colors = [];

    public function init() 
    {
        parent::init();
		if (!$this->defaultDate)
		{
			$this->defaultDate = date('Y-m-d');
		}

		$this->_searchModel = new CalendarSearch();

		$this->event_colors = [
            'event_base_bg' => Yii::$app->settings->get('main.eventBaseColor'),
            'event_line_bg' => Yii::$app->settings->get('main.eventLineColor'),
            'packing'=> Yii::$app->settings->get('main.partyColor'),
            'montage'=> Yii::$app->settings->get('main.montageColor'),
            'readiness'=> 'olive',
            'practice'=>'orange',
            'event'=> Yii::$app->settings->get('main.partyColor'),
            'disassembly'=>Yii::$app->settings->get('main.disassemblyColor'),
        ];
        $get_request = Yii::$app->request->get();
        if(isset($get_request["cleanCalendar"])){
            $this->_searchModel->cleanFilter();
        } elseif(!isset($get_request['CalendarSearch'])) {
            $get_request['CalendarSearch'] = $this->_searchModel->loadFilterParamsForCurrentUser();
        }
        $date2 = new DateTime($this->year . '-' . $this->month . '-01');
        $date2->modify('+ 45 days');
        $date1 = new DateTime($this->year . '-' . $this->month . '-01');
        $date1->modify('- 6 days');
        $this->_searchModel->start = $date1->format('Y-m-d H:i:s');
        $this->_searchModel->end = $date2->format('Y-m-d H:i:s');
        $this->_searchModel->search($get_request);
        $this->_setEvents();
        $this->_addMeetings();
        $this->_addRents();
        $this->_addVacations();
        $this->_addPersonal();

        //reset indexes
        $this->events = array_values($this->events);
    }
    
    public function run() 
    {
        parent::run();
        return $this->render('calendarWidget', [
            'events'=>  $this->events,
            'defaultDate'=>$this->defaultDate,
            'height'=>$this->height,
            'model'=>$this->_searchModel,
	        'nextLink' => $this->nextLink,
	        'prevLink' => $this->prevLink,
            'year'=>$this->year,
            'month'=>$this->month
        ]);
        
    }

    protected function _setEvents()
    {
        $models = $this->_searchModel->getEvents();
        if ($models === null) return;

        foreach ($models as $k => $model) {
            $event_line_color = $this->event_colors['event'];
            $color_line = $this->event_colors["event_line_bg"];
            $color_base = $this->event_colors["event_base_bg"];

            if ($model->type==2)
            {
                $color_line = Yii::$app->settings->get('main.produkcjaLineColor');
                $color_base = Yii::$app->settings->get('main.produkcjaLineColor');
                $event_line_color = Yii::$app->settings->get('main.produkcjaColor');
            }
            if ($model->type==3)
            {
                $color_line = Yii::$app->settings->get('main.biuroLineColor');
                $color_base = Yii::$app->settings->get('main.biuroLineColor');
                $event_line_color = Yii::$app->settings->get('main.biuroColor');
            }
            if ($model->type==4)
            {
                $color_line = Yii::$app->settings->get('main.grafikaLineColor');
                $color_base = Yii::$app->settings->get('main.grafikaLineColor'); 
                $event_line_color = Yii::$app->settings->get('main.grafikaColor');
            }
            if ($model->type==5)
            {
                $color_line = Yii::$app->settings->get('main.magazynLineColor');
                $color_base = Yii::$app->settings->get('main.magazynLineColor');
                $event_line_color = Yii::$app->settings->get('main.magazynColor');
            }
            if ($model->eventModel->color_line)
            {
                 $event_line_color = $model->eventModel->color_line;
            }
            if ($model->eventModel->color)
            {
                $color_line = $model->eventModel->color;
                $color_base = $model->eventModel->color;
            }
            $key = 'events_' . $k;
            /* @var $model Event; */
            $start = $model->getTimeStart();
            $end = $model->getTimeEnd();
            if ($start == null || $end == null) {
                continue;
            }
            /*
            $startDateTime = new DateTime($start);
            $endDateTime = new DateTime($end);

            $date2 = new DateTime($this->year . '-' . $this->month . '-01');
            $date2->modify('+ 45 days');
            $date1 = new DateTime($this->year . '-' . $this->month . '-01');
            $date1->modify('- 6 days');
	        if (!Event::datesAreOverlaping($startDateTime, $endDateTime, $date1, $date2)) {
		        continue;
            }
    */
            $start = strtotime($start);
            $end = strtotime($end);

            $full_time = $end - $start;

            $left = 0;
            $right = 0;
            $packing = '';
            $montage = '';
            $disassembly = '';
            $title= "";
            foreach (\common\models\EventASResult::find()->where(['event_id'=>$model->id])->orderBy(['event_additional_statut_id'=>SORT_ASC])->all() as $statut)
            {
                $title .="<i class='fa ".$statut->eventAdditionalStatutName->icon."'></i> ";
            }
            if ((!Yii::$app->user->can('calendarEventName'))||(Yii::$app->user->can('SiteAdministrator')))
                $title .= $model->name;
            if ((!Yii::$app->user->can('calendarEventID'))||(Yii::$app->user->can('SiteAdministrator')))
                $title .= ' ['.$model->code.']';
            $event_box = '';

            if ($model->manager) {
                if ((!Yii::$app->user->can('calendarEventPM'))||(Yii::$app->user->can('SiteAdministrator')))
                    $title .= ' [' . $model->manager->getInitials() . ']';
            }

            if ($full_time > 0) {
                $a = $start;
                $b = strtotime(date('Y-m-d 00:00:00', $a));

                $x = $end;
                $y = strtotime(date('Y-m-d 00:00:00', $x + 24 * 60 * 60 - 1));

                $time_line = $y - $b;
                $left_time = $a - $b;
                $right_time = $y - $x;

                $weekNumberStart = date('W', $a);
                $weekNumberEnd = date('W', $x);
                if ($weekNumberEnd == $weekNumberStart) {
                    $left = $left_time * 100 / $time_line;
                    $right = $right_time * 100 / $time_line;
                } else {
                    //jeśli w innych tygodniach
                    $dayStart = date('N', $a);
                    $dividerStart = 8 - $dayStart;

                    $dayEnd = date('N', $x);
                    $dividerEnd = $dayEnd;

                    $secondsOfDay = 3600 * 24;

                    $left = $left_time / $secondsOfDay * 100;
                    $left = $left / $dividerStart;

                    $right = ($right_time / $secondsOfDay) * 100;
                    $right = $right / $dividerEnd;


                }

                $secondsOfDay = 3600 * 24;
                $schedulesArr = [];
                foreach ($model->eventSchedules as $schedule)
                {
                    if ($schedule->start_time)
                    {
                            $schedulesize = strtotime($schedule->end_time) - strtotime($schedule->start_time); //ilość godzin
                             $schedulesize = $schedulesize * 100 / $full_time;



                             $left_dist = strtotime($schedule->start_time) - $start;
                             $left_dist = $left_dist * 100 / $full_time;
                             //echo "packing: ".$packingSize." ".$left_dist."<br/>";
                             if ($schedulesize > 0) {
                                if ($schedule->color)
                                {
                                    $color = $schedule->color;
                                }else{
                                    $color = $this->event_colors["event"];
                                }
                                if ($schedule->prefix)
                                {
                                    $prefix = $schedule->prefix;
                                }else{
                                    $prefix = substr($schedule->name, 0,1);
                                }
                             //   if (false){
                                 $schedulesArr[] = Html::tag('div', '&nbsp;'.$prefix, ['style' => [
                                         'background-color' => $color,
                                         'position' => 'absolute',
                                         'width' => $schedulesize . '%',
                                         'left' => $left_dist . '%',
                                         //'border-right' => '1px solid #898989',
                                         'border-left' => '1px solid #898989',
                                         'font-size' => '7px',
                                         'color' => '#867f77',
                                         'height' => '100%',
                                         'top' => '0',
                         'box-sizing' => 'border-box',
                                     ],
                                'data' => [
                                    'start' => $schedule->start_time,
                                    'end' => $schedule->end_time,
                                ]]) . ' ';
                         }
                    }
                }
                /*
                if (!empty($model->packing_start)) {
                
                     $packingSize = strtotime($model->packing_end) - strtotime($model->packing_start); //ilość godzin
                     $packingSize = $packingSize * 100 / $full_time;



                     $left_dist = strtotime($model->packing_start) - $start;
                     $left_dist = $left_dist * 100 / $full_time;
                     //echo "packing: ".$packingSize." ".$left_dist."<br/>";
                     if ($packingSize > 0) {
                     //   if (false){
                         $packing = Html::tag('div', '&nbsp;P', ['style' => [
                                 'background-color' => $this->event_colors["packing"],
                                 'position' => 'absolute',
                                 'width' => $packingSize . '%',
                                 'left' => $left_dist . '%',
                                 //'border-right' => '1px solid #898989',
                                 'border-left' => '1px solid #898989',
                                 'font-size' => '7px',
                                 'color' => '#867f77',
                                 'height' => '100%',
                                 'top' => '0',
                 'box-sizing' => 'border-box',
                             ],
                        'data' => [
                            'start' => $model->packing_start,
                            'end' => $model->packing_end,
                        ]]) . ' ';
                 }
             }


                if (!empty($model->montage_start)) {

                    $montageSize = strtotime($model->montage_end) - strtotime($model->montage_start); //ilość godzin
                    $mS = ($montageSize * 100) / $full_time;

                    $left_dist = strtotime($model->montage_start) - $start;
                    $left_dist = $left_dist * 100 / $full_time;

                    $montage = Html::tag('div', '&nbsp;M', [
                        'style' => [
                            'background-color' => $this->event_colors["montage"],
                            'position' => 'absolute',
                            'width' => $mS . '%',
                            'left' => $left_dist . '%',
                            // 'border-right' => '1px solid #898989',
                            'border-left' => '1px solid #898989',
                            'font-size' => '7px',
                            'color' => '#867f77',
                            'height' => '100%',
                            'top' => '0',
                            'box-sizing' => 'border-box',
                        ],
                        'data' => [
                            'start' => $model->montage_start,
                            'end' => $model->montage_end,
                        ]
                    ]) . ' ';
                }

                if (!empty($model->readiness_start)) {
                    $event_start = $model->readiness_start;
                } else if (!empty($model->practice_start)) {
                    $event_start = $model->practice_start;
                } else {
                    $event_start = $model->event_start;
                }


                if (!empty($model->event_end)) {
                	

                    $eventSize_in_time = strtotime($model->event_end) - strtotime($event_start); //ilość godzin

                    $a = strtotime($event_start);
                    $x = strtotime($model->event_end);
                    $eWS = date('W', $a);
                    $eWE = date('W', $x);
                    $dS = date('N', $a);

                    $dE = date('N', $x);

                    if ($weekNumberEnd != $weekNumberStart) {
                        $eventSize = $eventSize_in_time * 100 / ((($dE - $dS + 1) * $secondsOfDay - 6 * 3600));
                    } else {
                        $eventSize = ($eventSize_in_time * 100) / $full_time;
                    }


                    $left_dist = strtotime($event_start) - $start;
                    $left_dist = $left_dist * 100 / $full_time;

                    if ($eventSize_in_time > 0) {
                        $event_box = Html::tag('div', '&nbsp;E', [
                                'class' => 'event',
                                'style' => [
                                    'background-color' => $event_line_color,
                                    'position' => 'absolute',
                                    'width' => $eventSize . '%',
                                    'left' => $left_dist . '%',
                                    'z-index' => '10',
                                    // 'border-right' => '1px solid #898989',
                                    'border-left' => '1px solid #898989',
                                    'font-size' => '7px',
                                    'color' => '#867f77',
                                    'height' => '100%',
                                    'top' => '0',
                                    'box-sizing' => 'border-box',
                                ],
                                'data' => [
                                    'start' => $model->event_start,
                                    'end' => $model->event_end,
                                ]
                            ]) . ' ';
                    } 
                } 

                if (!empty($model->disassembly_start)) {

                    $disassemblySize = strtotime($model->disassembly_end) - strtotime($model->disassembly_start); //ilość godzin
                    $disassemblySize = ($disassemblySize * 100) / $full_time;

                    $left_dist = strtotime($model->disassembly_start) - $start;
                    $left_dist = $left_dist * 100 / $full_time;

                    if ($disassemblySize > 0) {
                        $disassembly = Html::tag('div', '&nbsp;D', [
                            'style' => [
                                'background-color' => $this->event_colors["disassembly"],
                                'position' => 'absolute',
                                'width' => $disassemblySize . '%',
                                'left' => $left_dist . '%',
                                // 'border-right' => '1px solid #898989',
                                'border-left' => '1px solid #898989',
                                'font-size' => '7px',
                                'color' => '#867f77',
                                'height' => '100%',
                                'top' => '0',
                                'box-sizing' => 'border-box',
                            ],
                            'data' => [
                                'start' => $model->disassembly_start,
                                'end' => $model->disassembly_end,
                            ]

                        ]);
                    }

                }
                */
            }

            $info = $model->getTooltipContent();
            $title_box = Html::tag('div', $model->getStatusIcon().' '.$title, [
                                'class' => 'title',
                                'style' => [
                                    // 'background' => $backgroud,
                                    'position' => 'relative',
                                    'z-index' => '11',
                                    'color' => '#000',
                                    'font-size' => '10px',
                                    'line-height' => '15px',
                                    'padding' => '6px 3px 0',
                                ]
                            ]) . ' ';

            $colors = ArrayHelper::map($model->departments, 'color', 'color');
            $departaments_boxes = '<div class="departament_box" style="width:7px; float:left; padding:2px 0 0 10px; position:relative; z-index:11;">';

            if (!empty($colors)) {
                foreach ($colors as $color) {

                    $departaments_boxes .= Html::tag('div', '&nbsp;', [
                            'class' => 'departament_circle',
                            'style' => [
                                'background' => $color,
                                'width' => '5px',
                                'height' => '5px',
                                'border-radius' => '50%',
                                'margin-bottom' => '1px',
                            ],
                        ]);

                }

            }

            $departaments_boxes .= '</div>';
            if ((!Yii::$app->user->can("calendarDetails"))&&($model->manager_id!=Yii::$app->user->id))
            {
                $info = "";
                $title_box = Html::tag('div', " ", [
                                'class' => 'title',
                                'style' => [
                                    // 'background' => $backgroud,
                                    'position' => 'relative',
                                    'z-index' => '11',
                                    'color' => '#000',
                                    'font-size' => '10px',
                                    'line-height' => '15px',
                                    'padding' => '6px 3px 0',
                                    'min-height'=>'10px'
                                ]
                            ]) . ' ';
            }
            
            $this->events[$key] = [
                'info'=>$info,
                'title' => $title_box,
                'departaments' => $departaments_boxes,
                'event' => $event_box,
                'start' => date('Y-m-d H:i:s', $start),
                'end' => date('Y-m-d H:i:s', $end),
                'left'=>$left,
                'right'=>$right,
                'line_bg' => $color_line ,
                'base_bg' => $color_base,
                'schedules'=>$schedulesArr,
                'border'=>$model->getStatusBorder()
            ];

	        $this->events[$key]['url'] = Url::to(['event/view', 'id'=>$model->id]);
        }
    }

    protected function _addMeetings()
    {
        $models = $this->_searchModel->getMeetings();
        if ($models === null) return;

        foreach ($models as $k=>$model)
        {
            $key = 'meeting_'.$k;
            /* @var $model Meeting; */
            $start = $model->start_time;
            $end = $model->end_time;
            $meeting_base_bg = Yii::$app->settings->get('main.meetingColor');
            $meeting_line_bg = Yii::$app->settings->get('main.meetingLineColor');
            $textColor = Yii::$app->settings->get('main.meetingTextColor');

            $info = $model->getTooltipContent();

            $title_box = Html::tag('div', $model->name, [
                                'class' => 'title',
                                'style' => [
                                    'position' => 'relative',
                                    'z-index' => '11',
                                ]
                            ]);

            $this->events[$key] = [
                'info'=>$info,
                'title' => $title_box,
                'start' => $start,
                'end' => $end,
                'left'=>$this->_getLeft($start,$end),
                'right'=>$this->_getRight($start,$end),
                'line_bg' => $meeting_line_bg,
                'base_bg' => $meeting_base_bg,
                'packing'=>'',
                'textColor' => $textColor,
            ];

            $this->events[$key]['url'] = Url::to(['meeting/view', 'id'=>$model->id]);
        }
    }

    protected function _addRents()
    {
        $models = $this->_searchModel->getRents();
        if ($models === null) return;

        foreach ($models as $k => $model)
        {
            $key = 'rent_'.$k;
            /* @var $model Rent; */
            $start = $model->start_time;
            $end = $model->end_time;
            $backgroud = Yii::$app->settings->get('main.rentColor');
            $rentLineColor = Yii::$app->settings->get('main.rentLineColor');

            $info = $model->getTooltipContent();
            $manager = "";
            if ($model->manager_id)
                $manager = ' [' . $model->manager->getInitials() . ']';
            $title_box = Html::tag('div', $model->getStatusIcon().' '.$manager.' '.$model->name, [
                                'class' => 'title',
                                'style' => [
                                    'position' => 'relative',
                                    'z-index' => '11',
                                ]
                            ]);
            if ((!Yii::$app->user->can("calendarDetails"))&&($model->manager_id!=Yii::$app->user->id))
            {
                $info = "";
                $title_box = Html::tag('div', " ", [
                                'class' => 'title',
                                'style' => [
                                    // 'background' => $backgroud,
                                    'position' => 'relative',
                                    'z-index' => '11',
                                    'color' => '#000',
                                    'font-size' => '10px',
                                    'line-height' => '15px',
                                    'padding' => '6px 3px 0',
                                    'min-height'=>'5px'
                                ]
                            ]) . ' ';
            }
            $this->events[$key] = [
                'info' => $info,
                'title' => $title_box,
                'start' => $start,
                'end' => $end,
                'left'=>$this->_getLeft($start,$end),
                'right'=>$this->_getRight($start,$end),
                'line_bg' => $rentLineColor,
                'base_bg' => $backgroud,
                'packing'=>'',
                'textColor' => Yii::$app->settings->get('main.rentTextColor'),
                'border'=>$model->getStatusBorder()
            ];

            $this->events[$key]['url'] = Url::to(['rent/view', 'id'=>$model->id]);
        }
    }

    protected function _addVacations()
    {
        $models = $this->_searchModel->getVacations();
        if ($models === null) return;

        foreach ($models as $k => $model)
        {
            $key = 'vacation_'.$k;
            /* @var $model Vacation; */
            $start = $model->start_date;
            $end = $model->end_date;
            $backgroud = Yii::$app->settings->get('main.vacationColor');
            $textColor = Yii::$app->settings->get('main.vacationTextColor');
            switch ($model->status)
            {
                case Vacation::STATUS_ACCEPTED:
                    $backgroud = Yii::$app->settings->get('main.vacationAcceptedColor');
                    $textColor = Yii::$app->settings->get('main.vacationTextAcceptedColor');
                    break;
                case Vacation::STATUS_REJECTED:
                    $backgroud = Yii::$app->settings->get('main.vacationRejectedColor');
                    $textColor = Yii::$app->settings->get('main.vacationTextRejectedColor');
                    break;
                default:
                    break;
            }

            $title_box = Html::tag('div', Yii::t('app', 'Urlop').': '.$model->user->getDisplayLabel(), [
                            'class' => 'title',
                            'style' => [
                                'font-size' => '9px',
                                'line-height' => '10px',
                                'position' => 'relative',
                                'z-index' => '11',
                            ],
                        ]);

            $this->events[$key] = [
                'title' => $title_box,
                'start' => $start." 00:00:00",
                'end' => $end." 23:59:59",
                'left'=>0,
                'right'=>0,
                'line_bg' => $backgroud,
                'packing'=>'',
                'textColor' => $textColor,
                'type' => 'vacation',
            ];

            $user = Yii::$app->user;
            if ($user->can('eventVacationsEdit') || $model->user_id == $user->id)
            {
                $this->events[$key]['url'] = Url::to(['vacation/view', 'id'=>$model->id]);
            }
        }
    }

    protected function _addPersonal()
    {
        $models = $this->_searchModel->getPersonals();
        if ($models === null) return;

        foreach ($models as $k => $model)
        {
            $key = 'personal_'.$k;
            /* @var $model Personal; */
            $start = $model->start_time;
            $end = $model->end_time;
            $backgroud = Yii::$app->settings->get('main.personalColor');
            $textColor = Yii::$app->settings->get('main.personalTextColor');

            $title = $model->name;
            if ($model->parent_id)
            {
                $title .= ' ['.Yii::t('app', 'kopia').']';
            }
            $title_box = Html::tag('div', $title, [
                                'class' => 'title',
                                'style' => [
                                    'position' => 'relative',
                                    'z-index' => '11',
                                ]
                            ]);
            $this->events[$key] = [
                'title' => $title_box,
                'start' => $start,
                'end' => $end,
                'left'=>$this->_getLeft($start,$end),
                'right'=>$this->_getRight($start,$end),
                'line_bg' => $backgroud,
                'packing'=>'',
                'textColor'=>$textColor,
            ];


            $this->events[$key]['url'] = Url::to(['personal/view', 'id'=>$model->id]);
        }
    }

    protected function _getRight($start,$end)
    {
        $a = strtotime($start);
        $b = strtotime(date('Y-m-d 00:00:00', $a));

        $x = strtotime($end);
        $y = strtotime(date('Y-m-d 00:00:00', $x+24*60*60-1));

        $time_line = $y-$b;
        $right_time = $y-$x;

        if ($time_line>0)
        {
            $right = $right_time*100/$time_line;
        }
        else
        {
            $right = 0;
        }

        return $right;

    }

    protected function _getLeft($start,$end)
    {
        $a = strtotime($start);
        $b = strtotime(date('Y-m-d 00:00:00', $a));

        $x = strtotime($end);
        $y = strtotime(date('Y-m-d 00:00:00', $x+24*60*60-1));

        $time_line = $y-$b;
        $left_time = $a-$b;

        if ($time_line>0)
        {
            $left = $left_time*100/$time_line;
        }
        else
        {
            $left = 0;
        }

        return $left;
    }

}

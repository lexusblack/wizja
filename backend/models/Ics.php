<?php
namespace backend\models;

use yii\base\Model;
use common\helpers\ArrayHelper;
use Yii;

class Ics extends Model
{
    var $data;
    var $name;
    function ICS($start,$end,$name,$description,$location) {
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $start = strtotime($start);
        $end = strtotime($end);
        date_default_timezone_set("UTC");

        $this->name = $name;
        $this->data = "BEGIN:VCALENDAR\nVERSION:2.0\nMETHOD:PUBLISH\nBEGIN:VEVENT\nDTSTART:".date("Ymd\THis\Z",$start)."\nDTEND:".date("Ymd\THis\Z",$end)."\nLOCATION:".$location."\nTRANSP: OPAQUE\nSEQUENCE:0\nUID:\nDTSTAMP:".date("Ymd\THis\Z")."\nSUMMARY:".$name."\nDESCRIPTION:".$description."\nPRIORITY:1\nCLASS:PUBLIC\nBEGIN:VALARM\nTRIGGER:-PT10080M\nACTION:DISPLAY\nDESCRIPTION:Reminder\nEND:VALARM\nEND:VEVENT\nEND:VCALENDAR\n";
    }
    function save($path) {
        file_put_contents($path,iconv("UTF-8", "CP1250//IGNORE", $this->data));
    }
    function show() {
        header("Content-type:text/calendar");
        header('Content-Disposition: attachment; filename="'.$this->name.'.ics"');
        Header('Content-Length: '.strlen($this->data));
        Header('Connection: close');
        echo $this->data;
    }

}
<?php

namespace common\models;

use Yii;
use \common\models\base\EventBreaks as BaseEventBreaks;

/**
 * This is the model class for table "event_breaks".
 */
class EventBreaks extends BaseEventBreaks {


    const ICONS = ['home', 'glass', 'cutlery', 'road', 'tint', 'plane', 'shopping-cart', 'education', 'asterisk','plus','minus','euro','cloud','envelope','pencil','music','search','heart','star','star-empty','user','film','th-large','th','th-list','ok','remove','zoom-in','zoom-out','off','signal','cog','trash','file','time','download-alt','download','upload','inbox','play-circle','repeat','refresh','list-alt','lock','flag','headphones','volume-off','volume-down','volume-up','qrcode','barcode','tag','tags','book','bookmark','print','camera','font','bold','italic','text-height','text-width','align-left','align-center','align-right','align-justify','list','indent-left','indent-right','facetime-video','picture','map-marker','adjust','edit','share','check','move','step-backward','fast-backward','backward','play','pause','stop','forward','fast-forward','step-forward','eject','chevron-left','chevron-right','plus-sign','minus-sign','remove-sign','ok-sign','question-sign','info-sign','screenshot','remove-circle','ok-circle','ban-circle','arrow-left','arrow-right','arrow-up','arrow-down','share-alt','resize-full','resize-small','exclamation-sign','gift','leaf','fire','eye-open','eye-close','warning-sign','calendar','random','comment','magnet','chevron-up','chevron-down','retweet','folder-close','folder-open','resize-vertical','resize-horizontal','hdd','bullhorn','bell','certificate','thumbs-up','thumbs-down','hand-right','hand-left','hand-up','hand-down','circle-arrow-right','circle-arrow-left','circle-arrow-up','circle-arrow-down','globe','wrench','tasks','filter','briefcase','fullscreen','dashboard','paperclip','heart-empty','link','phone','pushpin','usd','gbp','sort','sort-by-alphabet','sort-by-alphabet-alt','sort-by-order','sort-by-order-alt','sort-by-attributes','sort-by-attributes-alt','unchecked','expand','collapse-down','collapse-up','log-in','flash','log-out','new-window','record','save','open','saved','import','export','send','floppy-disk','floppy-saved','floppy-remove','floppy-save','floppy-open','credit-card','transfer','header','compressed','earphone','phone-alt','tower','stats','sd-video','hd-video','subtitles','sound-stereo','sound-dolby','sound-5-1','sound-6-1','sound-7-1','copyright-mark','registration-mark','cloud-download','cloud-upload','tree-conifer','tree-deciduous','cd','save-file','open-file','level-up','copy','paste','alert','equalizer','king','queen','pawn','bishop','knight','baby-formula','tent','blackboard','bed','apple','erase','hourglass','lamp','duplicate','piggy-bank','scissors','bitcoin','yen','ruble','scale','ice-lolly','ice-lolly-tasted','option-horizontal','option-vertical','menu-hamburger','modal-window','oil','grain','sunglasses','text-size','text-color','text-background','object-align-top','object-align-bottom','object-align-horizontal','object-align-left','object-align-vertical','object-align-right','triangle-right','triangle-left','triangle-bottom','triangle-top','superscript','subscript','menu-left','menu-right','menu-down','menu-up'];
	public $break_date_range;

	public function rules()
  {
      $rules = [
          [['break_date_range'], 'string'],
          [['break_date_range'], 'required'],
      ];
      return array_merge(parent::rules(), $rules);
  }

  public function attributeLabels()
    {
        $labels = [
            'break_date_range' => Yii::t('app', 'Czas przerwy'),
            'icon' => Yii::t('app', 'Ikona'),
            'name' => Yii::t('app', 'Nazwa')
        ];

        return array_merge(parent::attributeLabels(), $labels);
    }

  public function beforeValidate()
  {
      $this->setDatedFromRange();
      return parent::beforeValidate();
  }

	public function getIconsArray()
  {
    return self::ICONS;
  }

  public function getIconList($event_id)
  {
    $list = EventBreaks::find()->where(['event_id' => $event_id])->all();
    $icons_arr = self::getIconsArray();

    $icon_list = [];
    foreach ($list as $key => $value) {
      $icon_list[$value->id] = $icons_arr[$value->icon];
    }
    return $icon_list;
  }

  public function setDatedFromRange()
  {
  	$dates_arr = explode(" - ", $this->break_date_range);
  	$this->start_time = isset($dates_arr[0]) ? $dates_arr[0] : null;
  	$this->end_time = isset($dates_arr[1]) ? $dates_arr[1] : null;
  }
}

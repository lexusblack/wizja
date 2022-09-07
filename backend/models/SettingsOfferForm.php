<?php
namespace backend\models;

use common\components\SettingsTrait;
use yii\base\Model;
use Yii;

class SettingsOfferForm extends Model
{
    use SettingsTrait;

    public $firstDayPercent;
    public $transportColor;
    public $crewColor;
    public $otherColor;
    public $transportFontColor;
    public $crewFontColor;
    public $otherFontColor;
    public $orderRules;
    public $offerPayingTerm;



    public function rules()
    {
        return [
            [[
                'firstDayPercent', 'offerPayingTerm'
            ], 'integer', 'max'=>100, 'min'=>0],
            [['transportColor', 'crewColor', 'otherColor', 'transportFontColor', 'crewFontColor', 'otherFontColor', 'orderRules'], 'string']

        ];
    }


    public function attributeLabels()
    {
        $labels = [
            'firstDayPercent' => Yii::t('app', 'Procent dnia pierwszego'),
            'offerPayingTerm' => Yii::t('app', 'Domyślny termin płatności'),
            'transportColor' =>Yii::t('app', 'Kolor segmentu transport'),
            'crewColor' =>Yii::t('app', 'Kolor segmentu obsługa'),
            'otherColor' =>Yii::t('app', 'Kolor segmentu inne'),
            'transportFontColor' =>Yii::t('app', 'Kolor czcionki segmentu transport'),
            'crewFontColor' =>Yii::t('app', 'Kolor czcionki segmentu obsługa'),
            'otherFontColor' =>Yii::t('app', 'Kolor czcionki segmentu inne'),
            'orderRules' =>Yii::t('app', 'Warunki zamówienia'),
        ];

        return array_merge(parent::attributeLabels(), $labels);
    }

    public function attributeHints()
    {
        $hints = [

        ];
        return array_merge(parent::attributeHints(), $hints);
    }

    public function formName()
    {
        //settings section
        return 'offer';
    }



}
<?php
namespace backend\modules\finances\models;
use Yii;
use yii\base\Model;

class FinanceSearch extends Model
{
    public $q;
    public $qOptions = [];
    public $model;

    public function init()
    {
        $this->qOptions = array_keys(static::getOptionList());
        parent::init();
    }

    public function rules()
    {
        $rules = [
            [['q', 'qOptions'], 'required'],

        ];
        return array_merge(parent::rules(), $rules);
    }

    public static function getOptionList()
    {
        $list = [

//            NIP, Kontrahent, Wydarzenie, numer faktury, data faktury, nazwa towaru
            'nip'=>Yii::t('app', 'NIP'),
            'customer'=>Yii::t('app', 'Kontrahent'),
            'event'=>Yii::t('app', 'Wydarzenie'),
            'number'=>Yii::t('app', 'Numer faktury'),
            'date'=>Yii::t('app', 'Data faktury'),
            'name'=>Yii::t('app', 'Nazwa towaru'),
        ];
        return $list;
    }

    public function attributeLabels()
    {
        $labels = [
            'q'=>Yii::t('app', 'Szukana fraza'),
        ];
        return array_merge(parent::attributeLabels(), $labels);
    }
}
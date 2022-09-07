<?php

namespace common\models;

use Yii;
use \common\models\base\OfferLog as BaseOfferLog;

/**
 * This is the model class for table "offer_log".
 */
class OfferLog extends BaseOfferLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['offer_id', 'user_id'], 'integer'],
            [['create_time'], 'safe'],
            [['content'], 'string', 'max' => 255]
        ]);
    }

    public static function addLog($type, $model, $offer_id, $field=null, $val = null)
    {
        $log = new OfferLog();
        $log->offer_id = $offer_id;
        $log->user_id = Yii::$app->user->identity->id;
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $log->create_time = date('Y-m-d H:i:s');
        if ($type=='offer_created')
        {
            $log->content = Yii::t('app', 'Stworzono ofertę ').$model->name;
        }
        if ($type=='gear_add')
        {
            $log->content = Yii::t('app', 'Dodano sprzęt ').$model->gear->name." ".$model->quantity." ".Yii::t('app', 'szt.');
        }
        if ($type=='outer_add')
        {
            $log->content = Yii::t('app', 'Dodano sprzęt zewnętrzny ').$model->outerGearModel->name." ".$model->quantity." ".Yii::t('app', 'szt.');
        }
        if ($type=='extra_add')
        {
            $log->content = Yii::t('app', 'Dodano sprzęt/grupę sprzętu ').$model->name." ".$model->quantity." ".Yii::t('app', 'szt.');
        }
        if ($type=='role_add')
        {
            $log->content = Yii::t('app', 'Dodano obsługę ').$model->role->name." ".$model->quantity." ".Yii::t('app', 'os.');
        }
        if ($type=='vehicle_add')
        {
            $log->content = Yii::t('app', 'Dodano pojazd ').$model->vehicle->name;
        }
        if ($type=='custom_add')
        {
            $log->content = Yii::t('app', 'Dodano "inne" ').$model->name;
        }
        if ($type=='gear_edit')
        {
            if ($field=="quantity")
                $log->content = Yii::t('app', 'Edytowano sprzęt ').$model->gear->name.Yii::t('app', ' zmieniono liczbę sztuk z ').$val.Yii::t('app', ' na ').$model->quantity." ".Yii::t('app', 'szt.');
            if ($field=="price")
                $log->content = Yii::t('app', 'Edytowano sprzęt ').$model->gear->name.Yii::t('app', ' zmieniono cenę z ').$val.Yii::t('app', ' na ').$model->price;
            if ($field=="duration")
                $log->content = Yii::t('app', 'Edytowano sprzęt ').$model->gear->name.Yii::t('app', ' zmieniono liczbę dni z ').$val.Yii::t('app', ' na ').$model->duration;
            if ($field=="first_dy_percent")
                $log->content = Yii::t('app', 'Edytowano sprzęt ').$model->gear->name.Yii::t('app', ' zmieniono % dnia pierwszego z ').$val.Yii::t('app', ' na ').$model->first_dy_percent;
        }
        if ($type=='outer_edit')
        {
            if ($field=="quantity")
                $log->content = Yii::t('app', 'Edytowano sprzęt ').$model->outerGearModel->name.Yii::t('app', ' zmieniono liczbę sztuk z ').$val.Yii::t('app', ' na ').$model->quantity." ".Yii::t('app', 'szt.');
            if ($field=="price")
                $log->content = Yii::t('app', 'Edytowano sprzęt ').$model->outerGearModel->name.Yii::t('app', ' zmieniono cenę z ').$val.Yii::t('app', ' na ').$model->price;
            if ($field=="duration")
                $log->content = Yii::t('app', 'Edytowano sprzęt ').$model->outerGearModel->name.Yii::t('app', ' zmieniono liczbę dni z ').$val.Yii::t('app', ' na ').$model->duration;
            if ($field=="first_dy_percent")
                $log->content = Yii::t('app', 'Edytowano sprzęt ').$model->outerGearModel->name.Yii::t('app', ' zmieniono % dnia pierwszego z ').$val.Yii::t('app', ' na ').$model->first_dy_percent;
        }
        if ($type=='extra_edit')
        {
            if ($field=="quantity")
                $log->content = Yii::t('app', 'Edytowano sprzęt ').$model->name.Yii::t('app', ' zmieniono liczbę sztuk z ').$val.Yii::t('app', ' na ').$model->quantity." ".Yii::t('app', 'szt.');
            if ($field=="price")
                $log->content = Yii::t('app', 'Edytowano sprzęt ').$model->name.Yii::t('app', ' zmieniono cenę z ').$val.Yii::t('app', ' na ').$model->price;
            if ($field=="duration")
                $log->content = Yii::t('app', 'Edytowano sprzęt ').$model->name.Yii::t('app', ' zmieniono liczbę dni z ').$val.Yii::t('app', ' na ').$model->duration;
            if ($field=="first_dy_percent")
                $log->content = Yii::t('app', 'Edytowano sprzęt ').$model->name.Yii::t('app', ' zmieniono % dnia pierwszego z ').$val.Yii::t('app', ' na ').$model->first_dy_percent;
        }
        if ($type=='role_edit')
        {
            if ($field=="quantity")
                $log->content = Yii::t('app', 'Edytowano obsługę ').$model->role->name.Yii::t('app', ' zmieniono liczbę ').$val.Yii::t('app', ' na ').$model->quantity." ".Yii::t('app', 'szt.');
            if ($field=="price")
                $log->content = Yii::t('app', 'Edytowano obsługę ').$model->role->name.Yii::t('app', ' zmieniono cenę z ').$val.Yii::t('app', ' na ').$model->price;
            if ($field=="duration")
                $log->content = Yii::t('app', 'Edytowano obsługę ').$model->role->name.Yii::t('app', ' zmieniono przelicznik z ').$val.Yii::t('app', ' na ').$model->duration;
        }
        if ($type=='vehicle_edit')
        {
            if ($field=="quantity")
                $log->content = Yii::t('app', 'Edytowano pojazd ').$model->vehicle->name.Yii::t('app', ' zmieniono liczbę ').$val.Yii::t('app', ' na ').$model->quantity." ".Yii::t('app', 'szt.');
            if ($field=="price")
                $log->content = Yii::t('app', 'Edytowano pojazd ').$model->vehicle->name.Yii::t('app', ' zmieniono cenę z ').$val.Yii::t('app', ' na ').$model->price;
            if ($field=="distance")
                $log->content = Yii::t('app', 'Edytowano pojazd ').$model->vehicle->name.Yii::t('app', ' zmieniono przelicznik ').$val.Yii::t('app', ' na ').$model->distance;
        }
        if ($type=='custom_edit')
        {
            $log->content = Yii::t('app', 'Edytowano "inne" ').$model->name;
        }
        if ($type=='gear_delete')
        {
            $log->content = Yii::t('app', 'Usunięto sprzęt ').$model->gear->name." ".$model->quantity." ".Yii::t('app', 'szt.');
        }
        if ($type=='outer_delete')
        {
            $log->content = Yii::t('app', 'Usunięto sprzęt zewnętrzny ').$model->outerGearModel->name." ".$model->quantity." ".Yii::t('app', 'szt.');
        }
        if ($type=='extra_delete')
        {
            $log->content = Yii::t('app', 'Usunięto sprzęt/grupę sprzętu ').$model->name." ".$model->quantity." ".Yii::t('app', 'szt.');
        }
        if ($type=='role_delete')
        {
            $log->content = Yii::t('app', 'Usunięto obsługę ').$model->role->name." ".$model->quantity." ".Yii::t('app', 'os.');
        }
        if ($type=='vehicle_delete')
        {
            $log->content = Yii::t('app', 'Usunięto pojazd ').$model->vehicle->name;
        }
        if ($type=='custom_delete')
        {
            $log->content = Yii::t('app', 'Usunięto "inne" ').$model->name;
        }
        if ($type=='offer_status')
        {
            $log->content = Yii::t('app', 'Zmieniono status oferty na ').$model->getStatusLabel();
        }
        if ($type=='offer_time')
        {
            $log->content = Yii::t('app', 'Zmieniono harmonogram oferty').".";
        }
        if ($type=='offer_event')
        {
            $log->content = Yii::t('app', 'Przypisano ofertę do wydarzenia ').$model->event->name;
        }
        if ($type=='offer_rent')
        {
            $log->content = Yii::t('app', 'Przypisano ofertę do wypożyczenia ').$model->rent->name;
        }
        $log->save();
        return true;
    }
	
}

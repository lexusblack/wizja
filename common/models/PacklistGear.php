<?php

namespace common\models;

use Yii;
use \common\models\base\PacklistGear as BasePacklistGear;

/**
 * This is the model class for table "packlist_gear".
 */
class PacklistGear extends BasePacklistGear
{
    /**
     * @inheritdoc
     */

    public $dateRange;
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_gear_id', 'quantity', 'packlist_id', 'gear_id'], 'integer'],
            [['info', 'start_time', 'end_time'], 'string', 'max' => 255]
        ]);
    }
	
	    public function canBeDeleted()
    {
        $eo = EventGearOutcomed::find()->where(['gear_id'=>$this->gear_id, 'packlist_id'=>$this->packlist_id])->one();
        if (!$eo)
        {
            return true;
        }else{
            if ($eo->quantity ==0)
            {
                return true;
            }else{
                return false;
            }
		}
    }

    public function recalculateQuantity()
    {
        //wydajemy sprzęty, po wydaniu sprawdzamy czy nie wydano więccej sztuk danego sprzętu
        $count = EventGearItem::find()->where(['packlist_id'=>$this->packlist_id])->andWhere(['gear_id'=>$this->gear_id])->count();
        if ($count>$this->quantity)
        {
            $this->quantity = $count;
            $this->save();
        }
    }
    
    public function beforeSave($insert)
    {
        if ($insert)
        {
            $eg = EventGear::find()->where(['event_id'=>$this->packlist->event_id, 'gear_id'=>$this->gear_id])->one();
            if (!$eg)
            {
                $eg = new EventGear();
                $eg->event_id = $this->packlist->event_id;
                $eg->gear_id = $this->gear_id;
                $eg->quantity = $this->quantity;
                $eg->save();
            }
            $this->event_gear_id = $eg->id;
        }
        return true;
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert){
            $this->eventGear->updateCount();
            $log = new EventLog();
                $log->event_id = $this->packlist->event_id;
                $log->user_id = Yii::$app->user->id;
                $log->content = Yii::t('app', 'Zmieniono rezerwację sprzętu ').$this->gear->name.Yii::t('app', ' w pakliście ').$this->packlist->name.Yii::t('app', ' z ').$changedAttributes['quantity'].Yii::t('app', ' na ').$this->quantity.Yii::t('app', ' szt.');
                $log->save();
        }else{
                 $log = new EventLog();
                $log->event_id = $this->packlist->event_id;
                $log->user_id = Yii::$app->user->id;
                $log->content = Yii::t('app', 'Dodano rezerwację sprzętu ').$this->gear->name.Yii::t('app', ' w pakliście ').$this->packlist->name.Yii::t('app', ' w ilości ').$this->quantity.Yii::t('app', ' szt.');
                $log->save();
        }
    }
    
    public function afterDelete()
    {
            parent::afterDelete();
            EventConflict::deleteAll(['packlist_gear_id'=>$this->id]);
            $log = new EventLog();
                $log->event_id = $this->packlist->event_id;
                $log->user_id = Yii::$app->user->id;
                $log->content = Yii::t('app', 'Usunięto rezerwację sprzętu ').$this->gear->name.Yii::t('app', ' w pakliście ').$this->packlist->name.Yii::t('app', ' w ilości ').$this->quantity.Yii::t('app', ' szt.');
                $log->save();
            $gear = EventGear::findOne($this->event_gear_id);
            if ($gear)
                $gear->updateCount();
            
    }
	
}

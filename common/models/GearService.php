<?php

namespace common\models;

use Yii;
use \common\models\base\GearService as BaseGearService;
use yii\db\Expression;
use common\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\MethodNotAllowedHttpException;
/**
 * This is the model class for table "gear_service".
 */
class GearService extends BaseGearService
{

    const STATUS_NEW = 0;
    const STATUS_IN_REPAIR = 3;
    const STATUS_WAITING_FOR_PARTS = 5;
    const STATUS_REPAIRED = 50;
    const STATUS_REPAIR_IMPOSIBLE = 60;
    const STATUS_TO_CHECK = 70;
    const STATUS_NEED_SERVICE = 80;

    const TYPE_RETURNED = 10;
    CONST TYPE_SERVICE = 1;
    CONST TYPE_NEED_SERVICE = 2;

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
                  # custom validation rules
             ]
        );
    }

    public static function add($gearItem)
    {
        
        $model = static::getCurrentModel($gearItem->id);
        if ($model === null)
        {
            $model = static::loadByParams([
                'gear_item_id'=>$gearItem->id,
                'status'=>self::STATUS_NEW,
            ], true);
        }


        return $model;
    }

    public static function addNoItem($gearItem)
    {
        $model = static::loadByParams([
                'gear_item_id'=>$gearItem->id,
                'status'=>self::STATUS_NEW,
                'quantity' =>1
            ], true);


        return $model;
    }

    public function sendBack()
    {
        $this->status = self::STATUS_REPAIRED;
        if ($this->status!=self::STATUS_REPAIRED)
        {
            $message = Yii::t('app', 'Status').': "'.$this->getStatusLabel().'" '.Yii::t('app', 'nie pozwala na zwrócenie sprzętu');
//            throw new HttpException(400, $message);
            Yii::$app->session->setFlash('error', $message);
            return false;
        }
        $transaction = $this->getDb()->beginTransaction();

        try
        {
            $this->type = self::TYPE_RETURNED;
            $this->save(false);
            $this->gearItem->status = GearItem::STATUS_ACTIVE;
            $this->gearItem->save(false);

            $transaction->commit();
        }
        catch (\Exception $e)
        {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        return true;
    }


    public static function getStatusList()
    {
        $list = [
            self::STATUS_NEW => Yii::t('app', 'Przyjęty na serwis'),
            self::STATUS_IN_REPAIR => Yii::t('app', 'W naprawie'),
            self::STATUS_WAITING_FOR_PARTS => Yii::t('app', 'Oczekiwanie na części'),
            self::STATUS_REPAIRED => Yii::t('app', 'Naprawiony'),
            self::STATUS_REPAIR_IMPOSIBLE => Yii::t('app', 'Niemożliwy do naprawy'),
            self::STATUS_TO_CHECK  => Yii::t('app', 'Do sprawdzenia'),
            self::STATUS_NEED_SERVICE  => Yii::t('app', 'Wymaga serwisu'),
        ];
        $list = ArrayHelper::map(GearServiceStatut::find()->where(['active'=>1])->asArray()->all(), 'id', 'name');
        return $list;
    }

    public static function getPriorityList()
    {
        $list = [
            1 => Yii::t('app', 'Niski'),
            2 => Yii::t('app', 'Normalny'),
            3 => Yii::t('app', 'Wysoki')
        ];
        return $list;
    }

    public function getStatusLabel()
    {
        $list = static::getStatusList();
        $index = $this->status;
        return ArrayHelper::getValue($list, $index, UNDEFINDED_STRING);
    }

    public static function getTypeList()
    {
        $list = [
            self::TYPE_SERVICE => Yii::t('app', 'W serwisie'),
            self::TYPE_NEED_SERVICE => Yii::t('app', 'Wymaga serwisu'),
            self::TYPE_RETURNED => Yii::t('app', 'Zwrócony'),
        ];
        return $list;
    }
    public function getTypeLabel()
    {
        $list = static::getTypeList();
        $index = $this->type;
        return ArrayHelper::getValue($list, $index, UNDEFINDED_STRING);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            if (($this->gearItem->status == GearItem::STATUS_SERVICE)||($this->gearItem->status == GearItem::STATUS_NEED_SERVICE))
            {
                if (!$this->gearItem->gear->no_items)
                    throw new MethodNotAllowedHttpException(Yii::t('app', 'Urządzenie jest już w serwisie lub ma zgłoszone zapotrzebowanie.'));
            }
        	$now = new \DateTime();
        	$eventGearItem = EventGearItem::find()->where(['gear_item_id' => $this->gear_item_id])->andWhere(['>', 'start_time', $now->format('Y-m-s H:i:s')])->all();
        	/** @var \common\models\EventGearItem $eventGear */
	        foreach ($eventGearItem as $eventGear) {
	        	/** @var \common\models\User $manager */
        		if ($manager = $eventGear->event->manager) {
					if ($manager->phone) {
						Notification::sendUserSmsNotification($manager, Yii::t('app', 'Sprzęt: ' . $eventGear->gearItem->gear->name . ' z eventu: '.$eventGear->event->name.'  został wysłany do serwisu'), true);
					}
					if ($manager->email) {
						Notification::sendUserMailNotification($manager, Yii::t('app',  'Sprzęt z eventu odesłany do serwisu'), Yii::t('app', 'Sprzęt: ' . $eventGear->gearItem->gear->name . ' z eventu: '.$eventGear->event->name.'  został wysłany do serwisu'));
					}
		        }
	        }

        }
        else {
            $oldModel = static::findOne($this->id);
            if ($oldModel->status != $this->status)
            {
                $this->_setStatusTime();
            }
        }
        $statut = GearServiceStatut::findOne($this->status);
        if ($statut->type==1)
        {
            $this->type = 1;
        }
        if ($statut->type==2)
        {
            $this->type = 10;
        }
        if ($statut->type==3)
        {
            $this->type = 2;
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert){
            Note::createNote(4, 'gearService', $this, $this->id);
            $history = new GearServiceHistory();
            $history->datetime = date('Y-m-d H:i:s');
            $history->user_id = Yii::$app->user->id;
            $history->gear_service_id = $this->id;
            $history->statut_to = $this->status;
            $history->save();
            $this->gearItem->changeServiceStatut(null, $this->status);
        }else{
            if ((isset($changedAttributes['status']))&&($this->status!=$changedAttributes['status'])){
            $history = new GearServiceHistory();
            $history->datetime = date('Y-m-d H:i:s');
            $history->user_id = Yii::$app->user->id;
            $history->gear_service_id = $this->id;
            $history->statut_to = $this->status;
            $history->statut_from = $changedAttributes['status'];
            $history->save();
            $this->gearItem->changeServiceStatut($changedAttributes['status'], $this->status);
            }
        }
                


    }

    public function getHistory()
    {
        return GearServiceHistory::find()->where(['gear_service_id'=>$this->id])->all();
    }

    protected function _setStatusTime()
    {
        $this->status_time = new Expression('NOW()');
    }

    public function attributeLabels()
    {
        $labels = [
            'info' => Yii::t('app', 'Opis naprawy/Przyczyny uniemożliwiające naprawę')
        ];
        return array_merge(parent::attributeLabels(), $labels);
    }

    public static function getGearItemsList()
    {
        $models = static::find()
            ->innerJoinWith(['gearItem' => function($q)
            {
               return $q->innerJoinWith('gear');
            }])
            ->groupBy('gear_item_id')
            ->orderBy(['gear.name'=>SORT_ASC, 'gear_item.name'=>SORT_ASC])
            ->all();

        $list = ArrayHelper::map($models, 'gear_item_id', 'gearItem.name', 'gearItem.gear.name');
        return $list;
    }

    public static function getCurrentModel($gearItemId)
    {
        $model = static::find()->where([
            'gear_item_id'=>$gearItemId,
            'type'=>[1,2],
        ])
        ->orderBy(['id'=>SORT_DESC])
        ->one();

        return $model;
    }

    public function beforeDelete()
    {
        $this->gearItem->status=1;
        $this->gearItem->save();
        return true;
    }

}

<?php

namespace common\models;

use barcode\barcode\BarcodeGenerator;
use common\behaviors\WorkingTimeBehavior;
use Symfony\CS\Fixer\PSR2\ElseifFixer;
use Yii;
use \common\models\base\GearItem as BaseGearItem;
use common\helpers\ArrayHelper;
use yii\caching\ChainedDependency;
use yii\caching\DbDependency;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\helpers\Html;
use dosamigos\qrcode\lib\Enum;
use dosamigos\qrcode\QrCode;
/**
 * This is the model class for table "gear_item".
 */
class GearItem extends BaseGearItem
{
    protected $_availability = null;

    const TYPE_NORMAL = 1;
    const TYPE_NO_ITEM = 0;

    const NO_ITEM_NAME = '_ILOSC_SZTUK_';

    const STATUS_ACTIVE = 1;
    const STATUS_NEED_SERVICE = 2;
    const STATUS_SERVICE = 10;

    public function behaviors()
    {
        $behaviors = [
            'workingTime'=> [
                'class'=>WorkingTimeBehavior::className(),
                'connectionClassName'=>EventGearItem::className(),
                'itemIdAttribute'=>'gear_item_id',

            ],
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }

    public static function getStatusList()
    {
        $list = [
            self::STATUS_ACTIVE => Yii::t('app', 'Aktywny'),
            self::STATUS_NEED_SERVICE => Yii::t('app', 'Wymaga serwisu, ale działa'),
            self::STATUS_SERVICE => Yii::t('app', 'W serwisie'),
        ];
        return $list;
    }

    public function getStatusLabel()
    {
        $list = static::getStatusList();
        $index = $this->status;

        return ArrayHelper::getValue($list, $index, UNDEFINDED_STRING);
    }

    public function getPhotoUrl()
    {
        return Yii::getAlias('@uploads/gear-item/'.$this->photo);
    }

    public function getFileThumbUrl($options = [])
    {
        $defaultOptions = [
            'thumbnail' => [
                'width' => 200,
                'height' => 200,
                'mode'=>Thumbnail::THUMBNAIL_INSET,
            ],
            'placeholder' => [
                'width' => 200,
                'height' => 200,
                'text'=>'-',
            ]
        ];
        $options = ArrayHelper::merge($defaultOptions, $options);
        try
        {
            $thumb = @Yii::$app->thumbnail->url($this->getFilePath(), $options);
        }
        catch (\Exception $e)
        {
            return null;
        }
        return $thumb;
    }

    public static function getGroupedList()
    {
        $models = static::find()
            ->all();

        $list = ArrayHelper::map($models, 'id', 'name', 'gear.name');
        return $list;
    }

    public static function getAvaibleGroupedList($eventId)
    {
        $assigned = EventGearItem::find()
            ->select(['gear_item_id'])
            ->where(['event_id'=>$eventId])
            ->asArray()
            ->column();

        $models = static::find()
            ->where(['not', ['id'=>$assigned]])
            ->all();

        $list = ArrayHelper::map($models, 'id', 'name', 'gear.name');
        return $list;
    }


    /**
     * @param string $start Datetime.
     * @param string $end Datetime.
     * @return bool If item is available in range of dates.
     */
    public function isAvailableInRange($start, $end, $owner=null)
    {
        $count = 0;
        $queryEvent = $this->_getAvailabilityQuery(EventGearItem::className(), $start, $end);
        $queryRent = $this->_getAvailabilityQuery(RentGearItem::className(), $start, $end);
        $queryOffer = $this->_getAvailabilityQuery(OfferGearItem::className(), $start, $end);


        if ($owner !== null)
        {
            if ($owner instanceof Event)
            {
                $queryEvent->andWhere(['<>', 'event_id', $owner->id]);
            }
            elseif ($owner instanceof Rent)
            {
                $queryRent->andWhere(['<>', 'rent_id', $owner->id]);
            }
            elseif ($owner instanceof Offer)
            {
                $queryRent->andWhere(['<>', 'offer_id', $owner->id]);
            }
        }


        $count += $queryEvent->count();
        $count += $queryRent->count();
        $count += $queryOffer->count();

        return $count == 0 ? true : false;

    }

    protected function _getAvailabilityQuery($className, $start, $end)
    {
        $query = $className::find()
            ->where([
                'and',
                ['<=', 'start_time', $start],
                ['>=', 'end_time', $start],
            ])
            ->orWhere([
                'and',
                ['<=', 'start_time', $end],
                ['>=', 'end_time', $end],
            ])
            ->orWhere([
                'and',
                ['>=', 'start_time', $start],
                ['<=', 'end_time', $end],
            ])
            ->andWhere([
                'gear_item_id'=>$this->id,
            ]);

        return $query;
    }

    public function isAvailable($owner)
    {
        $db = Yii::$app->getDb();
        $available = false;
        $itemsUsed = 0;

        $start = $owner->getTimeStart();
        $end = $owner->getTimeEnd();

        $query = $this->_getAvailabilityQuery(EventGearItem::className(), $start, $end);

        if ($owner instanceof Event)
        {
            $query->andWhere(['<>', 'event_id', $owner->id]);
        }



        if ($this->type == self::TYPE_NORMAL)
        {
            $count = $db->cache(function($db) use ($query) {
                    return $query->count();
                }, 5);
            $available =  $count == 0 ? true : false;
        }
        else
        {
            $itemsUsed  += $db->cache(function($db) use ($query) {
                    return $query->sum('quantity');
                }, 5);
            if ($itemsUsed < $this->gear->quantity)
            {
                $available = true;
            }

        }

        if ($available == false)
        {
            return $available;
        }


        $query2  = $this->_getAvailabilityQuery(RentGearItem::className(), $start, $end);

        if ($owner instanceof Rent)
        {
            $query2->andWhere(['<>', 'rent_id', $owner->id]);
        }


        if ($this->type == self::TYPE_NORMAL)
        {
            $count2 = $db->cache(function($db) use ($query2) {
                    return $query2->count();
                }, 5);
            $available =  $count2 == 0 ? true : false;
        }
        else
        {
            $itemsUsed  += $db->cache(function($db) use ($query2) {
                    return $query2->sum('quantity');
                }, 5);
            if ($itemsUsed < $this->gear->quantity)
            {
                $available = true;
            }
        }

        if ($available == false)
        {
            return $available;
        }


        $query3  = $this->_getAvailabilityQuery(OfferGearItem::className(), $start, $end);

        if ($owner instanceof Offer)
        {
            $query3->andWhere(['<>', 'offer_id', $owner->id]);
        }


        if ($this->type == self::TYPE_NORMAL)
        {
            $count3 = $db->cache(function($db) use ($query3) {
                    return $query3->count();
                }, 5);
            $available =  $count3 == 0 ? true : false;
        }
        else
        {
            $itemsUsed  += $db->cache(function($db) use ($query3) {
                    return $query3->sum('quantity');
                }, 5);
            if ($itemsUsed < $this->gear->quantity)
            {
                $available = true;
            }
        }

        return $available;


    }

    public static function getUnavailableDataInRange($start, $end)
    {
    	$start .= date(" 00:00:01");
    	$end .= date(" 23:59:59");
        $cacheKey = md5(__FUNCTION__.$start.$end);
        $cache = Yii::$app->getCache();
        $data = $cache->get($cacheKey);
        if ($data === false)
        {
            $dependencies = [];
            $dependencies[] = new DbDependency([
                'sql'=>'SELECT MAX(update_time) update_time FROM event_gear_item',
            ]);
            $dependencies[] = new DbDependency([
                'sql'=>'SELECT MAX(update_time) update_time FROM rent_gear_item',
            ]);
            $dependencies[] = new DbDependency([
                'sql'=>'SELECT MAX(update_time) update_time FROM gear',
            ]);
            $dependencies[] = new DbDependency([
                'sql'=>'SELECT MAX(update_time) update_time FROM offer_gear_item',
            ]);
            $dependency = new ChainedDependency([
                'dependencies' => $dependencies,
                'reusable' => true,
            ]);

            $db = Yii::$app->db;

            $result = $db->cache(function($db) use ($start, $end){
                $query = new Query();
                $eventQuery = new Query();
                $eventQuery
                    ->select(['t1.gear_item_id', 't2.gear_id', 't1.quantity'])
                    ->from(['t1'=>'event_gear_item'])
                    ->leftJoin(['t2'=>'gear_item'], 't1.gear_item_id=t2.id')
                    ->where([
                        'and',
                        ['<=', 'start_time', $start],
                        ['>=', 'end_time', $start],
                    ])
                    ->orWhere([
                        'and',
                        ['<=', 'start_time', $end],
                        ['>=', 'end_time', $end],
                    ])
                    ->orWhere([
                        'and',
                        ['>=', 'start_time', $start],
                        ['<=', 'end_time', $end],
                    ]);
                $rentQuery = clone $eventQuery;
                $offerQuery = clone $eventQuery;

                $rentQuery->from(['t1'=>'rent_gear_item']);
                $eventQuery->union($rentQuery, true);
                
                $offerQuery->from(['t1'=>'offer_gear_item']);
                $eventQuery->union($offerQuery, true);

                $query
                    ->select(['gear_item_id', 'gear_id', 'quantity'=>new Expression('sum(quantity)')])
                    ->from(['tmp'=>$eventQuery])
                    ->groupBy(['gear_item_id']);

                $result = $query->all();
                return $result;
            }, 0, $dependency);

            $data = ArrayHelper::index($result, 'gear_item_id', 'gear_id');
//            $cache->set($cacheKey, $data, 0, $dependency);
        }

        return $data;
    }

    public static function getUnavailableDatesInRange($start, $end)
    {
        $cacheKey = md5(__FUNCTION__.$start.$end);
        $cache = Yii::$app->getCache();
        $data = $cache->get($cacheKey);
        if ($data === false)
        {
            $dependencies = [];
            $dependencies[] = new DbDependency([
                'sql'=>'SELECT MAX(update_time) update_time FROM event_gear_item',
            ]);
            $dependencies[] = new DbDependency([
                'sql'=>'SELECT MAX(update_time) update_time FROM rent_gear_item',
            ]);
            $dependencies[] = new DbDependency([
                'sql'=>'SELECT MAX(update_time) update_time FROM offer_gear_item',
            ]);
            $dependency = new ChainedDependency([
                'dependencies' => $dependencies,
                'reusable' => true,
            ]);

            $db = Yii::$app->db;

            $result = $db->cache(function($db) use ($start, $end){
                $query = new Query();
                $eventQuery = new Query();
                $eventQuery
                    ->select(['t1.gear_item_id', 't1.start_time', 't1.end_time', 'type'=>new Expression('"event"'), 'owner_id'=>'event_id'])
                    ->from(['t1'=>'event_gear_item'])
                    ->where([
                        'and',
                        ['<=', 'start_time', $start],
                        ['>=', 'end_time', $start],
                    ])
                    ->orWhere([
                        'and',
                        ['<=', 'start_time', $end],
                        ['>=', 'end_time', $end],
                    ])
                    ->orWhere([
                        'and',
                        ['>=', 'start_time', $start],
                        ['<=', 'end_time', $end],
                    ]);
                $rentQuery = clone $eventQuery;
                $offerQuery = clone $eventQuery;

                $rentQuery->select(['t1.gear_item_id', 't1.start_time', 't1.end_time', 'type'=>new Expression('"rent"'), 'owner_id'=>'rent_id']);
                $rentQuery->from(['t1'=>'rent_gear_item']);
                $eventQuery->union($rentQuery, true);

                $offerQuery->select(['t1.gear_item_id', 't1.start_time', 't1.end_time', 'type'=>new Expression('"offer"'), 'owner_id'=>'offer_id']);
                $offerQuery->from(['t1'=>'offer_gear_item']);
                $eventQuery->union($offerQuery, true);

                $query
                    ->select(['gear_item_id', 'start_time', 'end_time', 'type', 'owner_id'])
                    ->from(['tmp'=>$eventQuery]);
//                $query->groupBy(['gear_item_id']);

                $result = $query->all();
                return $result;
            }, 0, $dependency);


            $data = ArrayHelper::index($result, null, 'gear_item_id');
//            $cache->set($cacheKey, $data, 0, $dependency);
        }

        return $data;
    }

    public function isAssignedTo($owner)
    {
        $assigned = false;
        $model = $this->getAssignConnection($owner);
        if ($model !== null)
        {
            $assigned = true;
        }
        return $assigned;
    }

    public function getAssignConnection($owner)
    {
        $connectionClass = null;
        $field = null;

        if ($owner instanceof Event)
        {
            $connectionClass = EventGearItem::className();
            $field = 'event_id';
        }
        else if ($owner instanceof Rent)
        {
            $connectionClass = RentGearItem::className();
            $field = 'rent_id';
        }
        else if ($owner instanceof Offer)
        {
            $connectionClass = OfferGearItem::className();
            $field = 'offer_id';
        }
        else
        {
            throw new HttpException(400, Yii::t('app', 'Brak implementacji!'));
        }

        $model = $connectionClass::find()->where([$field=>$owner->id, 'gear_item_id'=>$this->id])->one();
        return $model;
    }

    public function getUnavailableRanges($from_date, $to_date)
    {
        $data = ArrayHelper::getValue($this->getUnavailableDates($from_date, $to_date), $this->id, false);
        $ranges = [];
        if ($data==false)
        {
            return false;
        }
        else
        {
            $formatter = \Yii::$app->formatter;
            foreach ($data as $d)
            {
                $id = $d['owner_id'];
                $route = '';
                switch ($d['type'])
                {
                    case 'event':
                        $route = ['/event/view', 'id'=>$id];
                        break;
                    case 'rent':
                        $route = ['/rent/view', 'id'=>$id];
                        break;
                    case 'offer':
                        $route = ['/offer/default/view', 'id'=>$id];
                        break;
                }
                $r = $formatter->asDatetime($d['start_time'], 'short').' - '.$formatter->asDatetime($d['end_time'], 'short');
                $ranges[] = Html::a($r, $route, ['class'=>'btn btn-danger btn-xs']);
            }
        }
        return $ranges;
    }

    public function getUnavailableDates($from_date, $to_date)
    {
        $unavailableDates = GearItem::getUnavailableDatesInRange($from_date, $to_date);
        return $unavailableDates;
    }

    public function sendToService()
    {
        if ($this->gear->no_items)
        {
            $service = GearService::addNoItem($this);
            return $service;
        }
        if ($this->status == self::STATUS_SERVICE)
        {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'Urządzenie jest już w serwisie.'));
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try
        {
            $this->updateAttributes([
                'status'=>self::STATUS_SERVICE,
            ]);
            $service = GearService::add($this);

            $transaction->commit();
        }
        catch (\Exception $e)
        {
            $transaction->rollBack();
            throw $e;
        }
        return $service;
    }

    /**
     * @param string $term
     * @return static[]
     */
    public static function getList($term=null)
    {
        $models = static::find()
            ->where([
                'type'=>1,
            ])
            ->andFilterWhere([ 'or',
                ['like', 'name', $term],

            ])
//            ->limit(20)
            ->all();
        $list = [];

        foreach ($models as $model)
        {
            $list[$model->id] = $model;
        }

        return $list;
    }

    public function generateBarCode() {
        $options = [
            'elementId' => 'bar-'.$this->id,
            'value' => $this->getBarCodeValue(),
            'type' => 'code128',
            'settings' => [
                'output' => 'bmp',
                'barWidth' => 1,
                'barHeight' => 50,
            ],
        ];
        return '<div class="bar-code-img" id="bar-'.$this->id.'" data-name="' . $this->name . '"></div><div style="margin-top:5px;text-align:center;font-size:9px;">' . $this->getBarCodeValue() . '</div>' . BarcodeGenerator::widget($options);
    }

    public function generateQrCode($width=null) {
        if ($width)
        {
            return Html::img(Url::to(['qr-code/get-big-img', 'text'=>$this->getBarCodeValue()]), ['width'=>$width]);
        }else
            return Html::img(Url::to(['qr-code/get-img', 'text'=>$this->getBarCodeValue()]));
    }

    public function generateQrCodeAsLink() {
        return Html::a($this->generateQrCode(), Url::toRoute(['qr-code/get-big-img', 'text' => $this->getBarCodeValue()]), ['download' => $this->name.'.png']);
    }

    public function getBarCodeValue() {
        // 13 digits
        $c = Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
        if ($c->own_ean)
        {
            return $this->code;
        }else{
            return BarCode::SINGEL_PRODUCT . BarCode::OUR_WAREHOUSE . $this->getNineDigits();
        }
        
    }

    private function getNineDigits() {
        $id_length = strlen($this->id);
        return str_repeat('0', 9-$id_length) . $this->id;
    }

    public function numberOfAvailable() {
        $not_available = OutcomesGearOur::find()->where(['gear_id' => $this->id])->count();
        $returned_items = IncomesGearOur::find()->where(['gear_id' => $this->id])->count();

        // dostępne = wszystkie - wypożyczone + zwrócone
        return 1 - $not_available + $returned_items;
    }

    public function isAvailableForOutcome() {
        if ($this->active == 0 || $this->status > 2) {
            return false;
        }
        if ($this->outcomed){
                if ($this->gear->no_items)
                {
                    $total = $this->gear->quantity - $this->outcomed;
                    if ($total>0)
                    {
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
                
        }else{
            return true;
        }
    }

    public function isAvailableForOutcome2() {
        if (!$this->gear->no_items)
        {
            $not_available = OutcomesGearOur::find()->where(['gear_id' => $this->id])->count();
            $returned_items = IncomesGearOur::find()->where(['gear_id' => $this->id])->count();
            if ($not_available <= $returned_items) 
                {return 0;}else{return 1;}
        }else{
            $sum_out = 0;
            $sum_in = 0;
            $not_available = OutcomesGearOur::find()->where(['gear_id' => $this->id])->all();
            $returned_items = IncomesGearOur::find()->where(['gear_id' => $this->id])->all();  
            foreach ($not_available as $o)
            {
                $sum_out+=$o->gear_quantity;
            } 
            foreach ($returned_items as $i)
            {
                $sum_in+=$i->quantity;
            } 
            $not_available = $sum_out;
            $returned_items = $sum_in;           
        }

        // jeżeli tyle samo wydanych co zwróconych to jest dostępne
        if ($not_available <= $returned_items) {
            return 0;
        }else{
            return $not_available-$returned_items;
        }
    }

    public function getNotReturnedNumber()
    {
        if ($this->active == 0 || $this->status != 1) {
            return 0;
        }
        if ($this->gear->no_items)
        {
            $not_available = OutcomesGearOur::find()->where(['gear_id' => $this->id])->count();
            $returned_items = IncomesGearOur::find()->where(['gear_id' => $this->id])->count();
        }else{
            $sum_out = 0;
            $sum_in = 0;
            $not_available = OutcomesGearOur::find()->where(['gear_id' => $this->id])->all();
            $returned_items = IncomesGearOur::find()->where(['gear_id' => $this->id])->all();  
            foreach ($not_available as $o)
            {
                $sum_out+=$o->gear_quantity;
            } 
            foreach ($returned_items as $i)
            {
                $sum_in+=$o->gear_quantity;
            } 
            $not_available = $sum_out;
            $returned_items = $sum_in;           
        }

        $sum =    $not_available-  $returned_items;
        return $sum;  
    }

    public function getPlaceholderMap()
    {
        $map = [
            'gear.name' => $this->name,
        ];

        return $map;
    }

    public function getCalculatedVolume()
    {
        if ($this->gear->volume)
            return $this->gear->volume;
        else{
            $volume = $this->gear->width * $this->gear->height * $this->gear->depth/1000000;
            return $volume;
        }
        

        
    }

    public function getLastEvent()
    {
        $ids = ArrayHelper::map(OutcomesGearOur::find()->where(['gear_id' => $this->id])->asArray()->all(), 'outcome_id', 'outcome_id');
        $last = OutcomesWarehouse::find()->where(['IN','id', $ids])->orderBy(['start_datetime'=>SORT_DESC])->one();

        if (!$last)
        {
            $event['type'] = 'other';
            return $event;
        }
        $gear = OutcomesGearOur::find()->where(['gear_id' => $this->id])->andWhere(['outcome_id'=>$last->id])->one();
        if ($last->outcomesForEvents)
        {
            $event['type'] = 'event';
            $event['event'] = $last->outcomesForEvents[0]->event;
            $event['count'] = $gear->gear_quantity;
            $event['outcome_id'] = $last->id;
            $event['outcome_datetime'] = $last->start_datetime;
            return $event;
        }
        if ($last->outcomesForRents){
            $event['type'] = 'rent';
            $event['event'] = $last->outcomesForRents[0]->rent;
            $event['count'] = $gear->gear_quantity;
            $event['outcome_id'] = $last->id;
            $event['outcome_datetime'] = $last->start_datetime;
            return $event;
        }
        $event['type'] = 'other';
        return $event;
    }

    public function changeServiceStatut($from, $to)
    {
        $checkConflicts = false;
        $checkReturn = false;
        if (!$from)
        {
            $statut = GearServiceStatut::findOne($to);
            if ($statut->type==1)
            {
                $this->status = self::STATUS_SERVICE;
                //sprawdzamy konflikty
                $checkConflicts = true;
            }
            if ($statut->type==2)
            {
                $this->status = self::STATUS_ACTIVE;
            }
            if ($statut->type==3)
            {
                $this->status = self::STATUS_NEED_SERVICE;
            }
        }else{
            $statut = GearServiceStatut::findOne($to);
            $statut2 = GearServiceStatut::findOne($from);
            if ($statut->type==1)
            {
                $this->status = self::STATUS_SERVICE;
                if ($statut2->type!=1)
                    $checkConflicts = true;
                //sprawdzamy konlikty
            }
            if ($statut->type==2)
            {
                $this->status = self::STATUS_ACTIVE;
                if ($statut2->type==1)
                    $checkReturn = true;
            }
            if ($statut->type==3)
            {
                $this->status = self::STATUS_NEED_SERVICE;
                if ($statut2->type==1)
                    $checkReturn = true;
            }         
        }
        $this->save();
        if ($checkConflicts)
            $this->gear->checkConflicts();
        if ($checkReturn)
            $this->gear->checkConflictsAfterReturn();
    }

    public function afterSave($insert, $changedAttributes)
    {
        //$this->getNoItemsItem();
        parent::afterSave($insert, $changedAttributes);
        if ($insert)
        {
            Note::createNote(1, 'gearItemAdded', $this, $this->id);
        }
    }

public function beforeSave($insert) {
    if ($insert)
        if (!$this->warehouse_id)
        {
            $w = \common\models\Warehouse::findOne(['type'=>1]);
            $this->warehouse_id = $w->id;
        }

    return parent::beforeSave($insert);
}

    public function makeIncome($warehouse, $event_id, $packlist_id=null, $type, $quantity)
    {
                
        if ($this->gear->no_items == 1) {
        if ($quantity<$this->outcomed)
                $this->outcomed-=$value;
        else
                $this->outcomed = 0;


        }else{
            $this->outcomed = 0;
            $this->event_id = null;
            $this->rent_id = null;
            $this->packlist_id = null;
            $this->warehouse_id = $warehouse;
        }
        $this->save();

        $wq = WarehouseQuantity::findOne(['warehouse_id'=>$warehouse, 'gear_id'=>$this->gear_id]);
        if (!$wq)
        {
            $wq = new WarehouseQuantity();
            $wq->warehouse_id = $warehouse;
            $wq->gear_id = $this->gear_id;
        }
        $wq->quantity+=$quantity;
        $wq->save();
        if ($type == OutcomesWarehouse::EVENT_TYPE_EVENT)
        {
            $eq = EventGearOutcomed::findOne(['packlist_id'=>$packlist_id,  'gear_id'=>$this->gear_id]);
            $eq->quantity-=$quantity;
            $eq->save();
        }else{
            $eq = RentGearOutcomed::findOne(['rent_id'=>$event_id,  'gear_id'=>$this->gear_id]);
            $eq->quantity-=$quantity;
            $eq->save();
        }

    }

    public function makeOutcome($warehouse, $event_id, $packlist_id=null, $type, $quantity)
    {
                                
                               
                                if ($this->gear->no_items == 1) {
                                    //wydajemy ilościowo
                                    $this->outcomed+=$quantity;
                                    $wq = \common\models\WarehouseQuantity::findOne(['gear_id'=>$this->gear_id, 'warehouse_id'=>$warehouse]);
                                    $wq->quantity -=$quantity;
                                    $wq->save();
                                    if ($type == OutcomesWarehouse::EVENT_TYPE_EVENT) {
                                        $eq = \common\models\EventGearOutcomed::findOne(['gear_id'=>$this->gear_id, 'event_id'=>$event_id, 'packlist_id'=>$packlist_id]);
                                        if (!$eq)
                                        {
                                            $eq = new \common\models\EventGearOutcomed(['gear_id'=>$this->gear_id, 'event_id'=>$event_id, 'packlist_id'=>$packlist_id, 'quantity'=>0]);

                                        }
                                        $eq->quantity +=$quantity;
                                        $eq->save();
                                    }
                                    if ($type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                                        $eq = \common\models\RentGearOutcomed::findOne(['gear_id'=>$this->gear_id, 'rent_id'=>$event_id]);
                                        if (!$eq)
                                        {
                                            $eq = new \common\models\RentGearOutcomed(['gear_id'=>$this->gear_id, 'rent_id'=>$event_id,  'quantity'=>0]);

                                        }
                                        $eq->quantity +=$quantity;
                                        $eq->save();
                                    }
                                }else{
                                    //wydajemy egzemplarzowo
                                    $this->outcomed = 1;
                                    $this->warehouse_id = null;
                                    if ($type == OutcomesWarehouse::EVENT_TYPE_EVENT) {
                                        $this->packlist_id = $packlist_id;
                                        $this->rent_id = null;
                                        $this->event_id = $event_id;
                                    }
                                    if ($type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                                        $this->packlist_id = null;
                                        $this->rent_id = $event_id;
                                        $this->event_id = null;
                                    }
                                    $wq = \common\models\WarehouseQuantity::findOne(['gear_id'=>$this->gear_id, 'warehouse_id'=>$warehouse]);

                                    $wq->quantity -=1;
                                    $wq->save();
                                    if ($type == OutcomesWarehouse::EVENT_TYPE_EVENT) {
                                        $eq = \common\models\EventGearOutcomed::findOne(['gear_id'=>$this->gear_id, 'event_id'=>$event_id, 'packlist_id'=>$packlist_id]);
                                        if (!$eq)
                                        {
                                            $eq = new \common\models\EventGearOutcomed(['gear_id'=>$this->gear_id, 'event_id'=>$event_id, 'packlist_id'=>$packlist_id, 'quantity'=>0]);

                                        }
                                        $eq->quantity +=1;
                                        $eq->save();
                                    }
                                    if ($type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                                        $eq = \common\models\RentGearOutcomed::findOne(['gear_id'=>$this->gear_id, 'rent_id'=>$event_id]);
                                        if (!$eq)
                                        {
                                            $eq = new \common\models\RentGearOutcomed(['gear_id'=>$this->gear_id, 'rent_id'=>$event_id,  'quantity'=>0]);

                                        }
                                        $eq->quantity +=1;
                                        $eq->save();
                                    }
                                }
                                $this->save();
    }

}



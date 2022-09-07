<?php

namespace common\models;

use backend\modules\offers\models\OfferExtraItem;
use common\behaviors\EventDatesBehavior;
use common\behaviors\WorkingTimeBehavior;
use common\models\query\OfferQuery;
use common\helpers\ArrayHelper;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;


/**
 * This is the model class for table "offer".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $name
 * @property integer $location_id
 * @property string $termin_from
 * @property string $termin_to
 * @property string $page
 * @property integer $manager_id
 * @property string $offer_date
 * @property string $comment
 */
class Offer extends \common\models\base\Offer
{
    public $eventDateRange;
    public $packingDateRange;
    public $montageDateRange;
    public $readinessDateRange;
    public $practiceDateRange;
    public $disassemblyDateRange;
    public $event_type;
    public $schedules;
    public $roleIds;

    const STATUS_ACCEPT = 1;
    const STATUS_NOT_ACCEPT = 0;

    public static function find()
    {
        return new OfferQuery(get_called_class());
    }

    public static function getStatusList()
    {
        return OfferStatut::getList();
    }

    public function getStatusLabel()
    {
        return static::getStatusList()[$this->status];
    }

    public function getStatusButton($class="")
    {
        $status = OfferStatut::findOne($this->status);
        return '<span class="label label-primary '.$class.'" style="background-color:'.$status->color.';"" data-offerid='.$this->id.'>'.$status->name.'</span>';
    }

    public function attributeLabels()
    {
        $labels = [
            'eventDateRange' => Yii::t('app', 'Impreza'),
            'packingDateRange' => (Yii::$app->params['companyID']=="imagination")?Yii::t('app','Załadunek') : Yii::t('app', 'Pakowanie'),
            'montageDateRange' => Yii::t('app', 'Montaż'),
            'readinessDateRange' => Yii::t('app', 'Gotowość'),
            'practiceDateRange' => Yii::t('app', 'Próby'),
            'disassemblyDateRange' => Yii::t('app', 'Demontaż'),
            'event_type'=>Yii::t('app', 'Rodzaj wydarzenia'),
        ];

        return array_merge(parent::attributeLabels(), $labels);
    }

    public function rules()
    {
        $rules = [
            [['roleIds'], 'each', 'rule'=>['integer']],
            [['eventDateRange','packingDateRange','montageDateRange','readinessDateRange','practiceDateRange','disassemblyDateRange', 'schedules'], 'string'],
            [['event_start'], 'validateDates', 'skipOnEmpty'=>false],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function behaviors()
    {
        $behaviors = [
            'workingTime'=> [
                'class'=>WorkingTimeBehavior::className(),
                'connectionClassName'=>OfferGearItem::className(),
                'itemIdAttribute'=>'gear_item_id',

            ],
            'eventDatesBehavior' => [
                'class'=>EventDatesBehavior::className(),
            ],


            'link' => [
                'class' => \common\behaviors\LinkBehavior::className(),
                'attributes' => [
                    'roleIds',
                ],
                'relations' => [
                    'roles',
                ],
                'modelClasses'=>[
                    'common\models\UserEventRole',
                ],
            ],





//            'timestamp' => [
//                'class' => TimestampBehavior::className(),
//                'attributes' => [
//                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_time','update_time'],
//                    ActiveRecord::EVENT_BEFORE_UPDATE => 'update_time',
//                ],
//                'value' => function() { return date("Y-m-d H:i:s"); },
//            ],
        ];
        $behaviors['codeBehavior'] = [
            'class'=>\common\behaviors\CodeBehavior::className(),
            'prefix' => 'O',
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }

    public function beforeDelete()
    {
        $this->customer->createLog('offer_delete', $this->id);
        Note::createNote(3, 'offerDeleted', $this, $this->id);
        return true;
    }

//    public function prepareDateAttributes()
//    {
//        $attributes = [
//            'event',
//            'packing',
//            'montage',
//            'practice',
//            'readiness',
//            'disassembly'
//
//        ];
//        foreach ($attributes as $attribute)
//        {
//            if(isset($this->{$attribute.'_start'}) && isset($this->{$attribute.'_end'})){
//                $this->{$attribute.'DateRange'} = $this->{$attribute.'_start'}.' - '.$this->{$attribute.'_end'};
//            } else {
//                $this->{$attribute.'DateRange'} = null;
//            }
//
//        }
//
//    }
//
//    public function setDateAttributes()
//    {
//        $attributes = [
//            'event',
//            'packing',
//            'montage',
//            'practice',
//            'readiness',
//            'disassembly'
//
//        ];
//        foreach ($attributes as $attribute)
//        {
//            if($this->{$attribute.'DateRange'} !== '' && $this->{$attribute.'DateRange'} !== null){
//                $date_arr = explode(" - ", $this->{$attribute.'DateRange'});
//                $this->{$attribute.'_start'} = $date_arr[0];
//                $this->{$attribute.'_end'} = $date_arr[1];
//            }
//        }
//
//        return true;
//
//    }

    public static function getAssignedQuantities($id, $type2, $itemId)
    {
        if ($type2)
        {
            if ($type2=='gear')
            {
                $data = OfferGearItem::find()
                ->where(['offer_id'=>$id])->andWhere(['offer_gear_id'=>$itemId])
                ->all();
            }
            if ($type2=='outerGear')
            {
                $data = OfferGearItem::find()
                ->where(['offer_id'=>$id])->andWhere(['offer_outer_gear_id'=>$itemId])
                ->all();
            }
            if ($type2=='extraGear')
            {
                $data = OfferGearItem::find()
                ->where(['offer_id'=>$id])->andWhere(['offer_group_id'=>$itemId])
                ->all();
            }
        }else{
            $data = OfferGearItem::find()
            ->where(['offer_id'=>$id])
            ->all();           
        }

        $list = ArrayHelper::map($data, 'gear_item_id', 'quantity');
        return $list;
    }

    public static function getAssignedGearQuantities($id, $type2, $itemId)
    {
        if ($type2)
        {
            if ($type2=='gear')
            {
                $data = OfferGear::find()
                ->where(['offer_id'=>$id])->andWhere(['offer_gear_id'=>$itemId])
                ->all();
            }
            if ($type2=='outerGear')
            {
                $data = OfferGear::find()
                ->where(['offer_id'=>$id])->andWhere(['offer_outer_gear_id'=>$itemId])
                ->all();
            }
            if ($type2=='extraGear')
            {
                $data = OfferGear::find()
                ->where(['offer_id'=>$id])->andWhere(['offer_group_id'=>$itemId])
                ->all();
            }
        }else{
            $data = OfferGear::find()
            ->where(['offer_id'=>$id])
            ->all();           
        }
        $list = ArrayHelper::map($data, 'gear_id', 'quantity');
        return $list;
    }

    public static function getAssignedOuterModelQuantities($id)
    {
        $data = OfferOuterGear::find()
            ->where(['offer_id'=>$id])
            ->all();
        $list = ArrayHelper::map($data, 'outer_gear_model_id', 'quantity');
        return $list;
    }

    public static function assignGear($id, $itemId, $quantity=null, $discount=null)
    {
        $model = OfferGear::findOne(['offer_id'=>$id, 'gear_id'=>$itemId]);
        $gear = Gear::findOne($itemId);
        if ($model===null)
        {
            $model = new OfferGear();
            $model->price = $gear->price;
        }
        $model->offer_id = $id;
        $model->gear_id = $itemId;
        $model->quantity = $quantity;
        $model->discount = $discount;
        return $model->save();
    }

    public static function removeGear($id, $itemId)
    {
        foreach (OfferGear::find()->where(['offer_id'=>$id, 'id'=>$itemId])->all() as $g) {
            $g->delete();
        }
    }

    public static function removeRole($id, $itemId, $time_type)
    {
        foreach (OfferRole::find()->where(['offer_id'=>$id, 'role_id'=>$itemId, 'time_type'=>$time_type])->all() as $g) {
            $g->delete();
        }
    }

    public static function removeCustomField($id, $itemId)
    {
                foreach (OfferCustomItems::find()->where(['offer_id'=>$id, 'id'=>$itemId])->all() as $g) {
            $g->delete();
        }
    }

    public static function assignGearItem($id, $itemId, $quantity=null, $discount=null)
    {
        
        $gear_item = GearItem::find()->joinWith(['gear'])->where(['gear_item.id'=>$itemId])->one();
        $offer = Offer::findOne($id);
        $isAvailable = $gear_item->isAvailable($offer);

        if($gear_item->gear->getOfferGearItemsQuantity($id) > 0 && $isAvailable ){
            $model = OfferGearItem::findOne(['offer_id'=>$id, 'gear_item_id'=>$itemId]);
            if ($model===null)
            {
                $model = new OfferGearItem();
            }
            $model->offer_id = $id;
            $model->gear_item_id = $itemId;
            return $model->save();
        }

        return false;
        
    }

    public static function removeGearItem($id, $itemId)
    {
        return OfferGearItem::deleteAll(['offer_id'=>$id, 'gear_item_id'=>$itemId]);
    }

    public static function assignOuterGearModel($offer_id, $outer_gear_id, $quantity=null, $discount=null, $type2 = null, $item = null)
    {
        if (!$type2)
            $params = ['offer_id' => $offer_id, 'outer_gear_model_id' => $outer_gear_id, 'type'=>1 ];
        else
        {
            if ($type2=='gear')
            {
                
                $params = ['offer_id' => $offer_id, 'outer_gear_model_id' => $outer_gear_id, 'type'=>2, 'offer_gear_id'=>$item];
            }
            if ($type2=='outerGear')
            {
                $params = ['offer_id' => $offer_id, 'outer_gear_model_id' => $outer_gear_id, 'type'=>2, 'offer_outer_gear_id'=>$item ];
            }
            if ($type2=='extraGear')
            {
                $g = OfferExtraItem::findOne($item);
                if ($g->type==4)
                {
                    $visible = 0;
                }else{
                    $visible = 1;
                }
                $params = ['offer_id' => $offer_id, 'outer_gear_model_id' => $outer_gear_id, 'type'=>2, 'offer_group_id'=>$item, 'visible'=>$visible];
            }
        }
        $model = OfferOuterGear::findOne($params);
        if ($model===null)
        {
            $model = new OfferOuterGear($params);
            $model->position = 1000;
        }
        $model->quantity = $quantity;
        $model->discount = $discount;
        $model->gears_price_id = Offer::getDefaultGearsPrice($offer_id);
        return $model->save();
    }

    public function getDefaultGearsPrice($offer_id)
    {
        $offer = Offer::findOne($offer_id);
        $ids = ArrayHelper::map(GearsPriceGroup::find()->where(['price_group_id'=>$offer->price_group_id])->asArray()->all(), 'gears_price_id', 'gears_price_id');
        $price = GearsPrice::find()->where(['type'=>1])->andWhere(['id'=>$ids])->one();
        return $price->id;
    }

    public static function removeOuterGear($id, $itemId)
    {
        foreach (OfferOuterGear::find()->where(['offer_id'=>$id, 'id'=>$itemId])->all() as $g) {
            $g->delete();
        }
    }


    public static function assignVehicle($id, $itemId, $quantity=null)
    {
        $model = OfferVehicle::findOne(['offer_id'=>$id, 'vehicle_id'=>$itemId]);
        if ($model===null)
        {
            $model = new OfferVehicle();
        }
        $model->offer_id = $id;
        $model->vehicle_id = $itemId;
        $model->quantity = $quantity;
        return $model->save();
    }

    public static function removeVehicle($id, $itemId)
    {
        foreach (OfferVehicle::find()->where(['offer_id'=>$id, 'vehicle_id'=>$itemId])->all() as $g) {
            $g->delete();
        }
    }

    public function getVehicles()
    {
        return $this->hasMany(\common\models\Vehicle::className(), ['id' => 'vehicle_id'])->viaTable('offer_vehicle', ['offer_id' => 'id']);
    }

    public function getOuterGear()
    {
        return $this->hasMany(\common\models\OuterGearModel::className(), ['id' => 'outer_gear_model_id'])->viaTable('offer_outer_gear', ['offer_id' => 'id']);
    }

//    public function getTimeStart()
//    {
//        //fixme: co tu ma być? Do magazynu
//        return $this->term_from;
//    }
//
//    public function getTimeEnd()
//    {
//        //fixme: co tu ma być? Do magazynu
//        return $this->term_to;
//    }

    // public function getOuterGear()
    // {
    //     return $this->hasOne(\common\models\OuterGear::className(), ['id' => 'outer_gear_id'])->viaTable('offer_outer_gear', ['offer_id' => 'id']);
    // }

    public function getAssignedOuterGearModel($offer_id,$outer_gear_id, $type=null, $item=null) {
        if (!$type)
            return OfferOuterGear::findOne(['offer_id' => $offer_id, 'outer_gear_model_id' => $outer_gear_id, 'type'=>1 ]);
        else
        {
            if ($type=='gear')
            {
                return OfferOuterGear::findOne(['offer_id' => $offer_id, 'outer_gear_model_id' => $outer_gear_id, 'type'=>2, 'offer_gear_id'=>$item ]);
            }
            if ($type=='outerGear')
            {
                return OfferOuterGear::findOne(['offer_id' => $offer_id, 'outer_gear_model_id' => $outer_gear_id, 'type'=>2, 'offer_outer_gear_id'=>$item ]);
            }
            if ($type=='extraGear')
            {
                return OfferOuterGear::findOne(['offer_id' => $offer_id, 'outer_gear_model_id' => $outer_gear_id, 'type'=>2, 'offer_group_id'=>$item ]);
            }
        }
    }

    public function getAssignedGear($offer_id,$gear_id) {
        return OfferGear::findOne(['offer_id' => $offer_id, 'gear_id' => $gear_id ]);
    }

    public static function getAssignedGearsList($id)
    {
        $model = Offer::find()->where(['offer.id' => $id])->one();
        $gears = Gear::find()->indexBy('id')->all();
        $outerGears = OuterGearModel::find()->indexBy('id')->all();
        $gcat = GearCategory::getMainList(true);
        $allgcat = GearCategory::find()->indexBy('id')->where(['active' => 1, 'visible' => 1, 'readonly' => 0])->addOrderBy('root, lft')->all();

        $discount = [];
        if ($model->customer !== null)
        {
            $discount = CustomerDiscountCategory::find()
                ->indexBy('category_id')->joinWith(['customerDiscount','customerDiscountCustomer'])
                ->where(['customer_discount_customer.customer_id' => $model->customer->id])->all();
        }

        $offer_gear_arr = [];
        if (isset($model->offerGears))
            foreach($model->offerGears as $keyog => $og){
                if ($og->type==1)
                    $offer_gear_arr[$og->gear_id] = $og;
            }

        $offer_outer_gear_arr = [];
        if (isset($model->offerOuterGears))
        foreach($model->offerOuterGears as $keyog => $og){
            $offer_outer_gear_arr[$og->outer_gear_model_id] = $og;
        }

        $gear_list = [];
        if (isset($model->offerGears))
        foreach ($model->offerGears as $key => $offerGear) {
            if ($offerGear->type==1){
                if(isset($gears[$offerGear->gear_id]) && isset($allgcat[$gears[$offerGear->gear_id]->category_id])){
                    $gear = $gears[$offerGear->gear_id];
                    $curr_cat = $allgcat[$gear->category_id];

                    foreach ($gcat as $key => $cat) {

                        if($cat->lft <= $curr_cat->lft && $cat->rgt >= $curr_cat->rgt){
                            $gear_list[$cat->name][] = ['gear' => $gear, 'discount' => isset($discount[$curr_cat->id]) ? $discount[$curr_cat->id]->customerDiscount->discount : 0, 'offer_gear' => $offerGear ];
                        }
                    }
                }
            }

        }
         if (isset($model->offerOuterGears))   
        foreach ($model->offerOuterGears as $key => $offerOuterGear) {
            if ($offerOuterGear->type==1){
            if(isset($outerGears[$offerOuterGear->outer_gear_model_id]) && isset($allgcat[$outerGears[$offerOuterGear->outer_gear_model_id]->category_id])){
                $outerGear = $outerGears[$offerOuterGear->outer_gear_model_id];
                $curr_cat = $allgcat[$outerGear->category_id];

                foreach ($gcat as $key => $cat) {

                    if($cat->lft <= $curr_cat->lft && $cat->rgt >= $curr_cat->rgt){
                        $gear_list[$cat->name][] = ['outerGear' => $outerGear, 'discount' => isset($discount[$curr_cat->id]) ? $discount[$curr_cat->id]->customerDiscount->discount : 0, 'offer_outer_gear' => $offerOuterGear ];
                    }
                }
            }
        }

        }

        return $gear_list;
    }

    public static function getGearsSummListWithMainCategories($id) {
        $gear_list = Offer::getAssignedGearsList($id);
        $new_list = [];
        foreach ($gear_list as $main_cat_name => $products) {
            $summ_of_one_cat = 0;

            foreach ($products as $key_p => $value) {
                if(isset($value['gear'])){
                    $gear = OfferGear::find()->where(['offer_id' => $id])->andWhere(['gear_id' => $value['gear']->id])->one();
                    if ($gear->price === null) {
                        $gear->price = $gear->gear->price;
                    }
                } else {
                    $gear = OfferOuterGear::find()->where(['offer_id' => $id])->andWhere(['outer_gear_model_id' => $value['outerGear']->id])->one();
                    if ($gear->price === null) {
                        $gear->price = $gear->outerGearModel->getSellingPrice();
                    }
                }

                $price_with_discount = $gear->price * (1 - $gear->discount/100);
                $total = $gear->quantity * $price_with_discount;
                $total += $gear->quantity * ($gear->duration-1)*( $price_with_discount * ($gear->first_day_percent/100));

                $summ_of_one_cat += $total;
            }
            $new_list[$main_cat_name] = $summ_of_one_cat;
        }

        /* @var $gear \backend\modules\offers\models\OfferExtraItem */
        foreach (self::findOne($id)->getExtraItem(OfferExtraItem::TYPE_GEAR) as $gear) {
            $first_day_percent = 100;
            $first_day_percent = $gear->first_day_percent;

            $price_with_discount = $gear->price * (1-$gear->discount/100);
            $firstDay = $gear->quantity * $price_with_discount;
            $value = $firstDay;
            $value += $gear->quantity * ($gear->duration-1)*( $price_with_discount * ($first_day_percent/100));

            if (isset($new_list[$gear->category->name])) {
                $new_list[$gear->category->name] += $value;
            }
            else {
                $new_list[$gear->category->name] = $value;
            }
        }

        return $new_list;
    }

    public function getGearDataProvider(){
        $gearQuery = Gear::find()->joinWith(['offerGears'])
            ->andFilterWhere([
                'offer_gear.offer_id'=>$this->id,
            ]);

        $gearDataProvider = new ActiveDataProvider([
            'query'=>$gearQuery,
            'pagination'=>false,
            'sort'=>[
                'defaultOrder' => ['sort_order'=>SORT_ASC],
            ]
        ]);

        return $gearDataProvider;
    }

    public function getOuterGearDataProvider(){
        $gearQuery = OuterGearModel::find()->joinWith(['offerOuterGears'])
            ->andFilterWhere([
                'offer_outer_gear.offer_id'=>$this->id,
            ]);

        $gearDataProvider = new ActiveDataProvider([
            'query'=>$gearQuery,
            'pagination'=>false,
            'sort'=>[
                'defaultOrder' => ['sort_order'=>SORT_ASC],
            ]
        ]);

        return $gearDataProvider;
    }

    public function getGearData()
    {
        $data = [];

        $models = $this->getOfferGears()
            ->innerJoinWith(['gear'=>function($query)
                {
                    $query->innerJoinWith('category');
            }])
            ->all();
        foreach ($models as $model)
        {
            /* @var $model \common\models\OfferGear */
            if ($model->type==1)
            {
                $gear = $model->gear;
                $cat = $gear->category->getMainCategory();

                $quantity = $model->quantity===null ? 1 : $model->quantity;
                $price = $model->price;
                if ($price == null) {
                    $price = (float)$gear->price;
                }
                $value = $model->getValue();
                $sub_gears = false;
                //$volume = $gear->width * $gear->height * $gear->depth;
                $volume = $gear->countVolume();
                
                if (isset($data[$cat->name]) == false)
                {
                    $data[$cat->name] = [];
                }
                $total_value = $value;
                $offerGears = OfferGear::find()->where(['offer_gear_id'=>$model->id])->andWhere(['visible'=>0])->all();
                foreach($offerGears as $og)
                {
                    $total_value+=$og->getValue();
                    $sub_gears = true;

                }
                 $offerGears = OfferOuterGear::find()->where(['offer_gear_id'=>$model->id])->andWhere(['visible'=>0])->all();
                foreach($offerGears as $og)
                {
                    $total_value+=$og->getValue();
                    $sub_gears = true;
                }
                if ((!$quantity)||($model->discount==100)||(($model->duration<1)))
                    $total_price = $total_value;
                        else{
                            if($sub_gears)
                            {
                                if ($model->gears_price_id)
                                    $total_price = $model->gearsPrice->getSinglePrice($total_value, $quantity, $model->discount, $model->duration);
                                else
                                    $total_price = $total_value/($quantity*(1 - $model->discount/100)*(1+($model->duration-1)*$model->first_day_percent/100));
                            }else{
                                $total_price = $model->price;
                            }

                    
                        }

                $data[$cat->name][] = [
                    'type' => 'gear',
                    'gear_id' => $model->gear_id,
                    'id' => $model->id,
                    'name' => $gear->name,
                    'quantity'=>$quantity,
                    'discount'=>$model->discount,
                    'duration'=>$model->duration,
                    'price' => $price,
                    'value' => $value,
                    'volume' => $volume,
                    'power_consumption' => $gear->power_consumption,
                    'weight' => $gear->weight,
                    'description'=>$model->description,
                    'visible'=>$model->visible,
                    'total_price' => $total_price,
                    'total_value' => $total_value,
                    'photo' => $model->gear->photo,
                    'long_description' => $model->gear->offer_description,
                    'vat_rate'=>$model->vat_rate,
                    'gears_price_id'=>$model->gears_price_id,
                    'position'=>$model->position,
                    'visible'=>$model->visible
                                    ];                
            }

        }
        return $data;
    }

    public function getOuterGearData()
    {
        $data = [];
        $models = $this->getOfferOuterGears()
            ->innerJoinWith(['outerGearModel'=>function($query) {
                    $query->innerJoinWith('category');

                }])
            ->all();
        foreach ($models as $model)
        {
            if ($model->type==1)
            {
            $outerGear = $model->outerGearModel;
            if ($model->outerGearModel->category->lvl==1)
            {
                $cat = $model->outerGearModel->category;
            }else{
                $cat = $model->outerGearModel->category->parents()->andWhere(['lvl'=>1])->one();
            }
            

            $quantity = $model->quantity===null ? 1 : $model->quantity;
            $price = $model->price;
            if ($price == null) {
                $price = (float)$model->outerGearModel->getSellingPrice();
            }
            $price_with_discount = $price * (1 - $model->discount/100);
            $value = $model->getValue();

            $volume = $outerGear->countVolume();
            if (!isset($data[$cat->name]))
            {
                $data[$cat->name] = [];
            }
                $total_value = $value;
                $sub_gears = false;
                $offerGears = OfferGear::find()->where(['offer_outer_gear_id'=>$model->id])->andWhere(['visible'=>0])->all();
                foreach($offerGears as $og)
                {
                    $total_value+=$og->getValue();
                    $sub_gears = true;
                }
                 $offerGears = OfferOuterGear::find()->where(['offer_outer_gear_id'=>$model->id])->andWhere(['visible'=>0])->all();
                foreach($offerGears as $og)
                {
                    $total_value+=$og->getValue();
                    $sub_gears = true;
                }
                if ((!$quantity)||($model->discount==100)||(($model->duration<1)))
                    $total_price = $total_value;
                        else{
                            if($sub_gears)
                            {
                                if ($model->gears_price_id)
                                    $total_price = $model->gearsPrice->getSinglePrice($total_value, $quantity, $model->discount, $model->duration);
                                else
                                    $total_price = $total_value/($quantity*(1 - $model->discount/100)*(1+($model->duration-1)*$model->first_day_percent/100));
                            }else{
                                $total_price = $model->price;
                            }

                    
                        }
            $data[$cat->name][] = [
                'type' => 'outerGear',
                'gear_id' => $model->outerGearModel->id,
                'id' =>$model->id,
                'name' => $model->outerGearModel->name,
                'quantity'=>$quantity,
                'discount'=>$model->discount,
                'duration' => $model->duration,
                'description'=>$model->description,
                'price' => $price,
                'volume' => $volume,
                'value' => $value,
                'power_consumption' => $outerGear->power_consumption,
                'weight' => $outerGear->weight,
                'visible'=>$model->visible,
                    'total_price' => $total_price,
                    'total_value' => $total_value,
                    'vat_rate'=>$model->vat_rate,
                    'gears_price_id'=>$model->gears_price_id,
                    'position'=>$model->position
            ];
            }
        }
        return $data;
    }

    public function getExtraGears() {
        $data = [];
        $models = $this->getExtraItem([1,4]);
        foreach ($models as $model) {

            $first_day_percent = $model->first_day_percent;
            /*
            if ($settings = OfferSetting::find()->where(['offer_id' => $this->id])->andWhere(['category_id' => $model->category->id])->one()) {
                $first_day_percent = $settings->first_day_percent;
            }
            */
            $price_with_discount = $model->price * (1-$model->discount/100);
            $firstDay = $model->quantity * $price_with_discount;
            $quantity = $model->quantity;
            $value = $model->getValue();
                $total_value = $value;
                $sub_gears = false;
                $offerGears = OfferGear::find()->where(['offer_group_id'=>$model->id])->andWhere(['visible'=>0])->all();
                foreach($offerGears as $og)
                {
                    $total_value+=$og->getValue();
                    $sub_gears = true;
                }
                 $offerGears = OfferOuterGear::find()->where(['offer_group_id'=>$model->id])->andWhere(['visible'=>0])->all();
                foreach($offerGears as $og)
                {
                    $total_value+=$og->getValue();
                    $sub_gears = true;
                }
                if ((!$quantity)||($model->discount==100)||(($model->duration<1)))
                    $total_price = $total_value;
                        else{
                            if($sub_gears)
                            {
                                if ($model->gears_price_id)
                                    $total_price = $model->gearsPrice->getSinglePrice($total_value, $quantity, $model->discount, $model->duration);
                                else
                                    $total_price = $total_value/($quantity*(1 - $model->discount/100)*(1+($model->duration-1)*$model->first_day_percent/100));
                            }else{
                                $total_price = $model->price;
                            }

                    
                        }
            $data[$model->category->name][] = [
                'type' => 'extraGear',
                'id' => $model->id,
                'name' => $model->name,
                'quantity' => $model->quantity,
                'discount' => $model->discount,
                'price' => $model->price,
                'duration' => $model->duration,
                'value' => $value,
                'description'=>$model->description,
                    'visible'=>$model->visible,
                    'total_price' => $total_price,
                    'total_value' => $total_value,
                    'vat_rate'=>$model->vat_rate,
                    'gears_price_id'=>$model->gears_price_id,
                    'position'=>$model->position
            ];
        }
        return $data;
    }

    public function isOfferInCompanyCity()
    {
        $value = false;

        $city = Yii::$app->settings->get('companyCity', 'main');
        $city = strtolower($city);
        if (empty($this->location) == false && strtolower($this->location->city) == $city)
        {
            $value = true;
        }


        return $value;
    }

    public function getCustomData($withName = true)
    {
        $d = [];
        $models = $this->getOfferCustomItems()
            ->orderBy(['department_id'=>SORT_ASC])
            ->all();

        foreach ($models as $model)
        {   /* @var $model \common\models\OfferCustomItems */


            $price = $model->price - ($model->price*($model->discount/100));
            $value = $model->quantity*$model->diff_count * $price;
            $d[$model->id] = [
                'value'=>$value,
                'price'=>$model->price,
                'diff_count' => $model->diff_count,
                'discount' => $model->discount,
                'quantity' => $model->quantity,
                'name'=>$model->name,
                'id' => $model->id,
                'department' => $model->department==null ? '' : $model->department->name,
            ];
        }
        $data = [];
        if ($withName == true)
        {
            $data = [
                'Inne' => $d,
            ];
        }
        else
        {
            $data = $d;
        }

        return $data;
    }

    public function getVehicleData() {
        $d = [];
        $models = $this->getOfferVehicles()->innerJoinWith('vehicle')->all();

        foreach ($models as $model)
        {   /* @var $model \common\models\OfferVehicle */

            $value = 0;
            if ($model->price_type == OfferVehicle::PRICE_TYPE_CITY)
            {
                $value = $model->quantity * $model->price;
            }
            else
            {
                $value = $model->quantity * $model->price * $model->distance;
            }
            $name = $model->vehicle->name;   
            if ($model->vehicle_price_id)  
            {
                $vp = $model->vehiclePrice->name;
            }   else{
                $vp = "-";
            }       
            $d[$model->id] = [
                'value'=>$value,
                'price'=>$model->price,
                'quantity' => $model->quantity,
                'name'=>$name,
                'id' => $model->vehicle_id,
                'distance'=>$model->distance,
                'type'=>$model->type,
                'vehicle_price_id'=>$model->vehicle_price_id,
                'unit'=>$model->unit,
                'description'=>$model->description,
                'price_group'=>$vp

            ];
        }
        $data = [
            'Transport' => $d,
        ];
        return $data;
    }

    public function getExtraData($type) {
        $d = [];
        $models = $this->getExtraItem($type);

        /* @var $model \backend\modules\offers\models\OfferExtraItem */
        foreach ($models as $model) {
            $d[uniqid()] = [
                'value' => $model->price * $model->quantity * $model->duration,
                'price' => $model->price,
                'quantity' => $model->quantity,
                'name' => $model->name,
                'id' => $model->id,
                'distance' => $model->duration,
            ];
        }
        $index = null;
        if ($type == OfferExtraItem::TYPE_VEHICLE) {
            $index = 'Transport';
        }
        if ($type == OfferExtraItem::TYPE_CREW) {
            $index = 'Obsługa';
        }
        $data = [
            $index => $d,
        ];
        return $data;
    }

    public function getExtraGearData() {
        $data = [];
        $models = $this->getExtraItem(OfferExtraItem::TYPE_GEAR);

        /* @var $model \backend\modules\offers\models\OfferExtraItem */
        foreach ($models as $model) {
            $data[GearCategory::findOne($model->category_id)->name][uniqid()] = [
                'type' => 'gear',
                'id' => $model->id,
                'name' => $model->name,
                'quantity' => $model->quantity,
                'discount' => $model->discount,
                'duration' => $model->duration,
                'price' => $model->price,
                'value' => $model->price * ($model->duration-1) * $model->quantity * (1 - $model->discount/100)*$model->first_day_percent/100+ $model->quantity * (1 - $model->discount/100)*$model->price,
            ];
        }
        return $data;
    }

    public function getRolesData() {
        $d = [];
        $models = $this->getOfferRoles()->innerJoinWith('role')->all();
        foreach ($models as $model)
        {
            $d[$model->role_id] = [
                'name'=> $model->role->name,
                'value' => $model->getValue(),
            ];
        }

        $data = [
            'Obsługa' => $d,
        ];
        return $data;
    }

    public function getRolesData2() {
        $d = [];
        $models = $this->getOfferRoles()->innerJoinWith('role')->all();
        foreach ($models as $model)
        {
            $d[$model->role_id."_".$model->time_type] = [
                'name'=> $model->role->name,
                'value' => $model->getValue(),
                'time'=>$model->time_type
            ];
        }

        $data = [
            'Obsługa' => $d,
        ];
        return $data;
    }

    public function getOfferData()
    {
        //TODO: singletony/cache
        $dataGear = $this->getGearData();
        $dateOuterGear = $this->getOuterGearData();
        $dataVehicle = $this->getVehicleData();
        $dataCustom  = $this->getCustomData();
        $dataRoles = $this->getRolesData2();
        $dataExtraGear = $this->getExtraGearData();
        $dataExtraVehicle = $this->getExtraData(OfferExtraItem::TYPE_VEHICLE);
        $dataExtraRoles = $this->getExtraData(OfferExtraItem::TYPE_CREW);
        $data = ArrayHelper::merge($dataGear,$dateOuterGear,$dataVehicle, $dataCustom, $dataRoles, $dataExtraVehicle, $dataExtraRoles, $dataExtraGear);
        return $data;
    }

    public function getSummary()
    {
        $data = $this->getOfferData();
        $summary = [];

        foreach ($data as $cat=>$d)
        {
            $summary[$cat] = array_sum(ArrayHelper::getColumn($d,'value'));
        }
        $summary['Suma'] = array_sum($summary);
        $brutto = $summary['Suma'] * 1.23;
        $summary['Brutto'] = round($brutto,2);
        $summary = Event::sortSummary($summary);

        return $summary;
    }

    public function removeFromEvent(){
        $this->event_id = null;
        $this->save();

        return OfferGearItem::deleteAll(['offer_id' => $this->id]);
    }

    public function removeFromProject(){
        $this->project_id = null;
        $this->save();

    }



    public function getLocationLabel()
    {
        $label = '-';
        if ($this->location !== null)
        {
            $label = $this->location->getDisplayLabel();
        }

        return $label;
    }

    public function prepareDateAttributes()
    {
        $this->behaviors['eventDatesBehavior']->prepareDateAttributes();
        $offerDate = \DateTime::createFromFormat('Y-m-d', $this->offer_date);
        if ($offerDate !== false)
        {
            $this->offer_date = $offerDate->format('d/m/Y');
        }
    }

    public function beforeSave($insert)
    {
        $offerDate = \DateTime::createFromFormat('d/m/Y', $this->offer_date);
        if ($offerDate != false)
        {
            $this->offer_date = $offerDate->format('Y-m-d');
        }
        if ($insert)
        {
            $setting = Settings::find()->where(['key'=>'orderRules'])->one();
            $this->created_by = Yii::$app->user->id;
            if ($setting){
                if (!$this->order_rules)
                    $this->order_rules = $setting->value;
            }
                    
        }
        return parent::beforeSave($insert);
    }


    public function getDistance(){
    // Google API key
        $from = Yii::$app->settings->get('main.warehouseCity')." ".Yii::$app->settings->get('main.warehouseAddress');
        if (isset($this->location_id))
        {
                $to =  $this->location->city.', '.$this->location->address;
        }else{
            $to = $this->address;
        }
        $from = urlencode($from);
        $to = urlencode($to);
        $apiKey= "AIzaSyAPDBOEfgjSaEHEiC8Zx3BpV5lT_cIRiBQ";  
        $data = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$from."&destinations=".
            $to."&key=".$apiKey."&language=en-EN&sensor=false");
        
        $data = json_decode($data, true);
        if ($data['rows'])
        {
            $distance = $data['rows'][0]['elements'][0]['distance']['value'];
        }else{
            $distance = 0;
        }
        $time = 0;
        
        /*
        foreach($data->rows[0]->elements as $road) {
            $time += $road->duration->value;
            $distance += $road->distance->value;
        }*/
        return floor($distance/1000);
    }

    public function getManagerDisplayLabel()
    {
        $label = '';
        if($this->manager !== null)
        {
            $label = $this->manager->getDisplayLabel();
        }
        return $label;
    }

    public function getAssignedOuterGearModelNumber($offer_id, $outer_gear_id, $type=null, $item=null) {

        if (!$type)
            $model = OfferOuterGear::findOne(['offer_id' => $offer_id, 'outer_gear_model_id' => $outer_gear_id, 'type'=>1 ]);
        else
        {
            if ($type=='gear')
            {
                $model = OfferOuterGear::findOne(['offer_id' => $offer_id, 'outer_gear_model_id' => $outer_gear_id, 'type'=>2, 'offer_gear_id'=>$item ]);
            }
            if ($type=='outerGear')
            {
                $model = OfferOuterGear::findOne(['offer_id' => $offer_id, 'outer_gear_model_id' => $outer_gear_id, 'type'=>2, 'offer_outer_gear_id'=>$item ]);
            }
            if ($type=='extraGear')
            {
                $model = OfferOuterGear::findOne(['offer_id' => $offer_id, 'outer_gear_model_id' => $outer_gear_id, 'type'=>2, 'offer_group_id'=>$item ]);
            }
        }
        if ($model == null) {
            return 0;
        }
        if ($model->quantity == null) {
            return 1;
        }
        return $model->quantity;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->event !== null)
        {
            $this->event->updateStatutes();
            $this->event->updateIvoiceIssued();
            $this->event->save();
        }
        if ($insert)
        {
                $customer = Customer::findOne($this->customer_id);   
                $this->copyProvisionGroups();  
                $customer->createLog('offer_create', $this->id); 
                if ($this->event_id)
                {
                    Note::createNote(2, 'eventOfferAdded', $this, $this->event_id);
                }else{
                    Note::createNote(3, 'offerAdded', $this, $this->id);
                }
                OfferLog::addLog('offer_created', $this, $this->id); 
                
        }
         if (isset($changedAttributes['status']) && $this->status != $changedAttributes['status'])
         {
                $customer = Customer::findOne($this->customer_id);     
                $customer->createLog('offer_update', $this->id);
                if ($this->event_id)
                {
                    Note::createNote(2, 'eventOfferChanged', $this, $this->event_id);
                }else{
                    Note::createNote(3, 'offerChanged', $this, $this->id);
                }  
                OfferLog::addLog('offer_status', $this, $this->id);   
                //tutaj wysyłanie powiadomień
                $this->sendStatusReminder();                      
         }
         if (isset($changedAttributes['event_id']) && $this->status != $changedAttributes['event_id'])
         {
            if ($this->event_id)
            {
                Note::createNote(2, 'eventOfferAdded', $this, $this->event_id);
                OfferLog::addLog('offer_event', $this, $this->id);
            }
            
         }
         if (isset($changedAttributes['rent_id']) && $this->status != $changedAttributes['rent_id'])
         {
            OfferLog::addLog('offer_rent', $this, $this->id);
         }
         if ((isset($changedAttributes['event_start']) && $this->event_start != $changedAttributes['event_start'])||(isset($changedAttributes['event_end']) && $this->event_end != $changedAttributes['event_end'])||(isset($changedAttributes['packing_start']) && $this->packing_start != $changedAttributes['packing_start'])||(isset($changedAttributes['packing_end']) && $this->packing_end != $changedAttributes['packing_end'])||(isset($changedAttributes['montage_start']) && $this->montage_start != $changedAttributes['montage_start'])||(isset($changedAttributes['montage_end']) && $this->montage_end != $changedAttributes['montage_end'])||(isset($changedAttributes['disassembly_start']) && $this->disassembly_start != $changedAttributes['disassembly_start'])||(isset($changedAttributes['disassembly_end']) && $this->disassembly_end != $changedAttributes['disassembly_end']))
         {
            OfferLog::addLog('offer_time', $this, $this->id);
         }


        parent::afterSave($insert, $changedAttributes);
    }

    public function copyProvisionGroups()
    {
        //usuwamy dotychczasowe
        OfferProvisionGroup::deleteAll(['offer_id'=>$this->id]);
        $provisions = ProvisionGroup::find()->all();
        foreach ($provisions as $p)
        {
            $opg = new OfferProvisionGroup();
            $opg->attributes = $p->attributes;
            $opg->offer_id = $this->id;
            $opg->save();
            foreach ($p->provisionGroupProvisions as $pgp)
            {
                $opgp = new OfferProvisionGroupProvision();
                $opgp->attributes = $pgp->attributes;
                $opgp->offer_provision_group_id = $opg->id;
                $opgp->save();
            }
        }
    }

    public function sendStatusReminder()
    {
        $text = Yii::t('app', 'Zmieniono status oferty ').$this->name.Yii::t('app', ' na ').$this->offerStatut->name;
        $user_ids = explode(";",$this->offerStatut->reminder_users);
        $userIds2 = [];
        $team_ids = explode(";",$this->offerStatut->reminder_groups);
        if ($team_ids)
        {
                $userIds2 = ArrayHelper::map(TeamUser::find()->where(['IN', 'team_id', $team_ids])->asArray()->all(), 'user_id', 'user_id');
        }
        
        $userIds = array_merge($user_ids, $userIds2);
        if ($this->offerStatut->reminder_pm)
        {
            $pm = [$this->manager_id];
            $userIds = array_merge($pm, $userIds);
        }
        $users = User::find()->where(['IN', 'id', $userIds])->all();
        foreach ($users as $user)
        {
            if ($this->offerStatut->reminder_sms)
            {
                Notification::sendUserSmsNotification($user, $text, date("Y-m-d H:i:s"));
            }
            if ($this->offerStatut->reminder_mail)
            {
                Notification::sendUserMailNotification($user, Yii::t('app', 'Wiadomość automatyczna'), $text." ".Html::a(Yii::t('app', 'Zobacz'), "http://".Yii::$app->getRequest()->serverName.'/admin/offer/default/view?id='.$this->id ));
            }            
        }

    }

    public function getExtraItem($type = null) {
        if ($type == null) {
            return OfferExtraItem::find()->where(['offer_id' => $this->id])->indexBy('id')->all();
        }
        return OfferExtraItem::find()->where(['offer_id' => $this->id])->andWhere(['type' => $type])->indexBy('id')->all();
    }

    public function getORoles($type)
    {
        return OfferRole::find()->where(['offer_id'=>$this->id])->andWhere(['time_type'=>$type])->all();
    }

    public function getAssignedLogs()
    {
        $query = $this->getLogs();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination' => false
        ]);

        return $dataProvider;       
    }

    public function countValues()
    {
        $cost = 0;
        $costs = ['Obsługa'=>0, 'Transport'=>0, 'Inne'=>0];
        OfferCost::deleteAll(['offer_id'=>$this->id]);
        OfferValue::deleteAll(['offer_id'=>$this->id]);
        foreach ($this->offerOuterGears as $gear){
            $price = $gear->outerGearModel->getPrice()*$gear->quantity+$gear->outerGearModel->getPrice()*$gear->quantity*($gear->duration-1)*$gear->first_day_percent/100;
            $section = $gear->outerGearModel->category->getMainCategory();
            if (!isset($costs[$section->name]))
            {
                $costs[$section->name]=0;
            }
            $costs[$section->name]+=$price;
            $cost+=$price;
        }
        foreach ($this->offerGears as $gear){
            if ($gear->gear->type==3){
                    $price = $gear->gear->value*$gear->quantity;
                    $cost+=$price;
                    $section = $gear->gear->category->getMainCategory();
                                if (!isset($costs[$section->name]))
                                {
                                    $costs[$section->name]=0;
                                }
                                $costs[$section->name]+=$price;
            }
        }
        foreach ($this->offerVehicles as $car){
                        $value = $car->cost;
                        $distance = $car->distance;
                        $quantity = $car->quantity;
                        $price = $value*$distance*$quantity;
                    if ($this->priceGroup->currency!=Yii::$app->settings->get('defaultCurrency', 'main'))
                    {
                        $price=$price*$this->exchange_rate;
                    }
                    

                    $costs['Transport']+=$price;
                    $cost+=$price;
                }
        foreach ($this->extraItems as $gear){
                    $price = $gear->cost*$gear->quantity;
                    if ($this->priceGroup->currency!=Yii::$app->settings->get('defaultCurrency', 'main'))
                    {
                        $price=$price*$this->exchange_rate;
                    }
                    $cost+=$price;
                    if (isset($gear->category_id))
                    {
                        $section = $gear->category->getMainCategory();
                        if (!isset($costs[$section->name]))
                            {
                                $costs[$section->name]=0;
                            }
                        
                        $costs[$section->name]+=$price;
                    }else{
                        if ($gear->type==OfferExtraItem::TYPE_CREW)
                        {
                            $costs['Obsługa']+=$price;
                        }
                        if ($gear->type==OfferExtraItem::TYPE_VEHICLE)
                        {
                            $costs['Transport']+=$price;
                        }
                        if ($gear->type==1)
                        {
                            $costs['Inne']+=$price;
                        }
                        
                    }
                    
                }
        foreach ($this->offerRoles as $role){
                    if ($role->type==1)
                    {
                        if ($role->salary_type==1)
                        {
                            $price = $role->duration*$role->quantity*$role->cost;
                            $duration = $role->duration." ".Yii::t('app', 'dni');
                        }else{
                            $duration =$this->getPeriodTime($role->time_type);
                            $price = $duration*$role->quantity*$role->cost_hour;
                            $duration = $duration." ".Yii::t('app', 'godzin');
                        }
                    }else{
                            
                        if ($role->salary_type==1)
                        {
                            $duration = ceil($role->duration/12);
                            $price = $duration*$role->quantity*$role->cost;
                            $duration = $duration." ".Yii::t('app', 'dni');
                        }else{
                            $price = $role->duration*$role->quantity*$role->cost_hour;
                            $duration = $role->duration." ".Yii::t('app', 'godzin');
                        }
                    }
                    if ($this->priceGroup->currency!=Yii::$app->settings->get('defaultCurrency', 'main'))
                    {
                        $price=$price*$this->exchange_rate;
                    }else{
                        $price=$price;
                    }
                    $cost+=$price;
                    $costs['Obsługa']+=$price;
                }
        foreach ($this->offerExtraCosts as $c)
        {
            $price = $c->quantity*$c->cost;
            if ($c->section)
            {
                if (!isset($costs[$c->section]))
                {
                $costs[$c->section]=0;
                }
                $costs[$c->section]+=$price;
            }else{
                $costs['Inne'] += $price;
            }
            
            $cost+=$price;
        }
        foreach ($this->offerCustomItems as $c)
        {
            $price = $c->diff_count*$c->cost;
            $costs['Inne'] += $price;   
            $cost+=$price;
        }
        $this->cost = $cost;
        $costs[Yii::t('app', 'Suma')]=$cost;
        foreach ($costs as $k => $c)
        {
            $oc = new OfferCost();
            $oc->offer_id = $this->id;
            $oc->section = $k;
            $oc->value = $c;
            $oc->save();
        }

        $summ = 0;
        $sums  = ['Obsługa'=>0, 'Transport'=>0, 'Inne'=>0];
        foreach ($this->offerRoles as $role){
                    $price = $role->duration*$role->quantity*$role->price;
                    $summ+=$price;
                    $sums['Obsługa']+=$price;
                } 
        foreach ($this->offerVehicles as $car){
                    if ($car->price_type==1)
                    {
                        $value = $car->price;
                        $distance = $car->distance;
                        $price = $car->quantity*$value*$distance;
                    }else{
                        $value = $car->price;
                        $price = $car->quantity*$value;
                    }
                    $summ+=$price;
                    $sums['Transport']+=$price;
                }
        foreach ($this->offerOuterGears as $gear){
            $price = $gear->getValue();
            $summ+=$price;
            $section = $gear->outerGearModel->category->getMainCategory();
                                if (!isset($sums[$section->name]))
                                {
                                    $sums[$section->name]=0;
                                }
                                $sums[$section->name]+=$price;
        }
        foreach ($this->offerGears as $gear){
            $price = $gear->getValue();
            $summ+=$price;
            $section = $gear->gear->category->getMainCategory();
                                if (!isset($sums[$section->name]))
                                {
                                    $sums[$section->name]=0;
                                }
                                $sums[$section->name]+=$price;
        }
        foreach ($this->extraItems as $gear){
            $price = $gear->getValue();
            $summ+=$price;
            if ($gear->type == OfferExtraItem::TYPE_VEHICLE)
            {
                $sums['Transport']+=$price;
            }else{
                if ($gear->type == OfferExtraItem::TYPE_CREW)
                {
                    $sums['Obsługa']+=$price;
                }else{
                    $section = GearCategory::findOne($gear->category_id);
                                if (!isset($sums[$section->name]))
                                {
                                    $sums[$section->name]=0;
                                }
                                $sums[$section->name]+=$price;
                }                
            }
        }
        foreach ($this->offerCustomItems as $gear){
            $price = $gear->price*$gear->diff_count*(1-$gear->discount/100);
            $summ+=$price;
            $sums['Inne']+=$price;
        }
        $this->value = $summ;
        $sums[Yii::t('app', 'Suma')]=$summ;
        $recalculate = false;
        if (isset($this->budget))
        {
            if ($this->budget<$summ)
            {
                    $price = $this->budget-$sums['Transport']-$sums['Obsługa'];
                    $price2 = $summ-$sums['Transport']-$sums['Obsługa'];
                    if ($price2>0)
                    {
                        $recalculate_val = $price/$price2;
                        $recalculate = true;
                    }
                    $sums[Yii::t('app', 'Suma')]=$this->budget;
                    //$this->value = $this->budget;
            }
        }
        foreach ($sums as $k => $c)
        {
            $oc = new OfferValue();
            $oc->offer_id = $this->id;
            $oc->section = $k;
            if ($recalculate)
            {
                    if (($k!='Transport')&&($k!=Yii::t('app', 'Suma'))&&($k!='Obsługa'))
                    {
                           $oc->value = $c*$recalculate_val; 
                    }else{
                        $oc->value = $c;
                    }
            }else{
                $oc->value = $c;
            }
            
            $oc->save();
        }
       

        $this->save();
    }

    public function getScheduleList()
    {
        $list = [];
        foreach ($this->offerSchedules as $schedule)
        {
            $list[$schedule->id] = $schedule->name;
        }
        return $list;
    }

    public function getPeriodTime($type)
    {
        $schedule = OfferSchedule::findOne($type);
        if (($type)&&($schedule))
                return $schedule->getPeriodTime();
            return 0;
        if ($type==1)
        {
            $time1 = strtotime($this->packing_start);
            $time2 = strtotime($this->packing_end);
            if (!$time1)
                return $this->packing_length;
        }
        if ($type==2)
        {
            $time1 = strtotime($this->montage_start);
            $time2 = strtotime($this->montage_end);
            if (!$time1)
                return $this->montage_length;
        }
        if ($type==3)
        {
            $time1 = strtotime($this->event_start);
            $time2 = strtotime($this->event_end);
            if (!$time1)
                return $this->event_length;
        }
        if ($type==4)
        {
            $time1 = strtotime($this->disassembly_start);
            $time2 = strtotime($this->disassembly_end);
            if (!$time1)
                return $this->disassembly_length;
        }
            $difference = ceil(abs($time2 - $time1) / 3600);
            return $difference;
    }

    public function getOfferValues()
    {
        $values = OfferValue::find()->where(['offer_id'=>$this->id])->all();
        $return = [];
        $return[Yii::t('app', 'Suma')] = 0;
        $return['Inne'] = 0;
        $return['Transport'] = 0;
        $return['Obsługa'] = 0;
        foreach ($values as $val)
        {
            $return[$val->section] = $val->value;
        }
        return $return;
    }
    public function getOfferCosts()
    {
        $values = OfferCost::find()->where(['offer_id'=>$this->id])->all();
        $return = [];
        $return[Yii::t('app', 'Suma')] = 0;
        $return['Inne'] = 0;
        $return['Transport'] = 0;
        $return['Obsługa'] = 0;
        foreach ($values as $val)
        {
            $return[$val->section] = $val->value;
        }
        return $return;
    }
    public function getOfferProfits()
    {
        $values = $this->getOfferValues();
        if ($this->priceGroup->currency!=Yii::$app->settings->get('defaultCurrency', 'main'))
        {
            foreach ($values as $key=>$val)
            {
                $values[$key] = $values[$key]*$this->exchange_rate;
            }
        }
        $values2 = $this->getOfferCosts();
        foreach ($values2 as $key=>$val)
        {
            if (!isset($values[$key]))
                $values[$key] = 0;
            $values[$key] = $values[$key]-$values2[$key];
        }
        return $values;
    }

    public function getPMCost()
    {
        if (($this->pm_cost)&&($this->pm_cost>0))
        {
            return $this->pm_cost;
        }else{

            if ($this->pm_cost_percent)
            {
                
                if (isset($this->getOfferValues()[Yii::t('app', 'Suma')]))
                {
                    return $this->getOfferValues()[Yii::t('app', 'Suma')]*$this->pm_cost_percent/100;
                }else{
                    return 0;
                }
                
            }else{
                return 0;
            }
        }
    }

    public function getGearValue()
    {
        $values = $this->getOfferValues();
        $summ = $this->value-$values['Obsługa']-$values['Transport'];
        return $summ;
    }

    public function saveOfferSend($recipients, $filename)
    {
        $os = new OfferSend();
        $os->offer_id = $this->id;
        $os->datetime = date('Y-m-d H:i:s');
        $os->recipient = implode(",", $recipients);
        $os->filename = $filename;
        $os->user_id = Yii::$app->user->id;
        $os->save();
    }

    public function getVehicleType()
    {
        if (count($this->offerVehicles)>0)
        {
            $v = $this->offerVehicles[0];
            return $v->price_type;
        }else{
            return 1;
        }
    }

    public function getWorkersCount($type)
    {
        $ors = OfferRole::find()->where(['offer_id'=>$this->id])->andWhere(['time_type'=>$type])->all();
        $sum = 0;
        foreach ($ors as $or)
        {
            $sum+=$or->quantity;
        }
        return $sum;
    }

    public function getGearSize()
    {
        $gears = [];
        foreach ($this->offerGears as $og)
        {
            if (!isset($gears[$og->gear_id]))
            {
                $gears[$og->gear_id]['gear'] = $og->gear;
                $gears[$og->gear_id]['quantity'] = $og->quantity;
            }else{
                $gears[$og->gear_id]['quantity'] += $og->quantity;
            }
        }
        $totalVolume = 0;
        $totalWeight = 0;
        foreach ($gears as $g)
        {
            if ($g['gear']->no_items)
            {
                $totalWeight  +=$g['quantity']*$g['gear']->weight;
            }else{
                $totalWeight  +=$g['quantity']*$g['gear']->weight;
                $totalWeight  +=$g['gear']->getWeightCase($g['quantity']);
            }

            $totalVolume += $g['gear']->countVolume2($g['quantity']);
        }
        foreach ($this->extraItems as $eg) {
            $totalWeight  += $eg->weight;
            $totalVolume +=$eg->volume;
        }
        foreach ($this->offerOuterGears as $eg) {
            $sum += $eg->quantity*$eg->outerGearModel->weight;
             $totalVolume +=$eg->outerGearModel->countVolume() * $eg->quantity;
        }
        return ['weight'=>$totalWeight, 'volume'=>$totalVolume];
        
    }

    public function getGearPrices()
    {
        $ids = ArrayHelper::map(OfferGear::find()->where(['offer_id'=>$this->id])->asArray()->all(), 'gear_id', 'gear_id');
        $prices = GearPrice::find()->where(['gear_id'=>$ids])->all();
        $price_array = [];
        foreach ($prices as $price)
        {
            $price_array[$price->gears_price_id][$price->gear_id] = $price->price;
        }
        return $price_array;
    }

    public function getEndValue()
    {
        if ((isset($this->budget))&&($this->value>$this->budget))
        {
            return $this->budget;
        }else{
            return $this->value;
        }
    }

    public function getVatValue()
    {
        $vat = 0;
        $recalculate = false;
        $sums = $this->getOfferValues();
        if (isset($this->budget))
        {
            if ($this->budget<$this->value)
            {
                    $price = $this->budget-$sums['Transport']-$sums['Obsługa'];
                    $price2 = $this->value-$sums['Transport']-$sums['Obsługa'];
                    if ($price2>0)
                    {
                        $recalculate_val = $price/$price2;
                        $recalculate = true;
                    }else{
                        $recalculate_val = 0;
                        $recalculate = true;
                    }
                    if ($price<0)
                    {
                        $recalculate_val = 0;
                        $recalculate = true;
                    }

            }
        }
        foreach ($this->offerGears as $og)
        {
            $netto = $og->getValue();
            if ($recalculate)
                $netto = $netto*$recalculate_val;
            $vat += $netto*$og->vat_rate/100;
        }
        foreach ($this->offerOuterGears as $og)
        {
            $netto = $og->getValue();
            if ($recalculate)
                $netto = $netto*$recalculate_val;
            $vat += $netto*$og->vat_rate/100;
        }
                foreach ($this->extraItems as $og)
        {
            if (($og->type==1)||($og->type==4))
            {
                $netto = $og->getValue();
                if ($recalculate)
                $netto = $netto*$recalculate_val;
                $vat += $netto*$og->vat_rate/100;
            }
            
        }
        foreach ($this->offerCustomItems as $og)
        {
            $netto = $og->getValue();
            if ($recalculate)
                $netto = $netto*$recalculate_val;
            $vat += $netto*23/100;
        }
        $recalculate2 = 1;
        if (isset($this->budget))
        {
            if ($this->budget<$this->value)
            {
                $price = $this->budget-$sums['Transport']-$sums['Obsługa'];
                if ($price<0)
                {
                    $recalculate2 = $this->budget/($sums['Transport']+$sums['Obsługa']);

                }
            }
            
        }
                        foreach ($this->offerRoles as $og)
                        {
                            $netto = $og->getValue();
                            $vat += $netto*$og->vat_rate/100*$recalculate2;
                        }
                        foreach ($this->offerVehicles as $og)
                        {
                            $netto = $og->getValue();
                            $vat += $netto*$og->vat_rate/100*$recalculate2;
                        }
                        foreach ($this->extraItems as $og)
                        {
                                if (($og->type==2)||($og->type==3))
                                {
                                    $netto = $og->getValue();
                                    $netto = $netto*$recalculate2;
                                    $vat += $netto*$og->vat_rate/100;
                                }
                                
                            }
        return $vat;
            
    }

    public function getTotalVolumeAndWeight()
    {
        $gears = [];
        foreach ($this->offerGears as $og)
        {
            if (!isset($gears[$og->gear_id]))
            {
                $gears[$og->gear_id]['gear'] = $og->gear;
                $gears[$og->gear_id]['quantity'] = $og->quantity;
            }else{
                $gears[$og->gear_id]['quantity'] += $og->quantity;
            }
        }
        $totalVolume = 0;
        $totalWeight = 0;
        $totalPower = 0;
        foreach ($gears as $g)
        {
            if ($g['gear']->no_items)
            {
                $totalWeight  +=$g['quantity']*$g['gear']->weight;
            }else{

                $totalWeight  +=$g['quantity']*$g['gear']->weight;
                $totalWeight  +=$g['gear']->getWeightCase($g['quantity']);
            }
            $totalPower +=$g['gear']->power_consumption*$g['quantity'];

            $totalVolume += $g['gear']->countVolume2($g['quantity']);
        }
        foreach ($this->extraItems as $eg) {
            $totalWeight  += $eg->weight;
            $totalVolume +=$eg->volume;
        }
        foreach ($this->offerOuterGears as $eg) {
            $totalWeight += $eg->quantity*$eg->outerGearModel->weight;
             $totalVolume +=$eg->outerGearModel->countVolume() * $eg->quantity;
        }
        return ['weight'=>$totalWeight, 'volume'=>$totalVolume, 'power'=>$totalPower];
    }

    public function getProvisionsSumButton()
    {
        $all = $this->getGProvisions();
        $title = "";
        $total = 0;
        foreach ($all as $g)
        {
            $title .=$g['group']->name." ".Yii::$app->formatter->asCurrency($g['value'])." \n";
            $total +=$g['value'];
        }
        return ['b'=>'<span class="label label-warning" title="'.$title.'">'.Yii::$app->formatter->asCurrency($total).'</span>', 'sum'=>$total];
    }

    public function getProvisionPM($profits)
    {
        
        $sum_prov = 0;
        if (isset($this->manager_id))
        {
                    $values = $this->getOfferValues();
                    $provisions = UserProvision::find()->where(['user_id'=>$this->manager_id])->all();
                    foreach ($provisions as $p)
                    {
                            
                                            if ($p->type==1){
                                                    if (isset($profits[$p->section]))
                                                        $val= $p->value/100*$profits[$p->section];
                                                    else
                                                        $val = 0;
                                                }else{
                                                    if (isset($values[$p->section]))
                                                        $val= $p->value/100*$values[$p->section];
                                                    else
                                                        $val = 0;
                                                }  
                                            $sum_prov +=$val;

                            
                                       
                    }
                    
        }
        return ['value'=>$sum_prov];
    }

    public function getGProvisions()
    {
        $groups = OfferProvisionGroup::find()->where(['offer_id'=>$this->id])->orderBy(['level'=>SORT_ASC])->all();
        $values = $this->getOfferValues();
        if (!$groups)
        {
            $this->copyProvisionGroups();
            $groups = OfferProvisionGroup::find()->where(['offer_id'=>$this->id])->orderBy(['level'=>SORT_ASC])->all();
        }
        if ($this->priceGroup->currency!=Yii::$app->settings->get('defaultCurrency', 'main'))
        {
            foreach ($values as $key=>$val)
            {
                $values[$key] = $values[$key]*$this->exchange_rate;
            }
        }
        $profits = $this->getOfferProfits();
        $costs = $this->getOfferCosts();
        //exit;
        $level = 0;
        $total_level = [];
        foreach ($profits as $key => $val)
        {
            $total_level[$key] = 0;
        }
        $provisions = [];
        foreach ($groups as $group)
        {

            if ((!$group->customer_group_id)||($this->customer->isInGroup($group->customer_group_id))){
            if ($group->level!=$level)
            {
                $level = $group->level;
                foreach ($profits as $key => $val)
                {
                    $profits[$key] = $val-$total_level[$key];
                    $total_level[$key] = 0;
                }
            }
            if ($group->is_pm)
            {
                $provisions[$group->id] = $this->getProvisionPM($profits);
                $provisions[$group->id]['group'] = $group;
                foreach ($profits as $k => $val)
                {
                    $total_level[$key] += $provisions[$group->id]['value'][$k];
                }
            }else{
            if (!$group->main_only)
            {
                //prowizja jednakowa dla wszystkich sekcji
                if ($group->type==1)
                {
                    //prowizja od zysku
                    $value = $profits[Yii::t('app', 'Suma')]*$group->provision/100;
                    foreach ($profits as $key => $val)
                    {
                        $total_level[$key] += $profits[$key]*$group->provision/100;
                    }
                }
                if ($group->type==2){
                    //prowizja od wartości
                    $value = $values[Yii::t('app', 'Suma')]*$group->provision/100;
                    foreach ($profits as $key => $val)
                    {
                        $total_level[$key] += $values[$key]*$group->provision/100;
                    }
                }
                if ($group->type==3){
                    //prowizja od wartości
                    $value = $costs[Yii::t('app', 'Suma')]*$group->provision/100;
                    foreach ($costs as $key => $val)
                    {
                        $total_level[$key] += $values[$key]*$group->provision/100;
                    }
                }
            }else{
                //inna prowizja dla niektórych sekcji
                if ($group->type==1)
                {
                    //prowizja od zysku
                    $value = 0;
                    foreach ($profits as $key => $val)
                    {
                        //szukamy czy jest nietypowy procent
                        if ($key!=Yii::t('app', 'Suma')){
                            $pgs = OfferProvisionGroupProvision::find()->where(['section'=>$key])->andWhere(['offer_provision_group_id'=>$group->id])->one();
                            if ($pgs)
                            {
                                if ($pgs->type==1)
                                {
                                    $value += $profits[$key]*$pgs->value/100;
                                    $total_level[$key] += $profits[$key]*$pgs->value/100;
                                }
                                if ($pgs->type==2){
                                    $value += $values[$key]*$pgs->value/100;
                                    $total_level[$key] += $values[$key]*$pgs->value/100;
                                }
                                if ($pgs->type==3){
                                    $value += $costs[$key]*$pgs->value/100;
                                    $total_level[$key] += $costs[$key]*$pgs->value/100;
                                }
                            }else{
                                $value += $profits[$key]*$group->provision/100;
                                $total_level[$key] += $profits[$key]*$group->provision/100;
                            }
                        }

                        
                    }
                }
                if ($group->type==2){
                    //prowizja od zysku
                    $value = 0;
                    foreach ($profits as $key => $val)
                    {
                        //szukamy czy jest nietypowy procent
                        if ($key!=Yii::t('app', 'Suma')){
                            $pgs = OfferProvisionGroupProvision::find()->where(['section'=>$key])->andWhere(['offer_provision_group_id'=>$group->id])->one();
                            if ($pgs)
                            {
                                if ($pgs->type==1)
                                {
                                    $value += $profits[$key]*$pgs->value/100;
                                    $total_level[$key] += $profits[$key]*$pgs->value/100;
                                }
                                if ($pgs->type==2){
                                    $value += $values[$key]*$pgs->value/100;
                                    $total_level[$key] += $values[$key]*$pgs->value/100;
                                }
                                if ($pgs->type==3){
                                    $value += $costs[$key]*$pgs->value/100;
                                    $total_level[$key] += $costs[$key]*$pgs->value/100;
                                }
                            }else{
                                $value += $values[$key]*$group->provision/100;
                                $total_level[$key] += $values[$key]*$group->provision/100;
                            }
                        }

                        
                    }                    
                }
                if ($group->type==3){
                    //prowizja od kosztów
                    $value = 0;
                    foreach ($profits as $key => $val)
                    {
                        //szukamy czy jest nietypowy procent
                        if ($key!=Yii::t('app', 'Suma')){
                            $pgs = OfferProvisionGroupProvision::find()->where(['section'=>$key])->andWhere(['offer_provision_group_id'=>$group->id])->one();
                            if ($pgs)
                            {
                                if ($pgs->type==1)
                                {
                                    $value += $profits[$key]*$pgs->value/100;
                                    $total_level[$key] += $profits[$key]*$pgs->value/100;
                                }
                                if ($pgs->type==2){
                                    $value += $values[$key]*$pgs->value/100;
                                    $total_level[$key] += $values[$key]*$pgs->value/100;
                                }
                                if ($pgs->type==3){
                                    $value += $costs[$key]*$pgs->value/100;
                                    $total_level[$key] += $costs[$key]*$pgs->value/100;
                                }
                            }else{
                                $value += $costs[$key]*$group->provision/100;
                                $total_level[$key] += $costs[$key]*$group->provision/100;
                            }
                        }

                        
                    }                    
                }
                $total_level[Yii::t('app', 'Suma')] +=$value;

            }
            $provisions[$group->id]['group'] = $group;
            $provisions[$group->id]['value'] = $value;
        }
        }
        }
        return $provisions;
    }

    public function getTotalProductionBudget()
    {
        $items = OfferExtraItem::find()->where(['offer_id'=>$this->id])->andWhere(['type'=>4])->all();
        $sum = 0;
        foreach ($items as $item)
        {
            $sum +=$item->getValue();
        }
        return $sum;
    }

    public function getOVehicle($type)
    {
        return OfferVehicle::find()->where(['offer_id'=>$this->id])->andWhere(['type'=>$type])->all();
    }

    public function saveSchedule($type, $schedules)
    {
        //return true;
        $models = \common\models\Schedule::find()->where(['schedule_type_id'=>$type])->orderBy(['position'=>SORT_ASC])->all();
        foreach ($models as $m)
        {
            $model = new OfferSchedule();
            $model->attributes = $m->attributes;
            $model->offer_id = $this->id;
            if ($schedules[$m->id]['dateRange'])
            {
                $dates = explode(" - ", $schedules[$m->id]['dateRange']);
                $model->start_time = $dates[0];
                $model->end_time = $dates[1];
            }
            $model->save();


        }
    }

    public function updateSchedule()
    {
        $models = \common\models\OfferSchedule::find()->where(['offer_id'=>$this->id])->andWhere(['<>', 'start_time', ''])->orderBy(['start_time'=>SORT_ASC])->all();
        $first = true;
        $start = "";
        $end = "";
        foreach ($models as $m)
        {
            if ($first)
            {
                $start = $m->start_time;
                $end = $m->end_time;
                $first = false;
            }else{
                if ($m->start_time < $start)
                {
                    $start = $m->start_time;
                }
                if ($m->end_time > $end)
                {
                    $end = $m->end_time;
                }
            }
        }
        $this->event_start = $start;
        $this->event_end = $end;
        $this->save();
    }


}

<?php

namespace common\models;

use common\helpers\ArrayHelper;
use sadovojav\image\Thumbnail;
use Yii;
use \common\models\base\Gear as BaseGear;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use barcode\barcode\BarcodeGenerator;
use dosamigos\qrcode\QrCode;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "gear".
 */
class Gear extends BaseGear
{
    const TYPE_WITH_ITEMS = 1;
    const TYPE_NO_ITEMS = 0;

    const UPLOAD_DIR = 'gear';


    protected function _userAgent()
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        if (preg_match('/(android|iPhone|iPad|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
            return true;
        } else {
            return false;
        }
    }

    public function getOnEvents()
    {
        $ev = EventGearOutcomed::find()->where(['gear_id'=>$this->id])->all();
        $re = RentGearOutcomed::find()->where(['gear_id'=>$this->id])->all();
        $total = 0;
        foreach ($ev as $e) {
            $total+=$e->quantity;
        }
        foreach ($re as $e) {
            $total+=$e->quantity;
        }
        return $total;
    }
    public function recalculateWarehouses()
    {
        if ($this->no_items) {
            $wq = WarehouseQuantity::find()->where(['gear_id'=>$this->id])->all();
            if ($wq) {
                $sum = 0;
                foreach ($wq as $w) {
                    $sum +=$w->quantity;
                }
                $this->quantity = $sum;
                $this->save();
            }
        }else{
            $warehouses = Warehouse::find()->all();
            foreach ($warehouses as $w) {
                $wq = WarehouseQuantity::find()->where(['gear_id'=>$this->id, 'warehouse_id'=>$w->id])->one();
                if (!$wq) {
                    $wq = new WarehouseQuantity();
                    $wq->gear_id = $this->id;
                    $wq->warehouse_id = $w->id;
                }
                $gears = GearItem::find()->where(['warehouse_id'=>$w->id, 'gear_id'=>$this->id])->all();
                $wq->quantity = count($gears);
                if ($gears)
                    $wq->location = $gears[0]->location;
                $wq->save();
            }
        }
    }

    public function isFavorite()
    {
        if (GearFavorite::findOne(['gear_id' => $this->id, 'user_id' => Yii::$app->user->id])) {
            return true;
        }else{
            return false;
        }
    }

    public function getGearsPricesByGroup($group_id)
    {
        $ids = ArrayHelper::map(GearsPriceGroup::find()->where(['price_group_id'=>$group_id])->asArray()->all(), 'gears_price_id', 'gears_price_id');
        return GearsPrice::find()->where(['id'=>$ids])->andWhere(['type'=>3])->andWhere(['gear_id'=>$this->id])->all();
    }

    public function getDefaultPrice($offer_id)
    {
        $offer = Offer::findOne($offer_id);
        $default = GroupDefaultPrice::find()->where(['gear_id'=>$this->id])->andWhere(['price_group_id'=>$offer->price_group_id])->one();
        if ($default)
            $price = GearPrice::find()->where(['gear_id'=>$this->id])->andWhere(['gears_price_id'=>$default->gears_price_id])->one();
        else{
            $ids = ArrayHelper::map(GearsPriceGroup::find()->where(['price_group_id'=>$offer->price_group_id])->asArray()->all(), 'gears_price_id', 'gears_price_id');
            $price = GearPrice::find()->where(['gear_id'=>$this->id])->andWhere(['gears_price_id'=>$ids])->one();
        }
        return $price;

    }

    public function getPricesNames($id, $group_id)
    {
        if ($id) {
            $ids = ArrayHelper::map(GearsPriceGroup::find()->where(['price_group_id'=>$group_id])->asArray()->all(), 'gears_price_id', 'gears_price_id');
            $ids = ArrayHelper::map(GearPrice::find()->where(['gears_price_id'=>$ids])->andWhere(['gear_id'=>$id])->asArray()->all(), 'gears_price_id', 'gears_price_id');
            $return = ArrayHelper::map(GearsPrice::find()->where(['id'=>$ids])->asArray()->all(), 'id', 'name');
            $return2 = [null=>""];
            $return2 +=$return;
            return $return2;
        }else{
            $ids = ArrayHelper::map(GearsPriceGroup::find()->where(['price_group_id'=>$group_id])->asArray()->all(), 'gears_price_id', 'gears_price_id');
            $return = ArrayHelper::map(GearsPrice::find()->where(['id'=>$ids])->andWhere(['type'=>1])->asArray()->all(), 'id', 'name');
            $return2 = [null=>""];
            $return2 +=$return;
            return $return2;
        }
        
    }

    public function getExplPrice()
    {
        $p = GearPurchase::find()->where(['gear_id'=>$this->id])->orderBy(['datetime'=>SORT_DESC])->one();
        if ($p)
            return $p->price;
        else
            return 0;
    }

    public function getExplCompany()
    {
        $p = GearPurchase::find()->where(['gear_id'=>$this->id])->orderBy(['datetime'=>SORT_DESC])->one();
        if ($p)
            return $p->customer_id;
        else
            return null;
    }

    public function getPhotoUrl()
    {
        if ($this->photo == null) {
            return null;
        } else {
            return Yii::getAlias('@uploads/'.static::UPLOAD_DIR.'/'.$this->photo);
        }

    }
    public function getInService()
    {
        $serwisNumber = 0;
        if ($this->no_items)
            $serwisNumber = $this->getNoItemSerwis();
        else{
        foreach ($this->gearItems as $item) {
        if ($item->active == 1 && $item->status === GearItem::STATUS_SERVICE) {
                $serwisNumber++;
            }
        }
        }
        return $serwisNumber;        
    }

    public function getConflicts($start, $end)
    {
        if (!$start)
            $start = date("Y-m-d");
        if ($end)
            $ids = ArrayHelper::map(\common\models\Event::find()->andWhere(['>', 'event_start', $start])->andWhere(['<', 'event_start', $end])->asArray()->all(), 'id', 'id');
        else
            $ids = ArrayHelper::map(\common\models\Event::find()->andWhere(['>', 'event_start', $start])->asArray()->all(), 'id', 'id');
        $conflicts = \common\models\EventConflict::find()->where(['gear_id'=>$this->id])->andWhere(['resolved'=>0])->andWhere(['event_id'=>$ids])->count();
        return $conflicts;
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
        try {
            $thumb = @Yii::$app->thumbnail->url($this->getFilePath(), $options);
        } catch (\Exception $e) {
            return null;
        }
        return $thumb;
    }

    public function getFilePath()
    {
        $file = Yii::getAlias('@uploadroot/'.static::UPLOAD_DIR.'/'.$this->photo);

        return $file;
    }

    public function getItemsInfo()
    {
        $info = "";
        if ($this->no_items==1){
            $info .= str_replace(PHP_EOL,"<br>",$this->info2);
        }else{
            foreach (GearItem::find()->where(['gear_id' => $this->id])->andWhere(['active' => 1])->andWhere(['<>', 'info', ''])->asArray()->all() as $item) {
                    $info .= Yii::t('app', "nr")." ".$item['number']." - ".$item['info']."<br/>";
                                        
            }   
        }
        return $info;
    }
    public function getAssignedAttachements($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getGearAttachments();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getTasks()
    {
        return Task::find()->where(['gear_id'=>$this->id])->orderBy(['create_time'=>SORT_DESC])->all();
    }

    public function getAssignedItems($params = [])
    {
        $params = array_merge(['active'=>1], $params);
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->hasMany(\common\models\GearItem::className(), ['gear_id' => 'id'])->andWhere(['active'=>1])->orderBy(['number'=>SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getConnectedGears($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getGearConnecteds();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }
    public function getPurchaseGears($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getGearPurchases();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getTranslates($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getGearTranslates();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }
    public function getOuterConnectedGears($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getGearOuterConnecteds();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getConnectedNoOffer()
    {
        $query = $this->getGearConnecteds()->where(['<>', 'in_offer', 1])->count();
        return $query;
    }

    public function getSimilarGears($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getGearSimilars();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public static function getSelectList() 
    {
        $query = self::find()->joinWith(['category'])->orderBy('category_id');
        
        $list = [];
        
        $models = $query->all();
        foreach ($models as $model) {
            $list[$model->category->name][$model->id] = $model->name;
            
        }
        
        return $list;
    }

    public function getMainCategory()
    {
        $cat = $this->category;
        if ($cat->lvl == 1) {
            return $cat;
        }else{
            return $cat->parents()->andWhere(['lvl'=>1])->one();
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        //$this->getNoItemsItem();
        parent::afterSave($insert, $changedAttributes);
        if (isset($changedAttributes['no_items']) && $this->no_items != $changedAttributes['no_items']) {
            $noItemsName = '_ILOSC_SZTUK_';
            if ($this->no_items == 1) {
                $model = new GearItem();
                $model->name = $noItemsName;
                $model->gear_id = $this->id;
                $model->code = $this->code;
                $model->type = 0;
                $model->save();
            } else {
                foreach ($this->gearItems as $item) {
                    $item->active = 0;
                    $item->save();
                }
            }
        }
        if ($insert) {
            $warehouses = Warehouse::find()->all();
            foreach ($warehouses as $w) {
                $wq = new WarehouseQuantity();
                $wq->warehouse_id = $w->id;
                $wq->gear_id = $this->id;
                $wq->quantity = 0;
                $wq->save();
            }
            Note::createNote(1, 'gearAdded', $this, $this->id);
            $noItemsName = '_ILOSC_SZTUK_';
            if ($this->no_items == 1) {
                $model = new GearItem();
                $model->name = $noItemsName;
                $model->gear_id = $this->id;
                $model->type = 0;
                $model->active = 1;
                $model->save();
            }
            //dodajemy stawki cenowe
            $groups = \common\models\GearsPrice::find()->where(['type'=>1])->orWhere(['type'=>2, 'gear_category_id'=>[$this->category_id, $this->category->getMainCategory()]])->all();
            foreach ($groups as $group) {
                $price = new GearPrice(['gear_id'=>$this->id, 'price'=>$this->price, 'gears_price_id'=>$group->id]);
                $price->save();
            }           
        }else{
            if (isset($changedAttributes['code']) && $this->code != $changedAttributes['code'])
            {
                if ($this->no_items==1)
                {
                    $model = GearItem::findOne(['gear_id'=>$this->id, 'active'=>1]);
                    $model->code = $this->code;
                    $model->save();
                }
            }

            if (isset($changedAttributes['quantity']) && $this->quantity != $changedAttributes['quantity']) {
            Note::createNote(1, 'gearQuantityChanged', $this, $this->id);
        }
    }

    }

    public function getNoItemsItem()
    {
        $item =  $this->getGearItems()->where(['type'=>GearItem::TYPE_NO_ITEM])->one();

        if ($item == null) {
            $item = new GearItem();
            $item->type = GearItem::TYPE_NO_ITEM;
            $item->name = GearItem::NO_ITEM_NAME;
            $item->gear_id = $this->id;
            if ($item->save() == false) {
                throw new HttpException(400, Yii::t('app', 'Proszę skontaktować się z administartorem'));
            }
        }

        return $item;
    }

    public function getIsGearAssigned($event)
    {
        //w grupie
        $ids = $this->getGearItems()->column();
        //w evencie
        $assigned = $event->getGearItems()->column();

        $value = count($ids) > 0 ? true : false;
        foreach ($ids as $id) {
            if (in_array($id, $assigned) == false) {
                $value=false;
                break;
            }
        }
        return $value;
    }

    public function getIsOfferGearAssigned($event,$model)
    {
        return $event->getAssignedGear($event->id,$model->id);
    }

    public function getCalculatedVolume()
    {
        $volume = $this->width * $this->height * $this->depth;

        return $volume;
    }

    public function getGearItemDataProvider()
    {
        $gearItemQuery = GearItem::find()->where(['gear_id'=>$this->id]);

        $gearItemDataProvider = new ActiveDataProvider([
            'query'=>$gearItemQuery,
            'pagination'=>false,
            'sort'=>[
                'defaultOrder' => ['name'=>SORT_ASC],
            ]
        ]);

        return $gearItemDataProvider;
    }

    public function getOfferGearItemsQuantity($offer_id)
    {
        
        $quantity = 0;
        foreach ($this->offerGears as $key => $offerGear) {
            if($offerGear->offer_id == $offer_id){
                $quantity = $offerGear->quantity;
                break;
            }
        }
        $offer_gear_item = OfferGearItem::find()->joinWith('gearItem')->where(['offer_gear_item.offer_id'=>$offer_id, 'gear_item.gear_id' => $this->id])->all();
        return ($quantity-count($offer_gear_item));
    }

    public function getCrossRental()
    {
        $count = CrossRental::find()->where(['owner_gear_id'=>$this->id])->andWhere(['owner'=>\Yii::$app->params['companyID']])->one();
        if ($count)
            return $count->quantity;
        else
            return 0;
    }

    public function numberOfItems()
    {
        $available = GearItem::find()->where(['gear_id' => $this->id])->andWhere(['active'=>1])->all();


        if ($this->no_items) {
            $count_available = $this->quantity;
        } else {
            $count_available = count($available);
        }
        return $count_available;        
    }

    public function numberOfAvailable()
    {
        

        $available = GearItem::find()->where(['gear_id' => $this->id])->andWhere(['active'=>1])->all();


        if ($this->no_items) {
            $count_available = $this->quantity;
        } else {
            $count_available = count($available);
        }
        

        $not_available = 0;
        foreach ($available as $gear_item) {
            $not_available_items = OutcomesGearOur::find()->where(['gear_id' => $gear_item->id])->all();
            foreach ($not_available_items as $gear) {
                $not_available+=$gear->gear_quantity;
            }
        }

        $returned_items = 0;
        foreach ($available as $gear_item) {
            $returned_items_arr = IncomesGearOur::find()->where(['gear_id' => $gear_item->id])->all();
            foreach ($returned_items_arr as $returned_item) {
                $returned_items++;
            }
        }

        $service_gear = 0;
        foreach ($available as $gear_item) {
            $service = GearService::getCurrentModel($gear_item->id);
            if ($service != null && $gear_item->active == 1) {
                $service_gear++;
            }
        }

        // dostępne = wszystkie - wypożyczone + zwrócone - w_serwisie
        if (($count_available - $not_available + $returned_items - $service_gear)<0) {
            return 0;
        }
        return $count_available - $not_available + $returned_items - $service_gear;
    }

    public function isOnTheRun()
    {
        foreach ($this->gearItems as $item) {
            if ((!$item->isAvailableForOutcome())&&($item->active==1)&&($item->status==1)) {
                return true;
            }
        }
        return false;
    }

    
    public function getAvailabe($start, $end)
    {
        $gearItemIds = array();
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        $interval = date_diff($start, $end)->format('%a');
        if ($interval < 365) {
        $working = PacklistGear::find()
            ->where(['and', ['<', 'start_time', $end->format("Y-m-d H:i:s")],['>', 'end_time', $start->format("Y-m-d H:i:s")], ['gear_id'=> $this->id]])
            ->all();
        //$working = array_merge($working1, $working2);

        $working_rents = RentGear::find()
            ->where(['and', ['>', 'end_time', $start->format("Y-m-d H:i:s")],['<=', 'end_time', $end->format("Y-m-d H:i:s")], ['gear_id'=> $this->id]])
            ->orWhere(['and', ['<=', 'start_time', $end->format("Y-m-d H:i:s")],['>=', 'end_time', $end->format("Y-m-d H:i:s")], ['gear_id'=> $this->id]])
            ->all();
        $max = 0;
        $day = $start;
            while ($day->format("Y-m-d H:i:s") <= $end->format("Y-m-d H:i:s")) {
            $quantity=0;
            $a = clone $day;
            $a->modify('+1 hour');
                foreach ($working as $w) {
                    if (($w->start_time < $a->format("Y-m-d H:i:s")) && ($w->end_time > $day->format("Y-m-d H:i:s"))) {
                    $quantity+=$w->quantity;
                }
            }
                foreach ($working_rents as $w) {
                    if (($w->start_time < $a->format("Y-m-d H:i:s")) && ($w->end_time > $day->format("Y-m-d H:i:s"))) {
                    $quantity+=$w->quantity;
                }
            }
            if ($quantity>$max)
                $max = $quantity;
            $day->modify('+1 hour');

        }
        }else{
            $max=0;
        }

        if ($this->no_items) {
            $quantity = $this->quantity;
        } else {
            $quantity = $this->getGearItems()->andWhere(['active'=>1])->count();
        }


        if ($quantity-$max<0) {
            return 0;
        }
        return $quantity-$max;
    }

    public function getAvailableDateChanged($start, $end, $id, $type)
    {
        $gearItemIds = array();
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        if ($type == 'event') {
            $working = PacklistGear::find()
            ->where(['and', ['>', 'end_time', $start->format("Y-m-d H:i:s")],['<=', 'start_time', $end->format("Y-m-d H:i:s")], ['gear_id'=> $this->id], ['<>', 'id', $id]])
            ->all();
            $working_rents = RentGear::find()
            ->where(['and', ['>', 'end_time', $start->format("Y-m-d H:i:s")],['<=', 'start_time', $end->format("Y-m-d H:i:s")], ['gear_id'=> $this->id]])
            ->all();
        }else{
            $working = EventGear::find()
            ->where(['and', ['>', 'end_time', $start->format("Y-m-d H:i:s")],['<=', 'start_time', $end->format("Y-m-d H:i:s")], ['gear_id'=> $this->id]])
            ->all();   
            $working_rents = RentGear::find()
            ->where(['and', ['>', 'end_time', $start->format("Y-m-d H:i:s")],['<=', 'start_time', $end->format("Y-m-d H:i:s")], ['gear_id'=> $this->id], ['<>', 'rent_id', $id]])
            ->all();        
        }

        //$working = array_merge($working1, $working2);


        $max = 0;
        $day = $start;
        while ($day->format("Y-m-d H:i:s") < $end->format("Y-m-d H:i:s")) {
            $quantity=0;
            $a = clone $day;
            $a->modify('+1 hour');
            foreach ($working as $w) {
                if (($w->start_time < $a->format("Y-m-d H:i:s")) && ($w->end_time > $day->format("Y-m-d H:i:s"))) {
                    $quantity+=$w->quantity;
                }
            }
            foreach ($working_rents as $w) {
                if (($w->start_time < $a->format("Y-m-d H:i:s")) && ($w->end_time > $day->format("Y-m-d H:i:s"))) {
                    $quantity+=$w->quantity;
                }
            }
            if ($quantity>$max)
                $max = $quantity;
            $day->modify('+1 hour');

        }

        if ($this->no_items) {
            $quantity = $this->quantity;
        } else {
            $quantity = $this->getGearItems()->andWhere(['active'=>1])->count();
        }
        if ($this->no_items) {
                        $serwisNumber = $this->getNoItemSerwis();
        }else{
                        $serwisNumber = 0;
                        foreach ($this->gearItems as $item) {
                            if ($item->active == 1 && $item->status === GearItem::STATUS_SERVICE) {
                                $serwisNumber++;
                            }
                        }
        }

        if ($quantity-$max-$serwisNumber<0) {
            return 0;
        }
        return $quantity-$max-$serwisNumber;
    }

    public function getEvents($start, $end)
    {

        $start = new \DateTime($start);
        $end = new \DateTime($end);
        $events = PacklistGear::find()
            ->where(['and', ['>', 'end_time', $start->format("Y-m-d H:i:s")],['<=', 'start_time', $end->format("Y-m-d H:i:s")], ['gear_id'=> $this->id]])->orderBy([ 'start_time'=>SORT_ASC])
            ->all();
        //$working = array_merge($working1, $working2);

        $rents = RentGear::find()
            ->where(['and', ['>', 'end_time', $start->format("Y-m-d H:i:s")],['<=', 'start_time', $end->format("Y-m-d H:i:s")], ['gear_id'=> $this->id]])
            ->all();          
        return array('events'=>$events, 'rents'=>$rents);
    }

    public function getEventsNear($start, $end)
    {
        $startD = new \DateTime($start);
        $endD = new \DateTime($end);
        $startD->modify('-1 day');
        $endD->modify('+1 day');
        $events = PacklistGear::find()
            ->where(['and', ['>', 'end_time', $startD->format("Y-m-d H:i:s")],['<', 'end_time', $start], ['gear_id'=> $this->id]])
            ->orWhere(['and', ['>', 'start_time', $end],['<', 'start_time', $endD->format("Y-m-d H:i:s")], ['gear_id'=> $this->id]])
            ->all();
        //$working = array_merge($working1, $working2);

        $rents = RentGear::find()
            ->where(['and', ['>', 'end_time', $startD->format("Y-m-d H:i:s")],['<', 'end_time', $start], ['gear_id'=> $this->id]])
            ->orWhere(['and', ['>', 'start_time', $end],['<', 'start_time', $endD->format("Y-m-d H:i:s")], ['gear_id'=> $this->id]])
            ->all();      
        return array('events'=>$events, 'rents'=>$rents);
    }

    public function getOffersInPeriod($start, $end, $offer_id)
    {
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        $start->modify('-5 days');
        $end->modify('+5 days');
        if ($offer_id)
            $event_ids = ArrayHelper::map(Offer::find()
            ->where(['and', ['>', 'event_start', $start->format("Y-m-d H:i:s")],['<=', 'event_end', $end->format("Y-m-d H:i:s")],  ['<>', 'id', $offer_id]])
            ->orWhere(['and', ['<=', 'event_start', $end->format("Y-m-d H:i:s")],['>=', 'event_end', $end->format("Y-m-d H:i:s")], ['<>', 'id', $offer_id]])
            ->asArray()->all(), 'id', 'id');
        else
             $event_ids = ArrayHelper::map(Offer::find()
            ->where(['and', ['>', 'event_start', $start->format("Y-m-d H:i:s")],['<=', 'event_end', $end->format("Y-m-d H:i:s")]])
            ->orWhere(['and', ['<=', 'event_start', $end->format("Y-m-d H:i:s")],['>=', 'event_end', $end->format("Y-m-d H:i:s")]])
            ->asArray()->all(), 'id', 'id');           
        $events = OfferGear::find()->where(['IN', 'offer_id', $event_ids])->andWhere(['gear_id'=>$this->id])->all();          
        return $events;
    }

    public function getGearQuantity()
    {
        if ($this->no_items == 1) {
            return $this->quantity;
        } else {
            $i = 0;
            /** @var \common\models\GearItem $item */
            foreach ($this->gearItems as $item) {
                if ($item->active && $item->status == GearItem::STATUS_ACTIVE) {
                    $i++;
                }
            }
            return $i;
        }
    }

    public function numberAssignedToEvent(Event $event)
    {
        $eventGear = EventGear::find()->where(['event_id' => $event->id])->andWhere(['gear_id'=> $this->id])->one();
        if ($eventGear)
            return $eventGear->quantity;
        else
            return 0;
    }

    public function numberInConflicts(Event $event)
    {
        $eventGear = EventConflict::find()->where(['event_id' => $event->id])->andWhere(['gear_id'=> $this->id])->andWhere(['resolved'=>0])->one();
        if ($eventGear)
            return $eventGear->quantity;
        else
            return 0;
    }

    public function numberAssignedToRent(Rent $event)
    {
        $eventGear = RentGear::find()->where(['rent_id' => $event->id])->andWhere(['gear_id'=> $this->id])->one();
        if ($eventGear)
            return $eventGear->quantity;
        else
            return 0;
    }

    public function getPacking()
    {
        if ($this->no_items) {
            if ($this->packing){

                $cases[$this->packing] = $this->packing." ".Yii::t('app', 'szt.');
                return $cases ;
            }
            
        }
        $results = GearItem::find()->select('count(*) as ile')->where(['gear_id'=>$this->id])->andWhere(['active'=>1])->andWhere(['>', 'group_id', 0])->groupBy(['group_id'])->asArray()->all();
        $cases = [];
        if (!$results) {
            $cases[1] = "1 ".Yii::t('app', 'szt.');
        }else{
            foreach ($results as $result) {
                $cases[$result['ile']] = $result['ile']." ".Yii::t('app', 'szt.');
            }
            $string = "";
                     
        }
        return $cases ;
    }

    public function getPacking2()
    {
        $results = $this->getPacking();
        foreach ($results as $result) {
                $cases[] = $result;
            }                
        return $cases ;
    }

    public function getAvability($start, $end)
    {
        $events = $this->getEvents($start, $end);
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        if ($this->no_items) {
            $quantity = $this->quantity;
        } else {
            $quantity = $this->getGearItems()->andWhere(['active'=>1])->count();
        }
        if ($this->no_items) {
                        $serwisNumber = $this->getNoItemSerwis();
        }else{
                        $serwisNumber = 0;
                        foreach ($this->gearItems as $item) {
                            if ($item->active == 1 && $item->status === GearItem::STATUS_SERVICE) {
                                $serwisNumber++;
                            }
                        }
        }
        $max = $quantity-$serwisNumber;
        $day = $start;
        $i=0;
        $avabiltyArray = [];
        $current = 99999999;
        $conflictArray = [];
        while ($day->format("Y-m-d H:i:s") <= $end->format("Y-m-d H:i:s")) {
            $quantity=0;
            $a = clone $day;
            $a->modify('+1 hour');
            foreach ($events['events'] as $w) {
                if (($w->start_time < $a->format("Y-m-d H:i:s")) && ($w->end_time > $day->format("Y-m-d H:i:s"))) {
                    $quantity+=$w->quantity;
                    $conflict = EventConflict::find()->where(['event_id'=>$w->event_id, 'gear_id'=>$w->gear_id, 'resolved'=>0])->one();
                    if ($conflict)
                        $quantity+=$conflict->quantity;
                }

            }
            foreach ($events['rents'] as $w) {
                if (($w->start_time < $a->format("Y-m-d H:i:s")) && ($w->end_time > $day->format("Y-m-d H:i:s"))) {
                    $quantity+=$w->quantity;
                }
            }
            $quantity=$max-$quantity;
            if ($quantity <> $current) {
                $i++;
                $avabiltyArray[$i]['start']=$day->format("Y-m-d H:i:s");
                $avabiltyArray[$i]['end']=$a->format("Y-m-d H:i:s");
                $avabiltyArray[$i]['title']=$quantity;
                if ($quantity < 0) {
                    $avabiltyArray[$i]['color'] = '#ed5565';
                }
                if ($quantity == 0) {
                    $avabiltyArray[$i]['color'] = '#f8ac59';
                }
                if ($quantity > 0) {
                    $avabiltyArray[$i]['color'] = '#1ab394';
                }
                $current = $quantity;
            }else{
                $avabiltyArray[$i]['end']=$a->format("Y-m-d H:i:s");
            }
            $day->modify('+1 hour');

        }
        $gearArray = "";      
        foreach ($avabiltyArray as $gear) {
            $tmp = "{title: '".$gear['title']."', resourceId:'a', start:'".substr($gear['start'], 0, 10)."T".substr($gear['start'], 11, 8)."', end:'".substr($gear['end'], 0, 10)."T".substr($gear['end'], 11, 8)."', backgroundColor:'".$gear['color']."',editable: false},";
            $gearArray .= $tmp;
        }
        return $gearArray;
    }
    public function getAvabilityArray($start, $end)
    {
        $events = $this->getEvents($start, $end);
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        if ($this->no_items) {
            $quantity = $this->quantity;
        } else {
            $quantity = $this->getGearItems()->andWhere(['active'=>1])->count();
        }
        if ($this->no_items) {
                        $serwisNumber = $this->getNoItemSerwis();
        }else{
                        $serwisNumber = 0;
                        foreach ($this->gearItems as $item) {
                            if ($item->active == 1 && $item->status === GearItem::STATUS_SERVICE) {
                                $serwisNumber++;
                            }
                        }
        }
        $max = $quantity-$serwisNumber;
        $day = $start;
        $i=0;
        $avabiltyArray = [];
        $current = 99999999;
        while ($day->format("Y-m-d H:i:s") <= $end->format("Y-m-d H:i:s")) {
            $quantity=0;
            $a = clone $day;
            $a->modify('+1 hour');
            foreach ($events['events'] as $w) {
                if (($w->start_time < $a->format("Y-m-d H:i:s")) && ($w->end_time > $day->format("Y-m-d H:i:s"))) {
                    $quantity+=$w->quantity;
                    $conflict = EventConflict::find()->where(['event_id'=>$w->packlist->event_id, 'gear_id'=>$w->gear_id, 'resolved'=>0])->one();
                    if ($conflict)
                        $quantity+=$conflict->quantity;
                }

            }
            foreach ($events['rents'] as $w) {
                if (($w->start_time < $a->format("Y-m-d H:i:s")) && ($w->end_time > $day->format("Y-m-d H:i:s"))) {
                    $quantity+=$w->quantity;
                }
            }
            $quantity=$max-$quantity;
            if ($quantity <> $current) {
                $i++;
                $avabiltyArray[$i]['start']=$day->format("Y-m-d H:i:s");
                $avabiltyArray[$i]['end']=$a->format("Y-m-d H:i:s");
                $avabiltyArray[$i]['title']=$quantity;
                $avabiltyArray[$i]['resourceId']='a';
                if ($quantity < 0) {
                    $avabiltyArray[$i]['backgroundColor'] = '#ed5565';
                }
                if ($quantity == 0) {
                    $avabiltyArray[$i]['backgroundColor'] = '#f8ac59';
                }
                if ($quantity > 0) {
                    $avabiltyArray[$i]['backgroundColor'] = '#1ab394';
                }
                $current = $quantity;
            }else{
                $avabiltyArray[$i]['end']=$a->format("Y-m-d H:i:s");
            }
            $day->modify('+1 hour');

        }
        return $avabiltyArray;
    }

    public function getNoItemSerwis()
    {
        $number = 0;
        $statuts = ArrayHelper::map(GearServiceStatut::find()->where(['type'=>1])->asArray()->all(), 'id', 'id');
        if (isset($this->gearItems[0])) {
            $gear_item_id = GearItem::find()->where(['active'=>1])->andWhere(['gear_id'=>$this->id])->one()->id;
            $services = GearService::find()->where(['gear_item_id'=>$gear_item_id])->andWhere(['status'=>$statuts])->all();
            foreach ($services as $s) {
                $number +=$s->quantity;
            }           
        }

        return $number;
    }

    public function generateBarCode()
    {
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

    public function generateQrCode($width = null)
        {
        if ($width) {
            return Html::img(Url::to(['qr-code/get-big-img', 'text'=>$this->getBarCodeValue()]), ['width'=>$width]);
        }else
            return Html::img(Url::to(['qr-code/get-img', 'text'=>$this->getBarCodeValue()]));
    }

    public function generateQrCodeAsLink()
    {
        return Html::a($this->generateQrCode(), Url::toRoute(['qr-code/get-big-img', 'text' => $this->getBarCodeValue()]), ['download' => $this->name.'.png']);
    }

    public function getBarCodeValue()
    {
        // 13 digits
        
        return BarCode::MODEL . BarCode::OUR_WAREHOUSE . $this->getNineDigits();
    }

    private function getNineDigits()
    {
        $id_length = strlen($this->id);
        return str_repeat('0', 9-$id_length) . $this->id;
    }


    public function getOfferPrices($priceGroup = null)
    {
        if ($priceGroup){
        $ids = [];
            if (isset($priceGroup->gearsPrices)) {
                foreach ($priceGroup->gearsPrices as $gp) {
                $ids[$gp->id] = $gp->id;
            } 
        }

            return GearPrice::find()->where(['gear_id'=>$this->id])->andWhere(['IN', 'gears_price_id', $ids])->all();
        }else{
            return GearPrice::find()->where(['gear_id'=>$this->id])->all();
        }
    }

    public function countVolume()
    {
        if ($this->no_items)
            if ($this->volume)
                return $this->volume;
            else 
                return $this->depth*$this->width*$this->height/1000000;
        else{
            if ($this->gearItems) {
                                $item = GearItem::find()->where(['active'=>1])->andWhere(['gear_id'=>$this->id])->one();
                if ($item){
                    if ($item->group_id) {
                    $count = count($item->group->gearItems);
                    $group = $item->group;
                    if ($group->volume)
                        return $group->volume/$count;
                    else
                        return $group->height*$group->width*$group->depth/$count/1000000;
                }else{
                    if ($item->volume)
                        return $item->volume;
                    else
                        return $item->depth*$item->width*$item->height/1000000;
                }
                }else{
                    if ($this->volume)
                        return $this->volume;
                    else 
                        return $this->depth*$this->width*$this->height/1000000;
                }
            }else{
                return $this->volume;
            }
        }
    }

    public function countVolume2($quantity)
    {
        if ($this->no_items)
            if ($this->packing) {
                $cases = ceil($quantity/$this->packing);
                if ($this->volume_case)
                    return $this->volume_case*$cases;
                else 
                    return $this->depth_case*$this->width_case*$this->height_case/1000000*$cases;
            }else{
                if ($this->volume)
                    return $this->volume*$quantity;
                else 
                    return $this->depth*$this->width*$this->height/1000000*$quantity;
            }

        else{
            if ($this->gearItems) {
                $item = GearItem::find()->where(['active'=>1])->andWhere(['gear_id'=>$this->id])->one();
                if ($item){
                    if ($item->group_id) {
                        $count = count($item->group->gearItems);
                        $group = $item->group;
                        $cases = ceil($quantity/$count);
                        if ($group->volume)
                            return $group->volume*$cases;
                        else
                            return $group->height*$group->width*$group->depth/1000000*$cases;
                    }else{
                        if ($item->one_in_case) {
                            return $item->depth_case*$item->width_case*$item->height_case/1000000*$quantity;
                        }else{
                            if ($this->volume)
                            return $this->volume*$quantity;
                        else
                            return $this->depth*$this->width*$this->height/1000000*$quantity;
                        }

                    }   
                }else{
                    if ($this->volume)
                            return $this->volume*$quantity;
                        else
                            return $this->depth*$this->width*$this->height/1000000*$quantity;
                }

            }else{
                return $this->volume*$quantity;
            }
        }
    }

    public function getSimilarCount()
    {
        $count = 0;
        foreach ($this->gearSimilars as $similar) {
            $similar = $similar->similar;
            if ($similar->no_items) {
                $count +=$similar->quantity;
            }else{
                $count +=$similar->getGearItems()->andWhere(['active'=>1])->count();
            }
        }
        return $count;
    }

    public function getWeightCase($gears)
    {
        if ($this->no_items) {
            if ($this->packing){
                $cases = ceil($gears/$this->packing);
                return $cases*$this->weight_case;
            }else{
                return 0;
            }
            
        } else {
            if ($this->gearItems) {
                $item = GearItem::find()->where(['active'=>1])->andWhere(['gear_id'=>$this->id])->one();
                if ($item) {
                    if ($item->group_id) {
                        $count = count($item->group->gearItems);
                        $group = $item->group;
                        return ceil($gears/$count)*$group->weight;
                    }else{
                        if ($item->one_in_case) {
                            return $gears*($item->weight_case-$model->weight);
                        }else{
                            return 0;
                        }
                        
                    }
                }else{
                    return 0;
                }
                
            }else{
                return 0;
            }
        }        
    }

    public function checkConflicts()
    {
        $gears = PacklistGear::find()->where(['gear_id'=>$this->id])->andWhere(['>', 'start_time', date('Y-m-d H:i:s')])->all();
        foreach ($gears as $gear) {
            $number = $this->getAvailabe($gear->start_time, $gear->end_time);
            if ($gear->gear->no_items)
                $serwisNumber = $this->getNoItemSerwis();
            else
                $serwisNumber = GearItem::find()->where(['status'=>10, 'active'=>1, 'gear_id'=>$this->id])->count();
            $number = $number-$serwisNumber;
            if ($number < 0) {
                //jest konflikt!
                $missing = 0-$number;
                $conflict = EventConflict::find()->where(['gear_id'=>$this->id, 'event_id'=>$gear->packlist->event_id, 'resolved'=>0, 'packlist_gear_id'=>$gear->id])->one();
                if ($gear->quantity > $missing) {
                    if (!$conflict)
                        $conflict = new EventConflict(['gear_id'=>$this->id, 'event_id'=>$gear->packlist->event_id, 'quantity'=>$missing, 'added'=>$gear->quantity-$missing, 'packlist_gear_id'=>$gear->id]);
                    else{
                        $conflict->quantity+=$missing;
                        $conflict->added-=$missing;
                    }
                    $conflict->resolved = 0;
                    $conflict->save();
                    $gear->quantity = $gear->quantity-$missing;
                    $gear->save();
                }else{
                    if (!$conflict)
                        $conflict = new EventConflict(['gear_id'=>$this->id, 'event_id'=>$gear->packlist->event_id, 'quantity'=>$gear->quantity, 'added'=>0, 'packlist_gear_id'=>$gear->id]);
                    else{
                        $conflict->quantity+=$gear->quantity;
                        $conflict->added=0;
                    }
                    $conflict->resolved = 0;
                    $conflict->save();
                    $gear->quantity = 0;
                    $gear->save();
                }
            }
        }
    }

    public function checkConflictsAfterReturn()
    {
        $ids = ArrayHelper::map(PacklistGear::find()->where(['gear_id'=>$this->id])->andWhere(['>', 'start_time', date('Y-m-d')])->asArray()->all(), 'id', 'id');
        $conflicts = EventConflict::find()->where(['packlist_gear_id'=>$ids])->andWhere(['gear_id'=>$this->id])->andWhere(['resolved'=>0])->all();
        foreach ($conflicts as $conflict) {
                $available = $this->getAvailabe($conflict->packlistGear->start_time, $conflict->packlistGear->end_time)-$this->getInService();
            if ($available >= $conflict->quantity) {
                    $oldQuantity=0;
                    $currentConlict = 0;
                    $egm = PacklistGear::findOne(['id'=>$conflict->packlist_gear_id]);
                    if ($egm)
                        $oldQuantity = $egm->quantity;
                    $quantity = $oldQuantity+$conflict->quantity;
                    Event::assignGearToPacklist($conflict->packlistGear->packlist_id, $this->id, $quantity, $conflict->packlistGear->start_time, $conflict->packlistGear->end_time, $oldQuantity);
                }
            }
    }

        public function getGearCalendarArray($start=null, $end=null)
    {
        $mobile = $this->_userAgent();

        if (!$start)
            $start = date("Y-m-d");
        if (!$end)
            $end = date("Y-m-d");
        $checkstart = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $start ) ) . "-5 days" ) );
        $checkend = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $end ) ) . "+180 days" ) );
        $gears = PacklistGear::find()->where(['gear_id'=>$this->id])->andWhere(['>', 'end_time', $checkstart])->andWhere(['<', 'start_time', $checkend])->all();
        $gearArray = [];
        $array2 =[];
        $array2[]=['id'=>'a', 'title'=>Yii::t('app', 'Dostępność')];
        $ids = [];

        foreach ($gears as $gear) {
            $conflict = EventConflict::find()->where(['packlist_gear_id'=>$gear->id, 'resolved'=>0])->one();
            $start = strtotime($gear->start_time);
            $end = strtotime($gear->end_time);
            $ids[] = $gear->packlist->event_id;
            $full_time = $end - $start;
            $quantity = $gear->quantity;
            $background = "#1ab394";
            if ($conflict){
                $quantity= $gear->quantity+$conflict->quantity;
                $background = "#ed5565";
            }
            if ($mobile) {
                $tmp = ['title' => $quantity, 'id' => $gear->packlist->event_id, 'resourceId' => 'e' . $gear->packlist->event_id, 'start' => substr($gear->start_time, 0, 10) . "T" . substr($gear->start_time, 11, 8), 'end' => substr($gear->end_time, 0, 10) . "T" . substr($gear->end_time, 11, 8), 'backgroundColor' => $background];
            } else {
            $tmp = ['title'=>$gear->packlist->name." (".$gear->quantity."/".$quantity.")", 'id'=>$gear->packlist->event_id, 'resourceId'=>'e'.$gear->packlist->event_id, 'start'=>substr($gear->start_time, 0, 10)."T".substr($gear->start_time, 11, 8), 'end'=>substr($gear->end_time, 0, 10)."T".substr($gear->end_time, 11, 8), 'backgroundColor'=>$background] ;
            }
            $gearArray[] = $tmp;
        }
        $gears = RentGear::find()->where(['gear_id'=>$this->id])->andWhere(['>', 'end_time', $checkstart])->andWhere(['<', 'start_time', $checkend])->all();
        foreach ($gears as $gear) {
            $tmp = ['title'=>$gear->rent->name." (".$gear->quantity."/".$gear->quantity.")", 'id'=>$gear->rent_id, 'resourceId'=>'r'.$geear->rent_id, 'start'=>substr($gear->start_time, 0, 10)."T".substr($gear->start_time, 11, 8), 'end'=>substr($gear->end_time, 0, 10)."T".substr($gear->end_time, 11, 8), 'backgroundColor'=>'#1c84c6'] ;
            $gearArray[] = $tmp;
            $array2[] = ["id"=>'r'.$geear->rent_id, "title"=>$gear->rent->name];

        }
        $events = Event::find()->where(['id'=>$ids])->asArray()->all();

        foreach ($events as $e) {
                $array2[] = ['id'=>'e'.$e['id'], 'title'=>str_replace("'", "", str_replace('"', "", $e['name']))];
            }
        $gearArray = array_merge($gearArray, $this->getAvabilityArray($checkstart, $checkend));     
        
        return ['events'=>$gearArray, 'resources'=>$array2];
    }

    public function getGearCalendarArrayRes($start=null, $end=null)
    {
        if (!$start)
            $start = date("Y-m-d");
        if (!$end)
            $end = date("Y-m-d");
        $checkstart = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $start ) ) . "-5 days" ) );
        $checkend = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $end ) ) . "+180 days" ) );
        $gears = ArrayHelper::map(PacklistGear::find()->where(['gear_id'=>$this->id])->andWhere(['>', 'end_time', $checkstart])->andWhere(['<', 'start_time', $checkend])->asArray()->all(), 'packlist_id', 'packlist_id');
        $ids = ArrayHelper::map(Packlist::find()->where(['id'=>$gears])->asArray()->all(), 'event_id', 'event_id');
        $array2 =[];
        $array2[]=['id'=>'a', 'title'=>Yii::t('app', 'Dostępność')];
        $gears = RentGear::find()->where(['gear_id'=>$this->id])->andWhere(['>', 'end_time', $checkstart])->andWhere(['<', 'start_time', $checkend])->all();
        foreach ($gears as $gear) {
            $gearArray[] = $tmp;
            $array2[] = ["id"=>'r'.$geear->rent_id, "title"=>$gear->rent->name];

        }
        $events = Event::find()->where(['id'=>$ids])->asArray()->all();
        foreach ($events as $e) {
            $array2[] = ['id' => 'e' . $e['id'], 'title' => str_replace("'", "", str_replace('"', "", $e['name']))];
        }

        return ['resources' => $array2];
    }

    public function getGearCalendarArrayResMobile($start = null, $end = null)
            {
        if (!$start)
            $start = date("Y-m-d");
        if (!$end)
            $end = date("Y-m-d");
        $checkstart = date("Y-m-d", strtotime(date("Y-m-d", strtotime($start)) . "-5 days"));
        $checkend = date("Y-m-d", strtotime(date("Y-m-d", strtotime($end)) . "+180 days"));
        $gears = ArrayHelper::map(PacklistGear::find()->where(['gear_id' => $this->id])->andWhere(['>', 'end_time', $checkstart])->andWhere(['<', 'start_time', $checkend])->asArray()->all(), 'packlist_id', 'packlist_id');
        $ids = ArrayHelper::map(Packlist::find()->where(['id' => $gears])->asArray()->all(), 'event_id', 'event_id');
        $array2 = [];
        $array2[] = ['id' => 'a', 'title' => Yii::t('app', 'Dostępność')];
        $gears = RentGear::find()->where(['gear_id' => $this->id])->andWhere(['>', 'end_time', $checkstart])->andWhere(['<', 'start_time', $checkend])->all();
        foreach ($gears as $gear) {
            $gearArray[] = $tmp;
            $array2[] = ["id" => 'r' . $geear->rent_id, "title" => $gear->rent->name];

        }
        $events = Event::find()->where(['id' => $ids])->asArray()->all();
        foreach ($events as $e) {
//            Vardumper::dump($e, 1000,true);exit();
                $array2[] = ['id'=>'e'.$e['id'], 'title'=>str_replace("'", "", str_replace('"', "", $e['name']))];
            }
        
        return ['resources'=>$array2];
    }
}
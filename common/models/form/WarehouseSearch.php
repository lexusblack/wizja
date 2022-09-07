<?php
namespace  common\models\form;

use common\helpers\ArrayHelper;
use common\models\GearCategory;
use common\models\GearItem;
use common\models\GearFavorite;
use common\models\GearItemsNoItemsRfid;
use common\models\BarCode;
use common\models\Gear;
use common\models\GearGroup;
use common\models\GearService;
use dmstr\helpers\Html;
use yii\data\ActiveDataProvider;
use Yii;
use yii\base\Model;

class WarehouseSearch extends Model
{
    public $q;
    public $from_date;
    public $to_date;
    public $type;

    public $activeModel;
    public $activeGroup;
    public $favorite = 0;
    public $showGroups = false;

    public $categories = [];
    public $gear_query = null;

    protected $_unavailableData;
    protected $_unavailableDates;

    protected $_categoryId;

    protected $_gearDataProvider;
    protected $_gearItemDataProvider;
    protected $_gearGroupDataProvider;
    protected $_gearGroupItemDataProvider;


    public function rules()
    {
        $rules = [
            [['q', 'from_date', 'to_date'], 'string'],
            [['activeModel', 'activeGroup'], 'integer'],
        ];

        return array_merge(parent::rules(), $rules);
    }


    public function init()
    {
//        \Yii::$app->cache->flush();
        if($this->from_date==null)
        {
            $this->from_date = date('Y-m-d')." 00:00:00";
        }
        if($this->to_date==null)
        {
            $this->to_date = date('Y-m-d')." 23:59:59";
        }
        parent::init();
    }

    public function getDataProviders()
    {
        $this->_setDataProviders();
        return [
            'gearDataProvider'=>$this->_gearDataProvider,
            'gearItemDataProvider'=>$this->_gearItemDataProvider,
            'gearGroupDataProvider' => $this->_gearGroupDataProvider,
            'gearGroupItemDataProvider' => $this->_gearGroupItemDataProvider,
        ];

    }

    public function getUnavailableData()
    {
        if ($this->_unavailableData === null)
        {
            $this->_unavailableData = GearItem::getUnavailableDataInRange($this->from_date, $this->to_date);
        }
        return $this->_unavailableData;
    }

    public function getUnavailableDates()
    {
        if ($this->_unavailableDates === null)
        {
            $this->_unavailableDates = GearItem::getUnavailableDatesInRange($this->from_date, $this->to_date);
        }
        return $this->_unavailableDates;
    }

    public function getUnavailableRanges($itemId)
    {
        $data = ArrayHelper::getValue($this->getUnavailableDates(), $itemId, false);
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

    protected function _setDataProviders($pagination=true)
    {
        $gearItem = null;
        if (strlen($this->to_date)<15)
        {
            if ($this->to_date!="")
                $this->to_date .=" 23:59:59";
        }
        if(isset($this->gear_query)){
            $gearQuery = $this->gear_query;
        } else {

            $categories = array_reverse($this->categories);
            $sub = null;
            foreach ($categories as $cat)
            {
                if ($cat !==null)
                {
                    $sub = $cat;
                    break;
                }
            }

            $categoryIds = [];
            $ids = [];
            $tmpCat = GearCategory::findOne($sub);

            if ($tmpCat !== null)
            {
                $ids = $tmpCat->children()->column();
            }

            $categoryIds = array_merge([$sub], $ids);
            //Model
            if ($this->type=="offer")
            {
                $gearQuery = Gear::find()
                    ->andWhere(['active'=>1])
                    ->andWhere(['visible_in_offer'=>1])
                    ->andFilterWhere([
                        'category_id'=>$categoryIds,
                    ])
                    ->andFilterWhere(['like', 'name', $this->q]);
                if ($this->favorite)
                {
                    $ids = ArrayHelper::map(GearFavorite::find()->where(['user_id'=>Yii::$app->user->id])->asArray()->all(), 'gear_id', 'gear_id');
                    $gearQuery->innerJoinWith('gearFavorite')->andWhere(['gear.id'=>$ids])->orderBy(['gear_favorite.position'=>SORT_ASC]);
                }
            }else{
                if ($this->q)
                {
                                    $gearQuery = Gear::find()
                    ->andWhere(['active'=>1])
                    ->andWhere(['visible_in_warehouse'=>1]);
                }else{
                                    $gearQuery = Gear::find()
                    ->andWhere(['active'=>1])
                    ->andWhere(['visible_in_warehouse'=>1])
                    ->andFilterWhere([
                        'category_id'=>$categoryIds,
                    ]);
                }

                if ($this->favorite)
                {
                    $ids = ArrayHelper::map(GearFavorite::find()->where(['user_id'=>Yii::$app->user->id])->asArray()->all(), 'gear_id', 'gear_id');
                    $gearQuery->innerJoinWith('gearFavorite')->andWhere(['gear.id'=>$ids])->orderBy(['gear_favorite.position'=>SORT_ASC]);
                }
                    if ((is_numeric($this->q[0]))&&(is_numeric($this->q[5])))
                    {
                        $c = \common\models\Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
                        if ($c->own_ean)
                        {
                            $gear_id=0;
                            $gearItem = GearItem::find()->where(['code'=>$this->q])->andWhere(['active'=>1])->one();
                            if ($gearItem)
                            {
                                if ($gearItem->gear->no_items)
                                {
                                    $gear_id = $gearItem->gear_id;
                                    
                                }else{
                                    $gear_id = $gearItem->gear_id;
                                    $this->activeModel = $gearItem->gear_id;
                                    $this->activeGroup = $gearItem->group_id;
                                }
                            }else{
                                $gearGroup = GearGroup::find()->where(['code'=>$this->q])->one();
                                if ($gearGroup)
                                {
                                    $gearItem = GearItem::find()->where(['group_id'=>$gearGroup->id])->andWhere(['active'=>1])->one();
                                    $gear_id = $gearItem->gear_id;
                                }
                            }

                        }else{
                            $gear_id=0;
                            $id = (int)substr($this->q, 4, 9);
                            $type = substr($this->q, 0, 2);
                            if ($type == BarCode::ITEMS_GROUP) {
                                $gearItem = GearItem::find()->where(['group_id'=>$id])->andWhere(['active'=>1])->one();
                                $gear_id = $gearItem->gear_id;
                            }else{
                                if ($type == BarCode::SINGEL_PRODUCT) {
                                    if (substr($this->q, 2, 2) == BarCode::OUR_WAREHOUSE) {
                                        $gearItem = GearItem::find()->where(['id'=>$id])->andWhere(['active'=>1])->one();
                                        $gear_id = $gearItem->gear_id;
                                        $this->activeModel = $gearItem->gear_id;
                                        $this->activeGroup = $gearItem->group_id;
                                    }
                                }else{
                                 if ($type == BarCode::MODEL) {
                                    if (substr($this->q, 2, 2) == BarCode::OUR_WAREHOUSE) {
                                        $gear = Gear::find()->where(['id'=>$id])->andWhere(['active'=>1])->one();
                                        $gear_id = $gear->id;
                                        }
                                    }                               
                                }
                            }                           
                        }

                        $gearQuery->andFilterWhere(['id'=>$gear_id]);
                    }else{
                        if ($this->q)
                        {
                            $ids = ArrayHelper::map(GearItem::find()->where(['like', 'rfid_code', $this->q])->asArray()->all(), 'gear_id', 'gear_id');
                            $ids2 = ArrayHelper::map(GearItemsNoItemsRfid::find()->where(['like', 'rfid_code', $this->q])->asArray()->all(), 'gear_id', 'gear_id');
                            $ids = array_merge($ids, $ids2);
                            $gearQuery->andWhere(['or', ['like', 'name', $this->q], ['IN', 'id', $ids]]);
                        }
                        
                    }
                    
            }

        }
        
        if (!$pagination)
        {
                $this->_gearDataProvider = new ActiveDataProvider([
            'query'=>$gearQuery,
            'pagination'=>false,
            'sort'=>[
                'defaultOrder' => ['sort_order'=>SORT_ASC],
            ]
        ]);
        }else{
            $this->_gearDataProvider = new ActiveDataProvider([
            'query'=>$gearQuery,
            //'pagination'=>false,
            'sort'=>[
                'defaultOrder' => ['sort_order'=>SORT_ASC],
            ]
        ]);
        }
        

//        //Egzemparze
        $modelIds = $gearQuery->column();
        if ($this->activeModel){
        if ($gearItem)
        {
            $gearItemQuery = GearItem::find()
            ->andWhere([
                'gear_item.active' => 1,
                'gear_id'=>$this->activeModel,
                'gear_item.id'=>$gearItem->id
            ])->orderBy(['number'=>SORT_ASC]);

        $gearNoGroupItemQuery = GearItem::find()
            ->andWhere([
                'active' => 1,
                'group_id' =>null,
                'gear_id'=>$this->activeModel,
                'gear_item.id'=>$gearItem->id
              //  'gear_item.status' => GearItem::STATUS_ACTIVE,
            ])->orderBy(['number'=>SORT_ASC]);
        }else{
                        $gearItemQuery = GearItem::find()
            ->andWhere([
                'gear_item.active' => 1,
                'gear_id'=>$this->activeModel,
            ])->orderBy(['number'=>SORT_ASC]);
                    $gearNoGroupItemQuery = GearItem::find()
            ->andWhere([
                'active' => 1,
                'group_id' =>null,
                'gear_id'=>$this->activeModel,
              //  'gear_item.status' => GearItem::STATUS_ACTIVE,
            ])->orderBy(['number'=>SORT_ASC]);
        }

        $this->_gearItemDataProvider = new ActiveDataProvider([
            'query'=>$gearNoGroupItemQuery,
            'pagination'=>false,
            'sort'=>false,
        ]);
                //Zestaw
        if(isset($this->gear_query)){
            $itemIds = $gearItemQuery
                ->column();
        } else {
            $itemIds = $gearItemQuery
                ->column();
        }
        $gearItemQuery = GearGroup::find()
            ->innerJoinWith('gearItems')
            ->andWhere([
                'gear_group.active'=>1,
                'gear_item.active' => 1,
                'gear_item.id' =>$itemIds,
            ]);

        $this->showGroups = $gearItemQuery->count() > 0 ? true : false;

        $this->_gearGroupDataProvider = new ActiveDataProvider([
            'query'=>$gearItemQuery,
            'pagination'=>false,
            'sort'=>false,
        ]);
        if ($gearItem)
        {
                    $activeGroupItemsQuery = GearItem::find()
            ->where([
                'active' => 1,
                'group_id'=>$this->activeGroup,
                'id'=>$gearItem->id
            ])->orderBy(['number'=>SORT_ASC]);
        }else{
                    $activeGroupItemsQuery = GearItem::find()
            ->where([
                'active' => 1,
                'group_id'=>$this->activeGroup,
            ])->orderBy(['number'=>SORT_ASC]);
        }


        $this->_gearGroupItemDataProvider = new ActiveDataProvider([
            'query'=>$activeGroupItemsQuery,
            'pagination'=>false,
            'sort'=>false,
        ]);
        }else{
            $this->showGroups = false;

        $this->_gearGroupDataProvider = null;
        $this->_gearGroupItemDataProvider = null;
         $this->_gearItemDataProvider  = null;
        }





    }

    /**
     * @param $model Gear;
     * @return int
     */
    public function getGearAvailableCount($model)
    {
        /*$count = 0;
        $unavailableData = $this->getUnavailableData();
        if ($model->no_items==1)
        {
            $index = $model->id.'.'.$model->getNoItemsItem()->id.'.quantity';
            $used = ArrayHelper::getValue($unavailableData, $index, 0);
            $count = $model->quantity - $used;

        }
        else
        {
            $all = $model->getGearItems()->andWhere(['active'=>1])->all();

            $all_count = 0;
            foreach ($all as $gear_item) {
                if (GearService::getCurrentModel($gear_item->id) == null) {
                    $all_count++;
                }
            }

            $used = ArrayHelper::getValue($unavailableData, $model->id, []);
            $usedNumber = count($used);
            foreach ($used as $id => $use) {
                if (GearItem::findOne($id)->active == 0) {
                    $usedNumber--;
                }
            }
            $count = $all_count - $usedNumber;
        }

        if ($count < 0) {
            return 0;
        }*/
        $count = $model->getAvailabe($this->from_date, $this->to_date);
        return $count;

    }

    public function getGearItemAvailableCount($model)
    {
        return '---';
    }

    public function getGearDataProvider($pagination = true)
    {
        if ($this->_gearDataProvider === null)
        {
            $this->_setDataProviders($pagination);
        }
        return $this->_gearDataProvider;
    }

    public function getGearItemDataProvider()
    {
        if ($this->_gearItemDataProvider === null)
        {
            $this->_setDataProviders();
        }
        return $this->_gearItemDataProvider;
    }

    public function getGearGroupDataProvider()
    {
        if ($this->_gearGroupDataProvider === null)
        {
            $this->_setDataProviders();
        }
        return $this->_gearGroupDataProvider;
    }

    public function getGearGroupItemDataProvider()
    {
        if ($this->_gearGroupItemDataProvider === null)
        {
            $this->_setDataProviders();
        }
        return $this->_gearGroupItemDataProvider;
    }

        public function searchInWarehouse($params, $warehouse)
    {
            $categories = array_reverse($this->categories);
            $sub = null;
            foreach ($categories as $cat)
            {
                if ($cat !==null)
                {
                    $sub = $cat;
                    break;
                }
            }

            $categoryIds = [];
            $ids = [];
            $tmpCat = GearCategory::findOne($sub);

            if ($tmpCat !== null)
            {
                $ids = $tmpCat->children()->column();
            }

            $categoryIds = array_merge([$sub], $ids);
            if ($this->q)
                {
                                    $gearQuery = Gear::find()
                    ->andWhere(['active'=>1])
                    ->andWhere(['visible_in_warehouse'=>1]);
                }else{
                                    $gearQuery = Gear::find()
                    ->andWhere(['active'=>1])
                    ->andWhere(['visible_in_warehouse'=>1])
                    ->andFilterWhere([
                        'category_id'=>$categoryIds,
                    ]);
                }
            $gearQuery->innerJoinWith('warehouseQuantities')->andWhere(['warehouse_id'=>$warehouse->id])->andWhere(['>', 'warehouse_quantity.quantity', 0]);

            if ($this->favorite)
                {
                    $ids = ArrayHelper::map(GearFavorite::find()->where(['user_id'=>Yii::$app->user->id])->asArray()->all(), 'gear_id', 'gear_id');
                    $gearQuery->innerJoinWith('gearFavorite')->andWhere(['gear.id'=>$ids])->orderBy(['gear_favorite.position'=>SORT_ASC]);
                }
                                if ((strlen($this->q)==13)&&(is_numeric($this->q[0]))&&(is_numeric($this->q[5])))
                    {
                        $gear_id=0;
                        $id = (int)substr($this->q, 4, 9);
                        $type = substr($this->q, 0, 2);
                        if ($type == BarCode::ITEMS_GROUP) {
                            $gearItem = GearItem::find()->where(['group_id'=>$id])->andWhere(['active'=>1])->one();
                            $gear_id = $gearItem->gear_id;
                        }else{
                            if ($type == BarCode::SINGEL_PRODUCT) {
                                if (substr($this->q, 2, 2) == BarCode::OUR_WAREHOUSE) {
                                    $gearItem = GearItem::find()->where(['id'=>$id])->andWhere(['active'=>1])->one();
                                    $gear_id = $gearItem->gear_id;
                                    $this->activeModel = $gearItem->gear_id;
                                    $this->activeGroup = $gearItem->group_id;
                                }
                            }else{
                             if ($type == BarCode::MODEL) {
                                if (substr($this->q, 2, 2) == BarCode::OUR_WAREHOUSE) {
                                    $gear = Gear::find()->where(['id'=>$id])->andWhere(['active'=>1])->one();
                                    $gear_id = $gear->id;
                                    }
                                }                               
                            }
                        }
                        $gearQuery->andFilterWhere(['id'=>$gear_id]);
                    }else{
                        if ($this->q)
                        {
                            $ids = ArrayHelper::map(GearItem::find()->where(['like', 'rfid_code', $this->q])->asArray()->all(), 'gear_id', 'gear_id');
                            $ids2 = ArrayHelper::map(GearItemsNoItemsRfid::find()->where(['like', 'rfid_code', $this->q])->asArray()->all(), 'gear_id', 'gear_id');
                            $ids = array_merge($ids, $ids2);
                            $gearQuery->andWhere(['or', ['like', 'name', $this->q], ['IN', 'id', $ids]]);
                        }
                        
                    }
       return new ActiveDataProvider([
            'query'=>$gearQuery,
            //'pagination'=>false,
            'sort'=>[
                'defaultOrder' => ['sort_order'=>SORT_ASC],
            ]
        ]);
    }
}
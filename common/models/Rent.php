<?php

namespace common\models;

use kartik\helpers\Html;
use common\helpers\ArrayHelper;
use kartik\icons\Icon;
use Yii;
use \common\models\base\Rent as BaseRent;
use yii\behaviors\BlameableBehavior;
use yii\data\ActiveDataProvider;
use common\models\interfaces\EventInterface;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;

/**
 * This is the model class for table "rent".
 */
class Rent extends BaseRent implements EventInterface
{
    const STATUS_READY = 1;
    const STATUS_BOOKED = 5;
    const STATUS_DELIVERED = 10;
    const STATUS_RETURNED = 20;
    const READY_TO_INVOICE = 30;
    const INVOICE = 40;
    const STATUS_DONE = 50;
    const STATUS_OUTDATED = 99;

    public $dateRange;

    public function behaviors()
    {
        $behaviors = [
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => false,
            ],
            'codeBehavior' => [
                'class'=>\common\behaviors\CodeBehavior::className(),
                'prefix' => 'W',
            ],
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }

    public function prepareForCalendar()
    {
        $description = $this->name."<br/>";
        if ($this->manager)
            $description.=Yii::t('app', 'Odpowiedzialny: ').$this->manager->displayLabel;
            $users = "[";

            $description .= "<br/>".$this->description;
        $users .="]";
        $whole = false;
            if ((substr($this->end_time, 11, 8)==substr($this->start_time, 11, 8))&&(substr($this->start_time, 11, 8)=="00:00:00"))
            {
                $whole = true;
            }

        $att = 0;
        $notes = 0;
        return ['title'=> $this->name, 'type'=>'rent', 'id'=>$this->id, 'start'=>substr($this->start_time, 0, 10)."T".substr($this->start_time, 11, 8), 'end'=>substr($this->end_time, 0, 10)."T".substr($this->end_time, 11, 8), 'className'=>'rent typ-'.$this->type.' status-'.$this->status, 'notes'=>$notes, 'users'=>$users, 'files'=>$att, 'allDay'=>$whole, 'description'=>$description];
    }

        public function getGearWeight()
    {
        $sum = 0;
        $gears = RentGear::find()->where(['rent_id'=>$this->id])->all();
        foreach ($gears as $eg) {
            if ($eg->gear->no_items)
            {
                $sum +=$eg->quantity*$eg->gear->weight;
                $sum +=$eg->gear->getWeightCase($eg->quantity);
            }else{
                $sum +=$eg->quantity*$eg->gear->weight;
                $sum +=$eg->gear->getWeightCase($eg->quantity);
            }
        }
        return $sum;
    }

        public function getGearVolume()
    {
        $gears = RentGear::find()->where(['rent_id'=>$this->id])->all();
        $sum = 0;
        foreach ($gears as $eg) {
            if ($eg->gear->no_items)
            {
                $sum +=$eg->gear->countVolume2($eg->quantity);
               // echo $eg->gear->name." x".$eg->quantity." = ".$eg->gear->countVolume2($eg->quantity)."</br>";
            }else{
                $items = ArrayHelper::map(GearItem::find()->where(['gear_id'=>$eg->gear_id])->all(), 'id', 'id');
                $eventGearItems = RentGearItem::find()->where(['rent_id'=>$this->id])->andWhere(['IN', 'gear_item_id', $items])->count();  
                $count =  $eg->quantity - $eventGearItems;
                if ($eventGearItems>0)
                {
                    //liczymy dodane egzemplarze i case
                    $ids = ArrayHelper::getColumn($this->getGearItems()->where(['IN', 'id', $items])->all(), 'group_id');
                    $cases = GearGroup::find()->where(['IN', 'id', $ids])->all();
                    $gearNoCase = $this->getGearItems()->where(['group_id'=>null])->andWhere(['IN', 'id', $items])->all();
                    $volumeCase = array_sum(ArrayHelper::getColumn($cases, 'calculatedVolume'));
                    $volumeNoCase = array_sum(ArrayHelper::getColumn($gearNoCase, 'calculatedVolume'));
                    $sum+=$volumeCase+$volumeNoCase;

                }
                if ($count>0)
                {
                    //jesli sprzęt został dodany ilościowo to liczymy tak mniej więcej
                    $sum +=$eg->gear->countVolume2($count);
                   // echo $eg->gear->name." x".$eg->quantity." = ".$eg->gear->countVolume2($eg->quantity)."</br>";
                }

            }


        }

        return $sum;
    }

public function getAssignedOuterGearModelNumber($event_id, $outer_gear_id) {
        $model = RentOuterGearModel::findOne(['rent_id' => $event_id, 'outer_gear_model_id' => $outer_gear_id]);
        if ($model == null) {
            return 0;
        }
        if ($model->quantity == null) {
            return 1;
        }
        return $model->quantity;
    }


    public static function getStatusList($status=null)
    {
        if ($status)
        {
            $s = \common\models\EventStatut::findOne($status);
            if ($s->blocks_status_revert)
            {
                $list = ArrayHelper::map(\common\models\EventStatut::find()->where(['type'=>2, 'active'=>1])->andWhere(['>=', 'position', $s->position])->orderBy(['position'=>SORT_ASC])->asArray()->all(), 'id', 'name');
                return $list;
            }
        }
        $list = ArrayHelper::map(\common\models\EventStatut::find()->where(['type'=>2, 'active'=>1])->orderBy(['position'=>SORT_ASC])->asArray()->all(), 'id', 'name');
        return $list;
    }


    public function getStatusButton()
    {
        $status = EventStatut::findOne($this->status);
        if ($status)
            return '<span class="label label-primary" style="background-color:'.$status->color.';"">'.$status->name.'</span>';
        else
            return "-";
    }

    public function getBlocks($type)
    {
        if (isset($this->eventStatut))
        {
            if ($type=="event")
            {
                if ($this->eventStatut->blocks_event)
                {
                    return true;
                }else{
                    return false;
                }
            }
            if ($type=="revert")
            {
                if ($this->eventStatut->blocks_status_revert)
                {
                    return true;
                }else{
                    return false;
                }
            }
            if ($type=="cost")
            {
                if ($this->eventStatut->blocks_costs)
                {
                    return true;
                }else{
                    return false;
                }
            }
            if ($type=="gear")
            {
                if ($this->eventStatut->blocks_gear)
                {
                    return true;
                }else{
                    return false;
                }
            }
            if ($type=="working")
            {
                if ($this->eventStatut->blocks_working_times)
                {
                    return true;
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
    }

    public function getStatusIcon()
    {
        if (isset($this->eventStatut)){
            return "<i class='fa ".$this->eventStatut->icon."'></i>";
        }
        return "";

    }

    public function getStatusBorder()
    {
        if (isset($this->eventStatut)){
            return $this->eventStatut->border."px solid ".$this->eventStatut->color;
        }
        return "";

    }

    public function getStatusLabel()
    {
        $list = static::getStatusList();
        $index = $this->status;
        return isset($list[$index]) ? $list[$index] : UNDEFINDED_STRING;
    }

    public function rules()
    {
        $rules = [
            ['dateRange', 'string'],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function prepareDateAttributes()
    {
        $this->dateRange = $this->start_time.' - '.$this->end_time;
    }

    public function attributeLabels()
    {
        $labels = [
            'dateRange' => Yii::t('app', 'Od - do'),
        ];

        return array_merge(parent::attributeLabels(), $labels);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        RentGearItem::updateTimesForRent($this);
        if ($insert){
            Note::createNote(5, 'rentCreate', $this, $this->id);
            $this->addLog(Yii::t('app', 'Utworzono wypożyczenie'));
            if (isset($this->customer_id)){
                $customer = Customer::findOne($this->customer_id);     
                if ($customer)
                    $customer->createLog('rent_create', $this->id);
            }
        }else{
            if (((isset($changedAttributes['start_time']))&&($changedAttributes['start_time']!=$this->start_time))||((isset($changedAttributes['end_time']))&&($changedAttributes['end_time']!=$this->end_time)))
                    Note::createNote(5, 'rentScheduleChanged', $this, $this->id);
            if ((isset($changedAttributes['status']))&&($changedAttributes['status']!=$this->status))
                    Note::createNote(5, 'rentStatus', $this, $this->id);

             if ((isset($changedAttributes['status']))&&($this->status!=$changedAttributes['status']))
            {
                //wysyłamy powiadomienie
                $this->sendStatusReminder();
                $this->changeBookings();
            }
        }

    }

        public function changeBookings()
    {
        if ($this->eventStatut->delete_gear)
        {
            //usuwamy wszystkie rezerwacje
            RentGear::deleteAll(['rent_id'=>$this->id]);
            RentGearItem::deleteAll(['rent_id'=>$this->id]);
            RentOuterGear::deleteAll(['rent_id'=>$this->id]);
            RentOuterGearModel::deleteAll(['rent_id'=>$this->id]);
        }
                if ($this->eventStatut->delete_task)
        {
            
            Task::deleteAll(['rent_id'=>$this->id]);
        }

    }

    public function sendStatusReminder()
    {
        $text = Yii::t('app', 'Zmieniono status wypożyczenia ').$this->name.Yii::t('app', ' na ').$this->eventStatut->name;
        $userIds = explode(";",$this->eventStatut->reminder_users);
        
        $users = User::find()->where(['IN', 'id', $userIds])->all();
        foreach ($users as $user)
        {
            if ($this->eventStatut->reminder)
            {
                    Notification::sendUserPushNotification($user, Yii::t('app', 'Powiadomienie z serwisu eventowego'), $text, Notification::PUSH_TYPE_EVENTS, $this->id);
            }
            if ($this->eventStatut->reminder_sms)
            {
                Notification::sendUserSmsNotification($user, $text, date("Y-m-d H:i:s"));
            }
            if ($this->eventStatut->reminder_mail)
            {
                Notification::sendUserMailNotification($user, Yii::t('app', 'Wiadomość automatyczna'), $text." ".Html::a(Yii::t('app', 'Zobacz'), "http://".Yii::$app->getRequest()->serverName.'/admin/rent/view?id='.$this->id ));
            }            
        }

    }

    public function beforeDelete()
    {
        Note::createNote(5, 'rentDelete', $this, $this->id);
        return true;
    }

    public static function assignOuterGearModel($id, $itemId, $quantity=null, $discount=null)
    {
        $model = RentOuterGearModel::findOne(['rent_id'=>$id, 'outer_gear_model_id'=>$itemId]);
        if ($model===null)
        {
            $model = new RentOuterGearModel();
        }
        $model->rent_id = $id;
        $model->outer_gear_model_id = $itemId;
        $model->quantity = $quantity;
        return $model->save();
    }

    public static function assignGearItem($id, $itemId, $quantity=null, $params=[])
    {
        $model = RentGearItem::findOne(['rent_id'=>$id, 'gear_item_id'=>$itemId]);
        if ($model===null)
        {
            $model = new RentGearItem();
        }
        $model->rent_id = $id;
        $model->gear_item_id = $itemId;
        $model->quantity = $quantity;
        $model->attributes = $params;

        $available = false;

        $start = ArrayHelper::getValue($params, 'start_time', false);
        $end = ArrayHelper::getValue($params, 'end_time', false);
        if ($start != false && $end != false)
        {
            $available = $model->gearItem->isAvailableInRange($start, $end);
        }
        else
        {
            $owner = static::findOne($id);
            $available = $model->gearItem->isAvailable($owner);
        }

        if ( $available == true && $model->gearItem->status == GearItem::STATUS_ACTIVE)
        {
            $model->rent->addLog(Yii::t('app', 'Zarezerwowano sprzęt ').$model->gearItem->name."[".$model->gearItem->number."]");
            return $model->save();
            
        }
        else
        {
            return false;
        }
    }

    public static function assignGear($id, $itemId, $quantity=null, $params = [])
    {
        $model = RentGear::findOne(['rent_id'=>$id, 'gear_id'=>$itemId]);
        $old_quantity = 0;
        if (!$model)
        {
            $model = new RentGear();
            $old_quantity = 0;
        }else{
            $old_quantity= $model->quantity;
        }
        $model->rent_id = $id;
        $model->gear_id = $itemId;
        $model->quantity = $quantity;
        $model->attributes = $params;
        $available = false;
        $start = $model->rent->start_time;
        $end = $model->rent->end_time;
        $model->start_time = $start;
        $model->end_time = $end;
        if ($model->gear->type==2)
        {
            return $model->save();
        }
        if ($model->gear->type==3)
        {
            $available = $model->gear->quantity+$old_quantity;
            if ( $available >= $quantity)
            {
                $model->gear->quantity = $model->gear->quantity+$old_quantity-$quantity;
                $model->gear->save();
                return $model->save();
            }else{
                return false;
            }
        }
        if ($start != false && $end != false)
        {
            $available = $model->gear->getAvailabe($start, $end)+$old_quantity;
        }
        else
        {
            $available = $model->gear->getAvailabe($start, $end)+$old_quantity;
        }
        $serwisNumber = 0;
        foreach ($model->gear->gearItems as $item) {
        if ($item->active == 1 && $item->status === GearItem::STATUS_SERVICE) {
                $serwisNumber++;
            }
        }
        $available = $available-$serwisNumber;
        if ( $available >= $quantity)
        {
            $model->rent->addLog(Yii::t('app', 'Zarezerwowano sprzęt ').$model->gear->name."[ x".$model->quantity."]");
            
            return $model->save();
            
        }
        else
        {
            return false;
        }

    }

    public static function removeGearItem($id, $itemId)
    {
        return RentGearItem::deleteAll(['rent_id'=>$id, 'gear_item_id'=>$itemId]);
    }

    public static function removeGear($id, $itemId)
    {
        $model =  RentGear::find()->where(['rent_id'=>$id, 'gear_id'=>$itemId])->one();
        foreach (GearItem::find()->where(['gear_id' => $itemId])->all() as $gearItem) {
            $gears = RentGearItem::find()->where(['rent_id' => $id])->andWhere(['gear_item_id' => $gearItem->id])->all();
            foreach ($gears as $gear) {
                        $gear->delete();
            }
        }
        $count = $model->quantity;
        $model->rent->addLog(Yii::t('app', 'Usunięto rezerwację sprzętu ').$model->gear->name."[ x".$model->quantity."]");
        $model->delete();
        return $count;
    }

    public function getAssignedGear($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getGearItems();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public static function getAssignedQuantities($id)
    {
        $data = RentGearItem::find()
            ->where(['rent_id'=>$id])
//            ->asArray()
            ->all();
        $list = ArrayHelper::map($data, 'gear_item_id', 'quantity');
        return $list;
    }

    public static function getAssignedGearQuantities($id)
    {
        $data = RentGear::find()
            ->where(['rent_id'=>$id])
            ->all();
        $list = ArrayHelper::map($data, 'gear_id', 'quantity');
        return $list;
    }

    public function getTimeStart()
    {
        $list = $this->_getEventTimes(0);
        $index = 0;

        return ArrayHelper::getValue($list, $index, null);
    }

    public function getTimeEnd()
    {
        $list = $this->_getEventTimes(1);
        $index = count($list) - 1;
        return ArrayHelper::getValue($list, $index, null);
    }

    public function getClassType()
    {
        return 'rent';
    }
    public static function getClassTypeLabel()
    {
        return Yii::t('app', 'Wypożyczenie');
    }

    /**
     * @param bool|integer $type false - all, 0 - start, 1 - end
     */
    protected function _getEventTimes($type=false)
    {
        $start = [
            $this->start_time,
            $this->deliver_time,
        ];
        $end = [
            $this->end_time,
            $this->return_time,
        ];

        $start = array_filter($start);
        $end = array_filter($end);

        $list = [];
        switch ($type)
        {
            default:
                $list = array_merge($start, $end);
                break;
            case 0:
                $list = $start;
                break;
            case 1:
                $list = $end;
                break;
        }
        $list = ArrayHelper::sortDates($list);
        return $list;
    }

    public function getTimeRange($separator = ' - ', $format='short')
    {
        $formatter = Yii::$app->formatter;

        $start = $this->getTimeStart();
        $end = $this->getTimeEnd();

        return $formatter->asDatetime($start, $format).$separator.$formatter->asDatetime($end, $format);
    }

    public function getTooltipContent()
    {
        $info = Html::tag('strong',$this->name);

        $info .= Html::tag('div', Yii::t('app', 'Termin').':<br />'. $this->getTimeRange());
        $info .= Html::tag('hr');
        if ($this->customer !== null)
        {
            $info .= Html::tag('strong', Yii::t('app', 'Klient').': ').$this->customer->getDisplayLabel();
            if ($this->contact !== null)
            {
                $info .= Html::tag('div', $this->contact->getDisplayLabel());
            }
//            $info .= Html::tag('hr');
        }
//        $info .= Html::tag('div', 'Dodał: '. $this->creator->getDisplayLabel());
        return $info;
    }


    // @return sprzęt wydany z magazynu dla danego eventu
    public function getGearsSpendFromWarehouse() {
        $result = ['gears'=>[], 'gearsOuter'=>[], 'gearsGroup'=>[]];
        $outcomes_for_event = OutcomesForRent::find()->where(['rent_id' => $this->id])->all();
        foreach ($outcomes_for_event as $outcome_for_event) {
            $outcomes = OutcomesWarehouse::find()->where(['id' => $outcome_for_event->outcome_id])->all();
            foreach ($outcomes as $outcome) {
                $gears = OutcomesGearOur::find()->where(['outcome_id' => $outcome->id])->all();
                foreach ($gears as $gear) {
                    if ($gear) {
                        if (isset($result['gears'][$gear->gear_id])) {
                            $result['gears'][$gear->gear_id] += $gear->gear_quantity;
                        }
                        else {
                            $result['gears'][$gear->gear_id] = $gear->gear_quantity;
                        }
                    }
                }
            }
        }

        return $result;
    }

    // @return sprzęt zwrócony z magazynu dla danego eventu
    public function getGearsReturnedToWarehouse() {
        $result = ['gears'=>[], 'gearsOuter'=>[], 'gearsGroup'=>[]];
        $incomes_for_event = IncomesForRent::find()->where(['rent_id' => $this->id])->all();
        foreach ($incomes_for_event as $income_for_event) {
            $incomes = IncomesWarehouse::find()->where(['id' => $income_for_event->income_id])->all();
            foreach ($incomes as $income) {
                $gears = IncomesGearOur::find()->where(['income_id' => $income->id])->all();
                $gearsOuter = IncomesGearOuter::find()->where(['income_id' => $income->id])->all();
                foreach ($gears as $gear) {
                    if ($gear) {
                        if (isset($result['gears'][$gear->gear_id])) {
                            $result['gears'][$gear->gear_id] += $gear->quantity;
                        }
                        else {
                            $result['gears'][$gear->gear_id] = $gear->quantity;
                        }
                    }
                }
                foreach ($gearsOuter as $gear) {
                    if ($gear) {
                        if (isset($result['gearsOuter'][$gear->outer_gear_id])) {
                            $result['gearsOuter'][$gear->outer_gear_id] += $gear->gear_quantity;
                        }
                        else {
                            $result['gearsOuter'][$gear->outer_gear_id] = $gear->gear_quantity;
                        }
                    }
                }
            }
        }

        return $result;
    }

    // @return [nie_zwrocone_gear, nie_zwrocone_magazyn_zewnetrzny, nie_zwrocone_gear_group]
    public function getWarehouseGearDifference() {
        $gearsOut = $this->getGearsSpendFromWarehouse();
        $gearsOurOut = $gearsOut['gears'];

        $gearsIn = $this->getGearsReturnedToWarehouse();
        $gearsOurIn = $gearsIn['gears'];

        // dla każdego wydanego sprzętu sprawdzamy czy została zwrócona taka sama ilość
        foreach ($gearsOurOut as $gear_id => $quantity) {
            if (isset($gearsOurIn[$gear_id])) {
                $gearsOurOut[$gear_id] -= $gearsOurIn[$gear_id];
            }
        }
        foreach ($gearsOurOut as $gear_id => $quantity) {
            if ($quantity <= 0) {
                unset($gearsOurOut[$gear_id]);
            }
        }

        return [$gearsOurOut];
    }

    public function countNotReturnedGears() {
        $not_returned = \common\models\RentGearOutcomed::find()->where(['rent_id'=>$this->id])->andWhere(['>', 'quantity', 0])->count();
        return $not_returned;
    }


	public function getDisplayLabel()
	{
		$label = $this->name.' ['.$this->code.']';
		return $label;
	}

    public function getEventValueSum()
    {
        $values = $this->getEventValueAll();
        return $values[Yii::t('app', 'Suma')];
    }

	public function getEventValue()
	{
        $profit = [
            Yii::t('app', 'Suma')=>0,
        ];
        $offersData = [];
        $offers = $this->getOffersAccepted();
        foreach ($offers as $offer)
        {
            $offersData[] = $offer->getSummary();
            foreach ($offer->getSummary() as $key=>$val)
            {
                $profit = ArrayHelper::setKey($profit, $key);
                $profit[$key] += $val;
            }

        }

        $sum = $profit[Yii::t('app', 'Suma')];
        unset($profit[Yii::t('app', 'Suma')]);
        ksort($profit, SORT_NATURAL);
        $profit[Yii::t('app', 'Suma')] = $sum;

        return $profit;
	}

    public function getOffersAccepted()
    {
        $statuts = ArrayHelper::map(OfferStatut::find()->where(['visible_in_finances'=>1])->asArray()->all(), 'id', 'id');
        $models = $this->getOffers()
            ->where([
                'in', 'status', $statuts,
            ])
            ->all();
        return $models;
    }

    public static function getList()
    {
        $model = Rent::find()->all();

        $list = ArrayHelper::map($model, 'id', 'displayLabel');
        return $list;
    }

    public function getPlaceholderMap()
    {
        $formatter = Yii::$app->formatter;
        $map = [
            'name' => $this->name,
            'timeStart'=>$formatter->asDatetime($this->getTimeStart(), 'short'),
            'timeEnd'=>$formatter->asDatetime($this->getTimeEnd(), 'short'),
            'link' => Html::a('link', Url::to(['/rent/view', 'id'=>$this->id], true)),
        ];

        return $map;
    }

    public function getAssignedOffers($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getOffers();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAcceptedOffers()
    {
        $offers =  $this->offers;
        $result = [];
        foreach ( $offers as $offer) {
            if ($offer->status == 1) {
                $result[] = $offer;
            }
        }
        if (count($result) > 0) {
            return $result;
        }
        if (count($result) == 0) {
            return ['error' => 1];
        }
        return ['error' => 2];
    }

    public function getAssignedGearModel($params = []) {
        $gear_category = [];

        $ids = [];
        $gears = RentGear::find()->where(['rent_id'=>$this->id])->all();
        foreach ($gears as $gear) {

                $category = $gear->gear->category;
                if (!$category->parents())
                {
                    $gear_category[$category->id][] = $gear;
                }else{

                    $categories = $category->parents()->all();
                    if (count($categories) > 1) {
                        $category = $categories[1];
                    }
                    $gear_category[$category->id][] = $gear;                    
                }

        }

        $gears = [];
        foreach ($gear_category as $category => $items) {
            foreach ($items as $item) {
                $gears[] = $item;
            }
        }

        $provider = new ArrayDataProvider([
            'allModels' => $gears,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $provider;
    }

    public function getInvoicesDataProvider()
    {

        $query = $this->getInvoices();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getInvoices()
    {
        return $this->hasMany(Invoice::className(), ['owner_id'=>'id'])->andWhere(['invoice.owner_type'=>Invoice::OWNER_TYPE_RENT]);
    }

    public function addLog($content)
    {
         date_default_timezone_set(Yii::$app->params['timeZone']);
        $log = new RentLog;
        $log->user_id = Yii::$app->user->identity->id;
        $log->rent_id = $this->id;
        $log->content = $content;
        $log->create_time = date("Y-m-d H:i:s");
        $log->save();
        return true;
    }

    public function getLogs()
    {
        return $this->hasMany(\common\models\RentLog::className(), ['rent_id' => 'id'])->orderBy(['create_time' => SORT_DESC]);
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

    public function getOtherTasks()
    {
        $tasks = Task::find()->where(['rent_id'=>$this->id])->andWhere(['is', 'task_category_id', null])->orderBy(['order'=>SORT_ASC])->all();
        return $tasks;
    }

    public function deleteAllTasks()
    {
        $tasks = Task::find()->where(['rent_id'=>$this->id])->all();
        foreach ($tasks as $task)
        {
            $task->delete();
        }
    }

    public function copyTasks()
    {
        if ($this->tasks_schema_id)
        {
            $tasksSchema = TasksSchema::findOne($this->tasks_schema_id);
            foreach ($tasksSchema->tasksSchemaCats as $category)
            {
                $cat = new TaskCategory;
                $cat->name = $category->name;
                $cat->order = $category->order;
                $cat->rent_id = $this->id;
                $cat->color = $category->color;
                $cat->save();
                foreach ($category->taskSchemas as $schema)
                {
                    $task = new Task;
                    $task->title = $schema->name;
                    $task->content = $schema->description;
                    $task->order = $schema->order;
                    $task->task_category_id = $cat->id;
                    $task->rent_id = $this->id;
                    $task->only_one = $schema->only_one;
                    if (($schema->time_type!=1)&&($this->getTimeStart()))
                    {
                        $secs = 3600*$schema->hours+60*$schema->minutes+24*3600*$schema->days;
                        if ($schema->time_type<4)
                        {
                            $start = $this->getTimeStart();
                            $rok = substr($start, 0, 4);
                            $miesiac = substr($start, 5, 2);
                            $dzien = substr($start, 8, 2);
                            $godzina = substr($start, 11, 2);
                            $time = mktime($godzina, 0, 0, $miesiac, $dzien, $rok);

                        }else{
                            $start = $this->getTimeEnd();
                            $rok = substr($start, 0, 4);
                            $miesiac = substr($start, 5, 2);
                            $dzien = substr($start, 8, 2);
                            $godzina = substr($start, 11, 2);
                            $time = mktime($godzina, 0, 0, $miesiac, $dzien, $rok);
                        }
                        if (($schema->time_type==2)||($schema->time_type==4))
                        {
                            $time = $time-$secs;
                        }else{
                            $time = $time+$secs;
                        }
                        $task->datetime = date("Y-m-d H:i:s", $time);
                    }
                    $task->save();
                    foreach ($schema->users as $user)
                    {
                        $tu = new UserTask;
                        $tu->task_id = $task->id;
                        $tu->user_id = $user->id;
                        $tu->save();
                    }
                    if (($schema->manager)&&($this->manager_id)){
                        $tu = new UserTask;
                        $tu->task_id = $task->id;
                        $tu->user_id = $this->manager_id;
                        $tu->save();
                    }
                    foreach ($schema->roles as $role)
                    {
                        $tr = new TaskRole;
                        $tr->task_id = $task->id;
                        $tr->user_event_role_id = $role->id;
                        $tr->save();
                    }
                    foreach ($schema->notificationUsers as $user)
                    {
                        $tu = new TaskNotificationUser;
                        $tu->task_id = $task->id;
                        $tu->user_id = $user->id;
                        $tu->save();
                    }
                    if (($schema->manager_notification)&&($this->manager_id)){
                        $tu = new TaskNotificationUser;
                        $tu->task_id = $task->id;
                        $tu->user_id = $this->manager_id;
                        $tu->save();
                    }
                    foreach ($schema->notificationRoles as $role)
                    {
                        $tr = new TaskNotificationRole;
                        $tr->task_id = $task->id;
                        $tr->user_event_role = $role->id;
                        $tr->save();
                    }
                    foreach ($schema->taskSchemaNotifications as $no)
                    {
                        $not = new TaskNotification;
                        $not->task_id = $task->id;
                        $not->time_type = $no->time_type;
                        $not->time = $no->time;
                        $not->email = $no->email;
                        $not->sms = $no->sms;
                        $not->push = $no->push;
                        $not->text = $no->text;
                        $not->sent = 0;
                        $not->save();
                    }

                }
            }
        }
    }

    public function getTaskStatus()
    {
        $task = Task::find()->where(['rent_id'=>$this->id])->count();
        $task_done =  Task::find()->where(['rent_id'=>$this->id])->andWhere(['status'=>10])->count();
        if ($task==0)
            return ['task'=>$task, 'done'=>$task_done, 'status'=>0];
        else
            return ['task'=>$task, 'done'=>$task_done, 'status'=>intval($task_done/$task*100)];
    }

    public function getAssignedGearsArray()
    {
        $data = [];
        foreach ($this->rentGears as $gear)
        {
            $tmp['id'] = $gear->gear->id;
            $tmp['name'] = $gear->gear->name;
            $tmp['quantity'] = $gear->quantity;
            $tmp['photo'] = $gear->gear->photo;
            $tmp['packing'] = $gear->gear->getPacking2();
            $tmp['start'] = $gear->start_time;
            $tmp['end'] = $gear->end_time;
            $category = $gear->gear->category;
            $categories = $category->parents()->all();
            if (count($categories) > 1) {
                    $category_name = $categories[1]->name;
            }else{
                    $category_name = $category->name;
            }
            $tmp['category'] = $category_name;
            $data[] = $tmp;
        }
        return $data;
    }

    public function getAssignedAttachements($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getRentAttachments();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedOuterGearModelsNumber()
    {
        $data = RentOuterGearModel::find()->where(['resolved'=>0, 'rent_id'=>$this->id])->count();
        if ($data)
            return ' <span class="badge badge-warning pull-right">'.$data.'</span>';
        else
            return "";
    }

    public static function getAssignedOuterQuantities($id)
    {
        $data = RentOuterGear::find()
            ->where(['rent_id'=>$id])
            ->all();
        $list = ArrayHelper::map($data, 'outer_gear_id', 'quantity');
        return $list;
    }

    public static function getAssignedOuterModelQuantities($id)
    {
        $data = RentOuterGearModel::find()
            ->where(['rent_id'=>$id])
            ->all();
        $list = ArrayHelper::map($data, 'id', 'quantity');
        return $list;
    }

    public function getAssignedOuterGears($params = [])
    {
        $query = $this->getOuterGears();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedOuterGearModels($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $ids = ArrayHelper::map(RentOuterGearModel::find()->where(['resolved'=>0, 'rent_id'=>$this->id])->asArray()->all(), 'outer_gear_model_id', 'outer_gear_model_id');
        $query = $this->getOuterGearModels()->where(['IN', 'id', $ids]);
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedOuterGearModel($offer_id,$outer_gear_id) {
        return RentOuterGearModel::findOne(['rent_id' => $offer_id, 'outer_gear_model_id' => $outer_gear_id ]);
    }

    public static function assignOuterGear2($id, $item)
    {
            $model = RentOuterGear::findOne(['rent_id'=>$id, 'outer_gear_id'=>$item['outer_gear_id']]);
            $gearItem = OuterGear::findOne($item['outer_gear_id']);
            if ((!$item['price'])||($item['price']==""))
                $item['price'] = 0;
            if (($model===null)&&($item['quantity']>0))
            {
                $model = new RentOuterGear();
            }
            if ($item['quantity']>0)
            {
                $model->rent_id = $id;
                $model->outer_gear_id = $item['outer_gear_id'];
                $model->quantity = $item['quantity'];
                $model->price = $item['price'];
                $model->discount = 0;
                $model->user_id = $item['user_id'];
                $model->description = $item['description'];
                $model->return_time = $item['return_time'];
                $model->reception_time = $item['reception_time'];
                $model->save();                 
            }else{
                if ($model)
                {
                    $model->delete();
                }
            }

            $eventOuterModel = RentOuterGearModel::find()->where(['outer_gear_model_id'=>$gearItem->outer_gear_model_id])->andWhere(['rent_id'=>$id])->one();
            if ($eventOuterModel)
            {
                $total = 0;
                $outerIds = ArrayHelper::map(OuterGear::find()->where(['outer_gear_model_id'=>$gearItem->outer_gear_model_id])->asArray()->all(), 'id', 'id');
                $eogs = RentOuterGear::find()->where(['rent_id'=>$id])->andWhere(['outer_gear_id'=>$outerIds])->all();
                foreach ($eogs as $eog)
                {
                    $total +=$eog->quantity;
                }
                if ($total>=$eventOuterModel->quantity)
                {
                    $eventOuterModel->resolved = 1;
                }else{
                    $eventOuterModel->resolved = 0;
                }
                $eventOuterModel->save();
            }

            return false;
          

    }

public function getEventValueAll()
    {
        if (Yii::$app->session->get('company')!=1){
            $sum = 0;
            //$offers = $this->getAcceptedAgencyOffers();
            //if (isset($offers['error']) && $offers['error']) {
                return  [
                    Yii::t('app', 'Suma')=>$sum,
                    ];
            //}
            /*foreach ($offers as $offer)
            {
                $sum +=$offer->getNettoValue();

            }*/
            $profit = [
                Yii::t('app', 'Suma')=>$sum,
            ];
        }else{
            $return = [];
            $return[Yii::t('app', 'Suma')] = 0;
            $offers = $this->getOffersAccepted();
            foreach ($offers as $offer)
            {
                $values = $offer->getOfferValues();
                foreach ($values as $key=>$val)
                {
                    if (!isset($return[$key]))
                    {
                        $return[$key] = 0;
                    }
                    $return[$key] += $val;
                }

            }

            return $return;           
        }


        return $profit;
    }

        public function checkItems($items)
    {

        $newItems = [];
        foreach ($items as $id=>$quantity)
        {
            $gearItem = GearItem::findOne($id);
            if ($gearItem->gear->no_items)
            {
                $q = RentGearOutcomed::find()->where(['gear_id'=>$gearItem->gear_id, 'rent_id'=>$this->id])->one();
                if ($q)
                {
                    if ($q->quantity>$quantity)
                    {
                        $newItems[$id] = $quantity;
                    }else{
                        if ($q->quantity>0)
                            $newItems[$id] = $q->quantity;
                    }
                }
            }   else{
                if (($gearItem->rent_id==$this->id))
                {
                    $newItems[$id] = $quantity;
                }
            }
        }
        return $newItems;
    }

    public function checkGroups($groups)
    {
        $newgroups = [];
        foreach ($groups as $id=>$value)
        {
            $items = GearItem::find()->where(['group_id'=>$id])->all();
            $return = true;
            foreach ($items as $gearItem)
            {
                    if (($gearItem->rent_id==$this->id))
                    {
                        $newgroups[$id] = $value;
                    }else{
                        $return = false;
                    }
                
            }
            if ($return)
                $newgroups[$id] = $value;
        }
        return $newgroups;
        
    }

}

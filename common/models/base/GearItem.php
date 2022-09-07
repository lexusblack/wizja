<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "gear_item".
 *
 * @property integer $id
 * @property string $name
 * @property string $photo
 * @property string $weight
 * @property string $width
 * @property string $height
 * @property string $depth
 * @property string $weight_case
 * @property string $height_case
 * @property string $depth_case
 * @property string $width_case
 * @property string $volume
 * @property string $warehouse
 * @property string $location
 * @property string $code
 * @property string $serial
 * @property string $lamp_hours
 * @property string $description
 * @property string $info
 * @property string $purchase_price
 * @property string $refund_amount
 * @property string $test_date
 * @property string $tester
 * @property string $test_status
 * @property string $service
 * @property integer $gear_id
 * @property integer $status
 * @property integer $type
 * @property string $create_time
 * @property string $update_time
 * @property integer $group_id
 * @property integer $number
 * @property integer $active
 *
 * @property \common\models\EventGearItem[] $eventGearItems
 * @property \common\models\Event[] $events
 * @property \common\models\Gear $gear
 * @property \common\models\GearGroup $group
 * @property \common\models\GearService[] $gearServices
 * @property \common\models\IncomesGearOur[] $incomesGearOurs
 * @property \common\models\OfferGearItem[] $offerGearItems
 * @property \common\models\Offer[] $offers
 * @property \common\models\OutcomesGearOur[] $outcomesGearOurs
 * @property \common\models\RentGearItem[] $rentGearItems
 * @property \common\models\Rent[] $rents
 * @property string $aliasModel
 * @property \common\models\GearItemsNoItemsRfid[] $gearItemsNoItemsRfid
 * @property string $rfid_code
 */
abstract class GearItem extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_item';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'gear_id'], 'required'],
            [['weight', 'width', 'height', 'depth', 'weight_case', 'height_case', 'depth_case', 'width_case', 'volume', 'lamp_hours', 'purchase_price', 'refund_amount'], 'number'],
            [['description', 'info', 'service', 'rfid_code'], 'string'],
            [['test_date', 'create_time', 'update_time'], 'safe'],
            [['gear_id', 'status', 'type', 'group_id', 'number', 'active', 'outcomed', 'one_in_case', 'warehouse_id', 'event_id', 'packlist_id', 'rent_id'], 'integer'],
            [['name', 'photo', 'warehouse', 'location', 'code', 'serial', 'tester', 'test_status'], 'string', 'max' => 255],
            [['gear_id'], 'exist', 'skipOnError' => true, 'targetClass' => Gear::className(), 'targetAttribute' => ['gear_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => GearGroup::className(), 'targetAttribute' => ['group_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Nazwa'),
            'photo' => Yii::t('app', 'Zdjęcie'),
            'weight' => Yii::t('app', 'Waga'),
            'width' => Yii::t('app', 'Szerokość'),
            'height' => Yii::t('app', 'Wysokość'),
            'depth' => Yii::t('app', 'Głębokość'),
            'weight_case' => Yii::t('app', 'Waga z case [kg]'),
            'height_case' => Yii::t('app', 'Wysokość case [cm]'),
            'depth_case' => Yii::t('app', 'Głębokość case [cm]'),
            'width_case' => Yii::t('app', 'Szerokość case [cm]'),
            'volume' => Yii::t('app', 'Objętość [m3]'),
            'warehouse' => Yii::t('app', 'Magazyn'),
            'location' => Yii::t('app', 'Miejsce w magazynie'),
            'code'=>Yii::t('app', 'Własny kod kreskowy'),
            'serial' => Yii::t('app', 'Numer seryjny'),
            'lamp_hours' => Yii::t('app', 'Aktualne godziny lamp'),
            'description' => Yii::t('app', 'Opis'),
            'info' => Yii::t('app', 'Uwagi'),
            'purchase_price' => Yii::t('app', 'Cena zakupu'),
            'refund_amount' => Yii::t('app', 'Kwota zwrotu'),
            'test_date' => Yii::t('app', 'Sprawdzony'),
            'tester' => Yii::t('app', 'Sprawdzający'),
            'test_status' => Yii::t('app', 'Status'),
            'service' => Yii::t('app', 'Akcje serwisowe'),
            'gear_id' => Yii::t('app', 'Model'),
            'status' => Yii::t('app', 'Status'),
            'type' => Yii::t('app', 'Type'),
            'create_time' => Yii::t('app', 'Stworzono'),
            'update_time' => Yii::t('app', 'Zaktualizowano'),
            'group_id' => Yii::t('app', 'Case'),
            'number' => Yii::t('app', 'Numer urządzenia'),
            'one_in_case' =>  Yii::t('app', 'Pakowany pojedynczo'),
            'rfid_code' => Yii::t('app', 'Kod NEIS'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventGearItems()
    {
        return $this->hasMany(\common\models\EventGearItem::className(), ['gear_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(\common\models\Event::className(), ['id' => 'event_id'])->viaTable('event_gear_item', ['gear_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGear()
    {
        return $this->hasOne(\common\models\Gear::className(), ['id' => 'gear_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(\common\models\Event::className(), ['id' => 'event_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRent()
    {
        return $this->hasOne(\common\models\Rent::className(), ['id' => 'rent_id']);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouseModel()
    {
        return $this->hasOne(\common\models\Warehouse::className(), ['id' => 'warehouse_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(\common\models\GearGroup::className(), ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearServices()
    {
        return $this->hasMany(\common\models\GearService::className(), ['gear_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomesGearOurs()
    {
        return $this->hasMany(\common\models\IncomesGearOur::className(), ['gear_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferGearItems()
    {
        return $this->hasMany(\common\models\OfferGearItem::className(), ['gear_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffers()
    {
        return $this->hasMany(\common\models\Offer::className(), ['id' => 'offer_id'])->viaTable('offer_gear_item', ['gear_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcomesGearOurs()
    {
        return $this->hasMany(\common\models\OutcomesGearOur::className(), ['gear_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRentGearItems()
    {
        return $this->hasMany(\common\models\RentGearItem::className(), ['gear_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRents()
    {
        return $this->hasMany(\common\models\Rent::className(), ['id' => 'rent_id'])->viaTable('rent_gear_item', ['gear_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearItemsNoItemsRfid()
    {
        return $this->hasMany(\common\models\GearItemsNoItemsRfid::className(), ['gear_item_id' => 'id']);
    }

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCategory()
	{
		return $this->hasOne(\common\models\GearCategory::className(), ['gear_id' => 'id'])->viaTable('gear', ['category_id' => 'id']);
	}

    public function fields()
    {
        $fields = parent::fields();

        if ($this->isNewRecord) {
            return $fields;
        }
        $fields['gear_service'] = function() {
            $service = null;
            if ($this->status!=1)
            {
                $service = \common\models\GearService::getCurrentModel($this->id);
                if ($service)
                    $service = $service->toArray();
            }
            return $service;
        };
        
        return $fields;
    }

}
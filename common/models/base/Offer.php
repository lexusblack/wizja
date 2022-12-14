<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use backend\modules\offers\models\OfferExtraItem;
use Yii;

/**
 * This is the base-model class for table "offer".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $status
 * @property integer $customer_id
 * @property integer $contact_id
 * @property string $name
 * @property integer $location_id
 * @property string $term_from
 * @property string $term_to
 * @property string $page
 * @property integer $manager_id
 * @property string $offer_date
 * @property string $comment
 * @property string $event_start
 * @property string $event_end
 * @property string $packing_start
 * @property string $packing_end
 * @property string $montage_start
 * @property string $montage_end
 * @property string $readiness_start
 * @property string $readiness_end
 * @property string $practice_start
 * @property string $practice_end
 * @property string $disassembly_start
 * @property string $disassembly_end
 * @property string $create_time
 * @property string $update_time
 * @property string $payment_date
 *
 * @property \common\models\Customer $customer
 * @property \common\models\Location $location
 * @property \common\models\User $manager
 * @property \common\models\Contact $contact
 * @property \common\models\Event $event
 * @property \common\models\OfferCustomItems[] $offerCustomItems
 * @property \common\models\OfferGear[] $offerGears
 * @property \common\models\Gear[] $gears
 * @property \common\models\OfferGearItem[] $offerGearItems
 * @property \common\models\GearItem[] $gearItems
 * @property \common\models\OfferGearSetting[] $offerGearSettings
 * @property \common\models\OfferOuterGear[] $offerOuterGears
 * @property \common\models\OuterGear[] $outerGears
 * @property \common\models\OfferRole[] $offerRoles
 * @property \common\models\UserEventRole[] $roles
 * @property \common\models\OfferSetting[] $offerSettings
 * @property \common\models\OfferUserSkills[] $offerUserSkills
 * @property \common\models\OfferVehicle[] $offerVehicles
 * @property \common\models\Vehicle[] $vehicles
 * @property \backend\modules\offers\models\OfferExtraItem[] $extraItems
 * @property string $aliasModel
 */
abstract class Offer extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'status', 'customer_id', 'contact_id', 'location_id', 'manager_id', 'rent_id', 'payment_days', 'firm_id', 'offer_draft_id', 'price_group_id', 'created_by', 'blocked'], 'integer'],
            [['pm_cost', 'pm_cost_percent', 'packing_length', 'event_length', 'montage_length', 'disassembly_length', 'exchange_rate'], 'number'],
            [['name', 'offer_date', 'customer_id', 'offer_draft_id', 'price_group_id'], 'required'],
            [['comment', 'address', 'order_rules', 'language'], 'string'],
            [['event_start', 'event_end', 'packing_start', 'packing_end', 'montage_start', 'montage_end', 'readiness_start', 'readiness_end', 'practice_start', 'practice_end', 'disassembly_start', 'disassembly_end', 'create_time', 'update_time', 'payment_date'], 'safe'],
            [['name', 'term_from', 'term_to', 'page', 'offer_date'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['manager_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['manager_id' => 'id']],
            [['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::className(), 'targetAttribute' => ['event_id' => 'id']]
        ];


        
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Numer'),
            'event_id' => Yii::t('app', 'ID wydarzenia'),
            'rent_id'=>Yii::t('app', 'Wypo??yczenie'),
            'status' => Yii::t('app', 'Status'),
            'customer_id' => Yii::t('app', 'Klient'),
            'contact_id' => Yii::t('app', 'Osoba kontaktowa'),
            'name' => Yii::t('app', 'Nazwa Projektu'),
            'location_id' => Yii::t('app', 'Miejsce eventu/dostawy'),
            'term_from' => Yii::t('app', 'Termin od'),
            'term_to' => Yii::t('app', 'Termin do'),
            'page' => Yii::t('app', 'Strona'),
            'manager_id' => Yii::t('app', 'Project Manager'),
            'offer_date' => Yii::t('app', 'Data sporz??dzenia ofery'),
            'comment' => Yii::t('app', 'Uwagi'),
            'event_start' => Yii::t('app', 'Impreza od'),
            'event_end' => Yii::t('app', 'Impreza do'),
            'packing_start' => Yii::t('app', 'Pakowanie od'),
            'packing_end' => Yii::t('app', 'Pakowanie do'),
            'montage_start' => Yii::t('app', 'Monta?? od'),
            'montage_end' => Yii::t('app', 'Monta?? do'),
            'readiness_start' => Yii::t('app', 'Gotowo???? od'),
            'readiness_end' => Yii::t('app', 'Gotowo???? do'),
            'practice_start' => Yii::t('app', 'Pr??by od'),
            'practice_end' => Yii::t('app', 'Pr??by do'),
            'disassembly_start' => Yii::t('app', 'Demonta?? od'),
            'disassembly_end' => Yii::t('app', 'Demonta?? do'),
            'create_time' => Yii::t('app', 'Stworzono'),
            'update_time' => Yii::t('app', 'Zaktualizowano'),
            'payment_date' => Yii::t('app', 'Platno???? w terminie'),
            'address' => Yii::t('app', 'Adres r??cznie'),
            'pm_cost_percent' => Yii::t('app', 'Zaliczka %'),
            'pm_cost' => Yii::t('app', 'Zaliczka kwota'),
            'event_length' => Yii::t('app', 'D??ugo???? eventu [h]'),
            'packing_length' => Yii::t('app', 'D??ugo???? pakowania [h]'),
            'montage_length' => Yii::t('app', 'D??ugo???? monta??u [h]'),
            'disassembly_length' => Yii::t('app', 'D??ugo???? demonta??u [h]'),
            'payment_days' => Yii::t('app', 'Termin p??atno??ci [dni]'),
            'firm_id' => Yii::t('app', 'Firma oferenta'),
            'offer_draft_id' => Yii::t('app', 'Schemat oferty'),
            'price_group_id' => Yii::t('app', 'Grupa cenowa'),
            'language' => Yii::t('app', 'J??zyk'),
            'created_by' => Yii::t('app', 'Przygotowa??')
        ];
    }

/**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceGroup()
    {
        return $this->hasOne(\common\models\PriceGroup::className(), ['id' => 'price_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(\common\models\Customer::className(), ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirm()
    {
        return $this->hasOne(\common\models\Firm::className(), ['id' => 'firm_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(\common\models\Location::className(), ['id' => 'location_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManager()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'manager_id']);
    }
        /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'created_by']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferDraft()
    {
        return $this->hasOne(\common\models\OfferDraft::className(), ['id' => 'offer_draft_id']);
    }    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferStatut()
    {
        return $this->hasOne(\common\models\OfferStatut::className(), ['id' => 'status']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(\common\models\Contact::className(), ['id' => 'contact_id']);
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
    public function getProject()
    {
        return $this->hasOne(\common\models\Project::className(), ['id' => 'project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferCustomItems()
    {
        return $this->hasMany(\common\models\OfferCustomItems::className(), ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferGears()
    {
        return $this->hasMany(\common\models\OfferGear::className(), ['offer_id' => 'id']);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferSchedules()
    {
        return $this->hasMany(\common\models\OfferSchedule::className(), ['offer_id' => 'id'])->orderBy(['position'=>SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGears()
    {
        return $this->hasMany(\common\models\Gear::className(), ['id' => 'gear_id'])->viaTable('offer_gear', ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferGearItems()
    {
        return $this->hasMany(\common\models\OfferGearItem::className(), ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearItems()
    {
        return $this->hasMany(\common\models\GearItem::className(), ['id' => 'gear_item_id'])->viaTable('offer_gear_item', ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferGearSettings()
    {
        return $this->hasMany(\common\models\OfferGearSetting::className(), ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferOuterGears()
    {
        return $this->hasMany(\common\models\OfferOuterGear::className(), ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferExtraCosts()
    {
        return $this->hasMany(\common\models\OfferExtraCost::className(), ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOuterGearModels()
    {
        return $this->hasMany(\common\models\OuterGearModel::className(), ['id' => 'outer_gear_model_id'])->viaTable('offer_outer_gear', ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferRoles()
    {
        return $this->hasMany(\common\models\OfferRole::className(), ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferSends()
    {
        return $this->hasMany(\common\models\OfferSend::className(), ['offer_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(\common\models\UserEventRole::className(), ['id' => 'role_id'])->viaTable('offer_role', ['offer_id' => 'id']);
    }    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(\common\models\OfferLog::className(), ['offer_id' => 'id'])->orderBy(['create_time'=>SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferSettings()
    {
        return $this->hasMany(\common\models\OfferSetting::className(), ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferUserSkills()
    {
        return $this->hasMany(\common\models\OfferUserSkills::className(), ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferVehicles()
    {
        return $this->hasMany(\common\models\OfferVehicle::className(), ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVehicles()
    {
        return $this->hasMany(\common\models\Vehicle::className(), ['id' => 'vehicle_id'])->viaTable('offer_vehicle', ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtraItems()
    {
        return $this->hasMany(OfferExtraItem::className(), ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerNotes()
    {
        return $this->hasMany(CustomerNote::className(), ['offer_id' => 'id']);
    }

}

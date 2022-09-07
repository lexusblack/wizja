<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "rent".
 *
 * @property integer $id
 * @property string $name
 * @property string $start_time
 * @property string $end_time
 * @property string $deliver_time
 * @property string $return_time
 * @property string $info
 * @property integer $customer_id
 * @property integer $contact_id
 * @property integer $status
 * @property integer $type
 * @property integer $reminder
 * @property string $description
 * @property string $create_time
 * @property string $update_time
 * @property string $private_note
 * @property integer $invoice_status
 * @property string $invoice_number
 * @property integer $payment_status
 * @property integer $created_by
 * @property string $code
 *
 * @property \common\models\IncomesForRent[] $incomesForRents
 * @property \common\models\OutcomesForRent[] $outcomesForRents
 * @property \common\models\User $createdBy
 * @property \common\models\Customer $customer
 * @property \common\models\Contact $contact
 * @property \common\models\RentGearItem[] $rentGearItems
 * @property \common\models\GearItem[] $gearItems
 * @property string $aliasModel
 */
abstract class Rent extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rent';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'start_time', 'days'], 'required'],
            [['start_time', 'end_time', 'deliver_time', 'return_time', 'create_time', 'update_time'], 'safe'],
            [['info', 'description', 'private_note'], 'string'],
            [['customer_id', 'contact_id', 'status', 'type', 'reminder', 'invoice_status', 'payment_status', 'created_by', 'days', 'manager_id', 'tasks_schema_id'], 'integer'],
            [['name', 'invoice_number', 'code'], 'string', 'max' => 255],
            [['code'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']]
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
            'start_time' => Yii::t('app', 'Od'),
            'end_time' => Yii::t('app', 'Do'),
            'deliver_time' => Yii::t('app', 'Odbiór'),
            'return_time' => Yii::t('app', 'Zwrot'),
            'info' => Yii::t('app', 'Uwagi'),
            'customer_id' => Yii::t('app', 'Klient'),
            'contact_id' => Yii::t('app', 'Osoba kontaktowa'),
            'status' => Yii::t('app', 'Status'),
            'type' => Yii::t('app', 'Typ'),
            'reminder' => Yii::t('app', 'Przypomnij'),
            'description' => Yii::t('app', 'Opis'),
            'create_time' => Yii::t('app', 'Stworzono'),
            'update_time' => Yii::t('app', 'Zaktualizowano'),
            'private_note' => Yii::t('app', 'Notatka prywatna'),
            'invoice_status' => Yii::t('app', 'Faktura'),
            'invoice_number' => Yii::t('app', 'Numer faktury'),
            'payment_status' => Yii::t('app', 'Płatność'),
            'created_by' => Yii::t('app', 'Utworzył'),
            'code' => Yii::t('app', 'ID wypożyczenia'),
            'days' => Yii::t('app', 'Liczba dni pracy'),
            'manager_id' => Yii::t('app', 'Project Manager'),
            'tasks_schema_id' => Yii::t('app', 'Schemat zadań')
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventStatut()
    {
        return $this->hasOne(\common\models\EventStatut::className(), ['id' => 'status']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomesForRents()
    {
        return $this->hasMany(\common\models\IncomesForRent::className(), ['rent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcomesForRents()
    {
        return $this->hasMany(\common\models\OutcomesForRent::className(), ['rent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'created_by']);
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
    public function getCustomer()
    {
        return $this->hasOne(\common\models\Customer::className(), ['id' => 'customer_id']);
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
    public function getRentGearItems()
    {
        return $this->hasMany(\common\models\RentGearItem::className(), ['rent_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRentGears()
    {
        return $this->hasMany(\common\models\RentGear::className(), ['rent_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearItems()
    {
        return $this->hasMany(\common\models\GearItem::className(), ['id' => 'gear_item_id'])->viaTable('rent_gear_item', ['rent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffers()
    {
        return $this->hasMany(\common\models\Offer::className(), ['rent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRentAttachments()
    {
        return $this->hasMany(\common\models\RentAttachment::className(), ['rent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskCategories()
    {
        return $this->hasMany(\common\models\TaskCategory::className(), ['rent_id' => 'id'])->orderBy(['order'=>SORT_ASC]);
    }

    public function getRentOuterGears()
    {
        return $this->hasMany(\common\models\RentOuterGear::className(), ['rent_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRentOuterGearModels()
    {
        return $this->hasMany(\common\models\RentOuterGearModel::className(), ['rent_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOuterGears()
    {
        return $this->hasMany(\common\models\OuterGear::className(), ['id' => 'outer_gear_id'])->viaTable('rent_outer_gear', ['rent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOuterGearModels()
    {
        return $this->hasMany(\common\models\OuterGearModel::className(), ['id' => 'outer_gear_model_id'])->viaTable('rent_outer_gear_model', ['rent_id' => 'id']);
    }

    public function fields()
    {
        $fields = parent::fields();

        if ($this->isNewRecord) {
            return $fields;
        }
        $fields['customer'] = function() {
            $customer = null;
            /*
            foreach ($this->bundles as $bundle) {
                $bundles[] = $bundle->toArray();
            }
            */
            if ($this->customer)
                $customer = $this->customer->toArray();
            return $customer;
        };
        $fields['contact'] = function() {
            $contact = null;
            /*
            foreach ($this->bundles as $bundle) {
                $bundles[] = $bundle->toArray();
            }
            */
            if ($this->contact)
                $contact = $this->contact->toArray();
            return $contact;
        };
        $fields['manager'] = function() {
            $manager = null;
            /*
            foreach ($this->bundles as $bundle) {
                $bundles[] = $bundle->toArray();
            }
            */
            if ($this->manager)
                $manager = $this->manager->toArray();
            return $manager;
        };
        return $fields;
    }

}

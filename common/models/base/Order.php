<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "order".
 *
 * @property integer $id
 * @property integer $company_id
 * @property string $hash
 * @property integer $contact_id
 * @property integer $confirm
 * @property string $create_at
 * @property string $update_at
 * @property integer $user_id
 *
 * @property \common\models\EventOuterGear[] $eventOuterGears
 * @property \common\models\Customer $company
 * @property \common\models\Contact $contact
 * @property \common\models\User $user
 */
class Order extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'contact_id', 'confirm', 'user_id'], 'integer'],
            [['create_at', 'update_at'], 'safe'],
            [['hash'], 'string', 'max' => 45]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' =>  Yii::t('app', 'ID'),
            'company_id' =>  Yii::t('app', 'Firma'),
            'hash' =>  Yii::t('app', 'Hash'),
            'contact_id' =>  Yii::t('app', 'Osoba kontaktowa'),
            'confirm' =>  Yii::t('app', 'Potwierdzenie'),
            'create_at' =>  Yii::t('app', 'Utworzono'),
            'update_at' =>  Yii::t('app', 'Zaktualizowano'),
            'user_id' =>  Yii::t('app', 'Utworzył'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventOuterGears()
    {
        return $this->hasMany(\common\models\EventOuterGear::className(), ['order_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(\common\models\Customer::className(), ['id' => 'company_id']);
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
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }
    
/**
     * @inheritdoc
     * @return array mixed
     */ 
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_at',
                'updatedAtAttribute' => 'update_at',
                'value' => new \yii\db\Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false,
            ],
        ];
    }
}

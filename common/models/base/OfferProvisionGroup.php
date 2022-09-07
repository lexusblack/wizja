<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "offer_provision_group".
 *
 * @property integer $id
 * @property integer $offer_id
 * @property integer $team_id
 * @property integer $level
 * @property string $name
 * @property string $provision
 * @property integer $type
 * @property integer $main_only
 * @property integer $add_to_users
 * @property integer $is_pm
 * @property integer $customer_group_id
 *
 * @property \common\models\CustomerType $customerGroup
 * @property \common\models\Offer $offer
 * @property \common\models\Team $team
 */
class OfferProvisionGroup extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'customerGroup',
            'offer',
            'team'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['offer_id', 'team_id', 'level', 'type', 'main_only', 'add_to_users', 'is_pm', 'customer_group_id'], 'integer'],
            [['provision'], 'number'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_provision_group';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'offer_id' => 'Offer ID',
            'team_id' => 'Team ID',
            'level' => 'Level',
            'name' => 'Name',
            'provision' => 'Provision',
            'type' => 'Type',
            'main_only' => 'Main Only',
            'add_to_users' => 'Add To Users',
            'is_pm' => 'Is Pm',
            'customer_group_id' => 'Customer Group ID',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerGroup()
    {
        return $this->hasOne(\common\models\CustomerType::className(), ['id' => 'customer_group_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffer()
    {
        return $this->hasOne(\common\models\Offer::className(), ['id' => 'offer_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(\common\models\Team::className(), ['id' => 'team_id']);
    }
    }

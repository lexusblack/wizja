<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_provision_group".
 *
 * @property integer $id
 * @property integer $team_id
 * @property integer $event_id
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
 * @property \common\models\Event $event
 * @property \common\models\Team $team
 * @property \common\models\EventProvisionGroupProvision[] $eventProvisionGroupProvisions
 */
class EventProvisionGroup extends \yii\db\ActiveRecord
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
            'event',
            'team',
            'eventProvisionGroupProvisions'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team_id', 'event_id', 'level', 'type', 'main_only', 'add_to_users', 'is_pm', 'customer_group_id'], 'integer'],
            [['provision'], 'number'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_provision_group';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'team_id' => Yii::t('app', 'Zespół'),
            'level' => Yii::t('app', 'Poziom'),
            'name' => Yii::t('app', 'Nazwa'),
            'provision' => Yii::t('app', 'Wartość %'),
            'type' => Yii::t('app', 'Typ'),
            'main_only' => Yii::t('app', 'Inna prowizja na każdą sekcję'),
            'add_to_users' => Yii::t('app', 'Dodaj prowizję do rozliczeń pracowników'),
            'is_pm' => Yii::t('app', 'Prowizja PM'),
            'customer_group_id' => Yii::t('app', 'Ogranicz do grupy kontrahentów'),
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
    public function getEvent()
    {
        return $this->hasOne(\common\models\Event::className(), ['id' => 'event_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(\common\models\Team::className(), ['id' => 'team_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventProvisionGroupProvisions()
    {
        return $this->hasMany(\common\models\EventProvisionGroupProvision::className(), ['event_provision_group_id' => 'id']);
    }
    }

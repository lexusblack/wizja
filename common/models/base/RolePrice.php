<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "role_price".
 *
 * @property integer $id
 * @property integer $role_id
 * @property string $price
 * @property string $cost_hour
 * @property string $cost
 * @property integer $default
 * @property integer $role_price_group_id
 *
 * @property \common\models\UserEventRole $role
 * @property \common\models\RolePriceGroup $rolePriceGroup
 */
class RolePrice extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'role',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'default'], 'integer'],
            [['price', 'cost_hour', 'cost'], 'number'],
            [['name', 'unit', 'currency'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'role_price';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_id' => Yii::t('app', 'Rola'),
            'price' => Yii::t('app', 'Cena'),
            'cost_hour' => Yii::t('app', 'Koszt godzinowy'),
            'cost' => Yii::t('app', 'Koszt'),
            'default' => Yii::t('app', 'Domyślna'),
            'name' => Yii::t('app', 'Nazwa'),
            'unit' => Yii::t('app', 'Jednostka'),
            'currency' => Yii::t('app', 'Waluta'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(\common\models\UserEventRole::className(), ['id' => 'role_id']);
    }
        

    }

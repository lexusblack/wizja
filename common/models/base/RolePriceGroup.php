<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "role_price_group".
 *
 * @property integer $id
 * @property string $name
 * @property string $currency
 * @property integer $active
 *
 * @property \common\models\RolePrice[] $rolePrices
 */
class RolePriceGroup extends \yii\db\ActiveRecord
{

    public $prices;
    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'rolePrices'
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['active'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['currency', 'unit'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'role_price_group';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'currency' => Yii::t('app', 'Waluta'),
            'active' => 'Active',
            'unit' => Yii::t('app', 'Jednostka'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRolePrices()
    {
        return $this->hasMany(\common\models\RolePrice::className(), ['role_price_group_id' => 'id']);
    }

    public function getpPrices()
    {
        $return = [];
        $i = 0;
        foreach (RolePrice::find()->where(['role_price_group_id'=>$this->id])->asArray()->all() as $role)
            {
                $return[$i] = $role;
                $i++;
            }
        return $return;
    }

    public function savePrices($post)
    {
        $i = 0;
        foreach (RolePrice::find()->where(['role_price_group_id'=>$this->id])->all() as $p)
            {
                $price = $post["RolePriceGroup"]["prices"][$i];
                $p->price = $price['price'];
                $p->cost = $price['cost'];
                $p->cost_hour = $price['cost_hour'];
                $p->default = $price['default'];
                $p->save();
                $i++;
            }
    }


    
    }

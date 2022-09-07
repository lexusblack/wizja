<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "price_group".
 *
 * @property integer $id
 * @property string $name
 * @property string $currency
 * @property integer $active
 */
class PriceGroup extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            ''
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
            [['currency'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'price_group';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'currency' => Yii::t('app', 'Waluty'),
            'active' => 'Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearsPrices()
    {
        return $this->hasMany(\common\models\GearsPrice::className(), ['id' => 'gears_price_id'])->viaTable('gears_price_group', ['price_group_id' => 'id']);
    }

}

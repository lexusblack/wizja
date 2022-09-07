<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "hall_group_price_percent".
 *
 * @property integer $id
 * @property integer $hall_group_price_id
 * @property integer $day
 * @property string $value
 *
 * @property \common\models\HallGroupPrice $hallGroupPrice
 */
class HallGroupPricePercent extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'hallGroupPrice'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hall_group_price_id', 'day'], 'integer'],
            [['value'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hall_group_price_percent';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hall_group_price_id' => 'Hall Group Price ID',
            'day' => Yii::t('app', 'Dzień'),
            'value' => Yii::t('app', '% ceny dnia pierwszego'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallGroupPrice()
    {
        return $this->hasOne(\common\models\HallGroupPrice::className(), ['id' => 'hall_group_price_id']);
    }
    }

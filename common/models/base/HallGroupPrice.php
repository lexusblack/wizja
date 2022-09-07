<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "hall_group_price".
 *
 * @property integer $id
 * @property integer $hall_group_id
 * @property string $name
 * @property string $price
 * @property string $vat
 * @property string $currency
 * @property integer $default
 *
 * @property \common\models\HallGroup $hallGroup
 * @property \common\models\HallGroupPricePercent[] $hallGroupPricePercents
 */
class HallGroupPrice extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'hallGroup',
            'hallGroupPricePercents'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hall_group_id', 'default'], 'integer'],
            [['price', 'vat'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hall_group_price';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hall_group_id' => 'Hall Group ID',
            'name' => Yii::t('app', 'Nazwa'),
            'price' => Yii::t('app', 'Cena'),
            'vat' => Yii::t('app', 'Stawka VAT'),
            'currency' => Yii::t('app', 'Waluta'),
            'default' => Yii::t('app', 'Stawka domyślna'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallGroup()
    {
        return $this->hasOne(\common\models\HallGroup::className(), ['id' => 'hall_group_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallGroupPricePercents()
    {
        return $this->hasMany(\common\models\HallGroupPricePercent::className(), ['hall_group_price_id' => 'id']);
    }

    public function getPercentes()
    {
        $content = "";
        $content2 = "";
        $i=0;
        foreach ($this->hallGroupPricePercents as $p)
        {
            $content .= Yii::t('app', "Od ").$p->day.Yii::t('app', " dzień")." - ".$p->value."%<br/>";
            $i++;
            if ($i<4)
                $content2 .= Yii::t('app', "Od ").$p->day.Yii::t('app', " dzień")." - ".$p->value."%<br/>";
        }
        if ($i>3)
            return "<span title='".str_replace("<br/>", ", ", $content)."'>".$content2."</span>";
        else    
            return $content;
    }
    }

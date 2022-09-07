<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "warehouse".
 *
 * @property integer $id
 * @property string $name
 * @property string $short_name
 * @property string $color
 * @property string $address
 */
class Warehouse extends \yii\db\ActiveRecord
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
            [['name', 'address'], 'string', 'max' => 255],
            [['short_name'], 'string', 'max' => 12],
            [['color'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'warehouse';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'short_name' => Yii::t('app', 'Krótka nazwa'),
            'color' => Yii::t('app', 'Kolor'),
            'address' => Yii::t('app', 'Adres'),
        ];
    }

    public function getNumberLabel($gear)
    {
        if ($gear->no_items)
        {
            $q = WarehouseQuantity::find()->where(['warehouse_id'=>$this->id])->andWhere(['gear_id'=>$gear->id])->one();
            if ($q)
            {
                if ($q->quantity>0)
                    return "<br/><span class='label label-primary' style='padding:1px; background-color:".$this->color."'>".$q->quantity."</span> ".$this->short_name;
                else
                    return "";
            }else{
                return "";
            }
        }else{
            $q = GearItem::find()->where(['warehouse_id'=>$this->id])->andWhere(['gear_id'=>$gear->id])->andWhere(['active'=>1])->count();
            if ($q)
            {
                return "<br/><span class='label label-primary' style='padding:1px; background-color:".$this->color."'>".$q."</span> ".$this->short_name;
            }else{
                return "";
            }
        }
    }

    public function getNumber($gear)
    {
        if ($gear->no_items)
        {
            $q = WarehouseQuantity::find()->where(['warehouse_id'=>$this->id])->andWhere(['gear_id'=>$gear->id])->one();
            if ($q)
            {
                if ($q->quantity>0)
                    return $q->quantity;
                else
                    return 0;
            }else{
                return 0;
            }
        }else{
            $q = GearItem::find()->where(['warehouse_id'=>$this->id])->andWhere(['gear_id'=>$gear->id])->andWhere(['active'=>1])->count();
            if ($q)
            {
                return $q;
            }else{
                return 0;
            }
        }
    }
}

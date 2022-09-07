<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "gear_outer_connected".
 *
 * @property integer $id
 * @property integer $gear_id
 * @property integer $connected_id
 * @property integer $quantity
 * @property integer $checked
 * @property integer $gear_quantity
 * @property integer $in_offer
 *
 * @property \common\models\OuterGearModel $connected
 * @property \common\models\Gear $gear
 */
class GearOuterConnected extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'connected',
            'gear'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gear_id', 'connected_id', 'quantity', 'checked', 'gear_quantity', 'in_offer', 'subgroup'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_outer_connected';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gear_id' => 'Gear ID',
            'connected_id' => Yii::t('app', 'Powiązany sprzęt'),
            'quantity' => Yii::t('app', 'Liczba sztuk'),
            'gear_quantity' => Yii::t('app', 'Na sztuk sprzętu podstawowego'),
            'checked' => Yii::t('app', 'Domyślnie zaznaczony'),
            'in_offer' => Yii::t('app', 'Wyświetlaj w ofercie'),
            'subgroup' => Yii::t('app', 'Jako podpozycja'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConnected()
    {
        return $this->hasOne(\common\models\OuterGearModel::className(), ['id' => 'connected_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGear()
    {
        return $this->hasOne(\common\models\Gear::className(), ['id' => 'gear_id']);
    }
    
}

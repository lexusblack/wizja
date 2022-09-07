<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "gear_similar".
 *
 * @property integer $id
 * @property integer $gear_id
 * @property integer $similar_id
 *
 * @property \common\models\Gear $similar
 * @property \common\models\Gear $gear
 */
class GearSimilar extends \yii\db\ActiveRecord
{

    public $both;
    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'similar',
            'gear'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gear_id', 'similar_id', 'both'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_similar';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gear_id' => Yii::t('app', 'Sprzęt'),
            'similar_id' => Yii::t('app', 'Sprzęt podobny'),
            'both' => Yii::t('app', 'Relacja dwustronna')
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSimilar()
    {
        return $this->hasOne(\common\models\Gear::className(), ['id' => 'similar_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGear()
    {
        return $this->hasOne(\common\models\Gear::className(), ['id' => 'gear_id']);
    }
    
}

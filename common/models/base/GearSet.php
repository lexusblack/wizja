<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "gear_set".
 *
 * @property integer $id
 * @property string $name
 * @property integer $category_id
 * @property string $create_time
 * @property string $update_time
 *
 * @property \common\models\GearSetItem[] $gearSetItems
 */
class GearSet extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'gearSetItems',
            'gearSetOuterItems'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        [['name'], 'required'],
            [['category_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'photo'], 'string'],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_set';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'category_id' => Yii::t('app', 'Kategoria'),
            'create_time' => 'Data utworzenia',
            'update_time' => 'Update Time',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearSetItems()
    {
        return $this->hasMany(\common\models\GearSetItem::className(), ['gear_set_id' => 'id']);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearSetOuterItems()
    {
        return $this->hasMany(\common\models\GearSetOuterItem::className(), ['gear_set_id' => 'id']);
    }
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(\common\models\GearCategory::className(), ['id' => 'category_id']);
    }   
    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }
}

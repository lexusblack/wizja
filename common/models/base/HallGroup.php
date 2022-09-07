<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "hall_group".
 *
 * @property integer $id
 * @property string $name
 * @property string $area
 * @property string $width
 * @property string $length
 * @property string $height
 * @property string $main_photo
 * @property string $description
 *
 * @property \common\models\HallAudience[] $hallAudiences
 * @property \common\models\HallGroupPhoto[] $hallGroupPhotos
 * @property \common\models\HallHallGroup[] $hallHallGroups
 */
class HallGroup extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'hallAudiences',
            'hallGroupPhotos',
            'hallHallGroups'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['area', 'width', 'length', 'height'], 'number'],
            [['description'], 'string'],
            [['name', 'main_photo'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hall_group';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'area' => Yii::t('app', 'Powierzchnia'),
            'width' => Yii::t('app', 'Szerokość'),
            'length' =>  Yii::t('app', 'Długość'),
            'height' =>  Yii::t('app', 'Wysokość'),
            'main_photo' => Yii::t('app', 'Zdjęcie'),
            'description' => Yii::t('app', 'Opis'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallAudiences()
    {
        return $this->hasMany(\common\models\HallAudience::className(), ['hall_group_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallGroupPhotos()
    {
        return $this->hasMany(\common\models\HallGroupPhoto::className(), ['hall_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallGroupGears()
    {
        return $this->hasMany(\common\models\HallGroupGear::className(), ['hall_group_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallGroupNotes()
    {
        return $this->hasMany(\common\models\HallGroupNote::className(), ['hall_group_id' => 'id']);
    }
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallGroupCosts()
    {
        return $this->hasMany(\common\models\HallGroupCost::className(), ['hall_group_id' => 'id']);
    } 

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallGroupPrices()
    {
        return $this->hasMany(\common\models\HallGroupPrice::className(), ['hall_group_id' => 'id']);
    }       /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallHallGroups()
    {
        return $this->hasMany(\common\models\HallHallGroup::className(), ['hall_group_id' => 'id']);
    }

    public function getHalls()
    {
        return $this->hasMany(\common\models\Hall::className(), ['id' => 'hall_id'])->viaTable('hall_hall_group', ['hall_group_id' => 'id']);
    }
    }

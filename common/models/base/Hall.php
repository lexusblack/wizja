<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "hall".
 *
 * @property integer $id
 * @property string $name
 * @property string $area
 * @property string $width
 * @property string $length
 * @property string $height
 * @property string $main_photo
 *
 * @property \common\models\HallHallGroup[] $hallHallGroups
 */
class Hall extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
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
            [['name', 'main_photo'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hall';
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
        ];
    }

    public function getHallGroups()
    {
        return $this->hasMany(\common\models\HallGroup::className(), ['id' => 'hall_group_id'])->viaTable('hall_hall_group', ['hall_id' => 'id']);
    }
    
    }

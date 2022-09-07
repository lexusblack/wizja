<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "packlist".
 *
 * @property integer $id
 * @property integer $event_id
 * @property string $name
 * @property string $color
 *
 * @property \common\models\Event $event
 * @property \common\models\PacklistExtra[] $packlistExtras
 * @property \common\models\PacklistGear[] $packlistGears
 * @property \common\models\PacklistOuterGear[] $packlistOuterGears
 */
class Packlist extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'event',
            'packlistExtras',
            'packlistGears',
            'packlistOuterGears'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 45],
            [['info'], 'string'],
            [['start_time', 'end_time'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'packlist';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'name' => 'Name',
            'color' => 'Color',
            'info' => 'Opis'
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(\common\models\Event::className(), ['id' => 'event_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacklistExtras()
    {
        return $this->hasMany(\common\models\PacklistExtra::className(), ['packlist_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacklistGears()
    {
        return $this->hasMany(\common\models\PacklistGear::className(), ['packlist_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacklistOuterGears()
    {
        return $this->hasMany(\common\models\PacklistOuterGear::className(), ['packlist_id' => 'id']);
    }
    }

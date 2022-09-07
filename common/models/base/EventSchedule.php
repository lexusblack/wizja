<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_schedule".
 *
 * @property integer $id
 * @property integer $event_id
 * @property string $name
 * @property integer $position
 * @property string $start_time
 * @property string $end_time
 * @property string $prefix
 * @property integer $is_required
 * @property integer $book_gears
 */
class EventSchedule extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


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
            [['event_id', 'position', 'is_required', 'book_gears'], 'integer'],
            [['start_time', 'end_time'], 'safe'],
            [['name', 'prefix'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_schedule';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'is_required' => Yii::t('app', 'Pole obowiązkowe'),
            'book_gears' => Yii::t('app', 'Domyślnie rezerwuj sprzęt na ten etap'),
            'dateRange' => Yii::t('app', 'Czas trwania'),
            'prefix' => Yii::t('app', 'Prefix - wyświetlany w kalendarzu')
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
    public function getEventUserPlannedWrokingTimes()
    {
        return $this->hasMany(\common\models\EventUserPlannedWrokingTime::className(), ['event_schedule_id' => 'id']);
    }
}

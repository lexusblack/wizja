<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "schedule".
 *
 * @property integer $id
 * @property integer $event_type_id
 * @property string $name
 * @property integer $position
 * @property integer $is_required
 * @property integer $book_gears
 */
class Schedule extends \yii\db\ActiveRecord
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
            [['schedule_type_id', 'position', 'is_required', 'book_gears'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'schedule';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_type_id' => 'Event Type ID',
            'name' => Yii::t('app', 'Nazwa'),
            'position' => 'Position',
            'is_required' => Yii::t('app', 'Pole obowiązkowe w wydarzeniu'),
            'book_gears' => Yii::t('app', 'Domyślnie rezerwuj sprzęt na ten etap'),
        ];
    }
}

<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_field_setting".
 *
 * @property integer $id
 * @property string $name
 * @property integer $active
 * @property integer $type
 * @property integer $column_in_list
 * @property integer $visible_on_packlist
 */
class EventFieldSetting extends \yii\db\ActiveRecord
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
            [['active', 'type', 'column_in_list', 'visible_on_packlist'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['default_value', 'packlist_position', 'default_value_int'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_field_setting';
    }

    public static function getTypeList()
    {
        return
        [
            1=>Yii::t('app', 'Pole numeryczne'),
            2=>Yii::t('app', 'Pole tekstowe krótkie'),
            3=>Yii::t('app', 'Pole tekstowe długie'),
        ];
    }

    public static function getPacklistPositions()
    {
        return
        [
            1=>Yii::t('app', 'Nad listą sprzętu'),
            2=>Yii::t('app', 'Pod listą sprzętu'),
            3=>Yii::t('app', 'Na następnej stronie'),
        ];        
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'active' => 'Active',
            'type' => Yii::t('app', 'Typ'),
            'column_in_list' => Yii::t('app', 'Kolumna na liście wydarzeń'),
            'visible_on_packlist' => Yii::t('app', 'Widoczny na packliście'),
            'packlist_position' => Yii::t('app', 'Miejsce na packliście'),
            'default_value' => Yii::t('app', 'Domyślna wartość (tekst)'),
            'default_value_int' => Yii::t('app', 'Domyślna wartość (liczba)'),
        ];
    }
}

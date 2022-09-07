<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "rfid_antenna".
 *
 * @property integer $id
 * @property string $name
 * @property string $parameters
 * @property string $code
 */
class RfidAntenna extends \yii\db\ActiveRecord
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
            [['name', 'parameters'], 'required'],
            [['parameters'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rfid_antenna';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'parameters' => 'Parameters',
            'code' => 'Code',
        ];
    }
}

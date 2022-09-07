<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "customer_note_permission".
 *
 * @property integer $id
 * @property integer $customer_note_id
 * @property string $permission
 */
class CustomerNotePermission extends \yii\db\ActiveRecord
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
            [['customer_note_id'], 'integer'],
            [['permission'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_note_permission';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_note_id' => 'Customer Note ID',
            'permission' => 'Permission',
        ];
    }
}

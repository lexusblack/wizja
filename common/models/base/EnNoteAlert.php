<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "en_note_alert".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $en_note_id
 */
class EnNoteAlert extends \yii\db\ActiveRecord
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
            [['user_id', 'en_note_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'en_note_alert';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'en_note_id' => 'En Note ID',
        ];
    }
}

<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "request_read".
 *
 * @property integer $id
 * @property integer $request_id
 * @property integer $user_id
 * @property integer $type
 */
class RequestRead extends \yii\db\ActiveRecord
{


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
            [['id', 'request_id', 'user_id', 'type'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request_read';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'request_id' => 'Request ID',
            'user_id' => 'User ID',
            'type' => 'Type',
        ];
    }
}

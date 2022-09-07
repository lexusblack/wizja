<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "crn_chat".
 *
 * @property integer $id
 * @property string $company_asking
 * @property string $company_recieving
 * @property integer $cross_rental_id
 * @property string $last_message
 * @property string $name
 * @property integer $create_by
 */
class CrnChat extends \yii\db\ActiveRecord
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
            [['cross_rental_id', 'create_by'], 'integer'],
            [['last_message'], 'safe'],
            [['company_asking', 'company_recieving'], 'string', 'max' => 45],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'crn_chat';
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
            'company_asking' => 'Company Asking',
            'company_recieving' => 'Company Recieving',
            'cross_rental_id' => 'Cross Rental ID',
            'last_message' => 'Last Message',
            'name' => 'Name',
            'create_by' => 'Create By',
        ];
    }
}

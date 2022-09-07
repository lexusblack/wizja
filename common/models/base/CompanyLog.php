<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "company_log".
 *
 * @property integer $id
 * @property string $company_id
 * @property string $datetime
 * @property integer $users
 * @property integer $rents
 * @property integer $events
 * @property integer $gears
 */
class CompanyLog extends \yii\db\ActiveRecord
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
            [['datetime'], 'safe'],
            [['users', 'rents', 'events', 'gears'], 'integer'],
            [['company_id'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company_log';
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
            'company_id' => 'Company ID',
            'datetime' => 'Datetime',
            'users' => 'Users',
            'rents' => 'Rents',
            'events' => 'Events',
            'gears' => 'Gears',
        ];
    }
}

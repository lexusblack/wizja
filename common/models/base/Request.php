<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "request".
 *
 * @property integer $id
 * @property string $name
 * @property string $company_id
 * @property string $mail
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class Request extends \yii\db\ActiveRecord
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
            [['name', 'company_id', 'mail', 'status'], 'required'],
            [['status', 'event_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'mail', 'username'], 'string', 'max' => 255],
            [['company_id'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request';
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
            'name' => Yii::t('app', 'Nazwa'),
            'company_id' => Yii::t('app', 'Firma'),
            'mail' => 'Mail',
            'status' => Yii::t('app', 'Status'),
            'create_time' => Yii::t('app', 'Data'),
            'update_time' => Yii::t('app', 'Zmiana'),
            'type' => Yii::t('app', 'Typ'),
            'priority' => Yii::t('app', 'Priorytet'),
        ];
    }

    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    public function getRequestNotes()
    {
        return $this->hasMany(\common\models\RequestNote::className(), ['request_id' => 'id']);
    }

    public function getCompany()
    {
        return $this->hasOne(\common\models\Company::className(), ['code' => 'company_id']);
    }

    public function getEvents()
    {
        return $this->hasMany(\common\models\Event::className(), ['id' => 'event_id'])->viaTable('event_request', ['request_id' => 'id']);
    }
}

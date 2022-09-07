<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "en_note".
 *
 * @property integer $id
 * @property integer $company_id
 * @property string $text
 * @property string $link
 * @property string $datetime
 */
class EnNote extends \yii\db\ActiveRecord
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
            [['company_id'], 'required'],
            [['company_id'], 'integer'],
            [['text'], 'string'],
            [['datetime'], 'safe'],
            [['link'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'en_note';
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
            'text' => 'Text',
            'link' => 'Link',
            'datetime' => 'Datetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(\common\models\Company::className(), ['id' => 'company_id']);
    }
}

<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "packlist_schema".
 *
 * @property integer $id
 * @property string $name
 *
 * @property \common\models\PacklistSchemaItem[] $packlistSchemaItems
 */
class PacklistSchema extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'packlistSchemaItems'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'packlist_schema';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacklistSchemaItems()
    {
        return $this->hasMany(\common\models\PacklistSchemaItem::className(), ['packlist_schema_id' => 'id']);
    }
    }

<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "packlist_schema_item".
 *
 * @property integer $id
 * @property string $name
 * @property string $color
 * @property integer $packlist_schema_id
 *
 * @property \common\models\PacklistSchema $packlistSchema
 */
class PacklistSchemaItem extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'packlistSchema'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['packlist_schema_id'], 'integer'],
            [['name', 'color'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'packlist_schema_item';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'color' => Yii::t('app', 'Kolor'),
            'packlist_schema_id' => 'Packlist Schema ID',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacklistSchema()
    {
        return $this->hasOne(\common\models\PacklistSchema::className(), ['id' => 'packlist_schema_id']);
    }
    }

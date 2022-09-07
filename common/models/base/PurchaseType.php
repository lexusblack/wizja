<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "purchase_type".
 *
 * @property integer $id
 * @property string $name
 *
 * @property \common\models\Purchase[] $purchases
 */
class PurchaseType extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'purchases'
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
        return 'purchase_type';
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
    public function getPurchases()
    {
        return $this->hasMany(\common\models\Purchase::className(), ['purchase_type_id' => 'id']);
    }
    }

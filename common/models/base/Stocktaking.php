<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "stocktaking".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $datetime
 * @property string $description
 *
 * @property \common\models\User $user
 * @property \common\models\StocktakingItem[] $stocktakingItems
 */
class Stocktaking extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'user',
            'stocktakingItems'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['datetime'], 'safe'],
            [['description'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stocktaking';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'datetime' => 'Datetime',
            'description' => 'Description',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStocktakingItems()
    {
        return $this->hasMany(\common\models\StocktakingItem::className(), ['stocktaking_id' => 'id']);
    }
    }

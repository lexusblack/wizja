<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "investition".
 *
 * @property integer $id
 * @property string $name
 * @property integer $quantity
 * @property string $price
 * @property string $total_price
 * @property string $vat
 * @property integer $year
 * @property integer $month
 * @property string $section
 * @property integer $expense_id
 * @property integer $creator_id
 * @property string $create_time
 *
 * @property \common\models\Expense $expense
 * @property \common\models\User $creator
 */
class Investition extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'expense',
            'creator'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['quantity', 'expense_id', 'creator_id'], 'integer'],
            [['price', 'total_price', 'vat'], 'number'],
            [['create_time'], 'safe'],
            [['name', 'section'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'investition';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nazwa',
            'quantity' => 'Ilość',
            'price' => 'Cena',
            'total_price' => 'Cena łącznie',
            'vat' => 'Stawka Vat %',
            'section' => 'Sekcja',
            'expense_id' => 'Faktura kosztowa',
            'creator_id' => 'Dodał',
            'create_time' => 'Create Time',
            'datetime' => 'Data'
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpense()
    {
        return $this->hasOne(\common\models\Expense::className(), ['id' => 'expense_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'creator_id']);
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
                'updatedAtAttribute' => false,
            ],
        ];
    }
}

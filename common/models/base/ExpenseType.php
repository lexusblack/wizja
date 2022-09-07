<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "expense_type".
 *
 * @property integer $id
 * @property string $name
 * @property string $color
 * @property integer $investition
 */
class ExpenseType extends \yii\db\ActiveRecord
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
            [['investition', 'active'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'expense_type';
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
            'investition' => Yii::t('app', 'Dodać do inwestycji?'),
        ];
    }
}

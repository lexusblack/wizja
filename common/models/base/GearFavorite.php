<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "gear_favorite".
 *
 * @property integer $id
 * @property integer $gear_id
 * @property integer $user_id
 * @property integer $position
 */
class GearFavorite extends \yii\db\ActiveRecord
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
            [['gear_id', 'user_id', 'position'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_favorite';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gear_id' => 'Gear ID',
            'user_id' => 'User ID',
            'position' => 'Position',
        ];
    }
}

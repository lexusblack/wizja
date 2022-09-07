<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "gear_translate".
 *
 * @property integer $gear_id
 * @property string $name
 * @property string $info
 * @property string $language_id
 * @property integer $id
 */
class RoleTranslate extends \yii\db\ActiveRecord
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
            [['role_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['language_id'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'role_translate';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role_id' => 'Gear ID',
            'name' => Yii::t('app', 'Nazwa'),
            'language_id' => Yii::t('app', 'Język'),
            'id' => 'ID',
        ];
    }
}

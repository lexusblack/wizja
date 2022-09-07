<?php

namespace common\models;

use Yii;
use \common\models\base\GearTranslate as BaseGearTranslate;

/**
 * This is the model class for table "gear_translate".
 */
class GearTranslate extends BaseGearTranslate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_id'], 'integer'],
            [['info'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['language_id'], 'string', 'max' => 45],
            [['gear_id', 'language_id', 'name'], 'required']
        ]);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(\common\models\Language::className(), ['code' => 'language_id']);
    }
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGear()
    {
        return $this->hasOne(\common\models\Gear::className(), ['id' => 'gear_id']);
    }
}

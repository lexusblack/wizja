<?php

namespace common\models;

use Yii;
use \common\models\base\OuterGearTranslate as BaseOuterGearTranslate;

/**
 * This is the model class for table "gear_translate".
 */
class OuterGearTranslate extends BaseOuterGearTranslate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['language_id'], 'string', 'max' => 45]
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
        return $this->hasOne(\common\models\OuterGearModel::className(), ['id' => 'gear_id']);
    }
}

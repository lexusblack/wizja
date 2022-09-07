<?php

namespace common\models;

use common\helpers\ArrayHelper;
use Yii;
use \common\models\base\Language as BaseLanguage;

/**
 * This is the model class for table "language".
 */
class Language extends BaseLanguage
{

	public function rules() {
		$rules = [
			[['code'], 'match', 'pattern'=>'/^[a-z]{2}(\-[a-z]{2})?$/i'],
		];
		return array_merge(parent::rules(), $rules);
	}

	public static function getTranslationList()
    {
        $sourceLanguage = 'pl';

        $models = static::find()
            ->where(['!=', 'code', $sourceLanguage ])
            ->all();

        $list = ArrayHelper::map($models, 'code', 'name');

        return $list;
    }

    public static function getCodesList2()
    {
        $models = static::find()
            ->all();

        $list = ArrayHelper::map($models, 'code', 'name');

        return $list;
    }

    public static function getCodesList()
    {
        $list = static::getModelList(false, 'code');
        return $list;
    }

    public static function getTranslationListString()
    {
        $list = static::getTranslationList();
        return implode(',', array_keys($list));
    }

    public function afterSave( $insert, $changedAttributes )
    {
    	if (isset($changedAttributes['code']))
	    {
	    	Message::updateAll([
				'language'=>$this->code,
		    ], [
		    	'language'=>$changedAttributes['code'],
		    ]);
	    }

	    parent::afterSave( $insert, $changedAttributes );
    }
}

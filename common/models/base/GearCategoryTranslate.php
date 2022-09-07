<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "gear_category_translate".
 *
 * @property integer $id
 * @property integer $gear_category_id
 * @property string $name
 * @property string $language_id
 */
class GearCategoryTranslate extends \yii\db\ActiveRecord
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
            [['gear_category_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['language_id'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_category_translate';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gear_category_id' => Yii::t('app', 'Kategoria sprzętu'),
            'name' => Yii::t('app', 'Tłumaczenie'),
            'language_id' => Yii::t('app', 'Język'),
        ];
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearCategory()
    {
        return $this->hasOne(\common\models\GearCategory::className(), ['id' => 'gear_category_id']);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(\common\models\Language::className(), ['code' => 'language_id']);
    }

}

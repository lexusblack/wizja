<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "migration".
 *
 * @property string $version
 * @property integer $apply_time
 * @property string $aliasModel
 */
abstract class Migration extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'migration';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['version'], 'required'],
            [['apply_time'], 'integer'],
            [['version'], 'string', 'max' => 180]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'version' => Yii::t('app', 'Wersja'),
            'apply_time' => Yii::t('app', 'Data zastosowania'),
        ];
    }




}

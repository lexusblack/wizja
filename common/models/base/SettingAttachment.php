<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "setting_attachment".
 *
 * @property integer $id
 * @property string $filename
 * @property string $extension
 * @property integer $type
 * @property integer $status
 * @property string $content
 * @property string $create_time
 * @property string $update_time
 * @property string $info
 * @property string $mime_type
 * @property string $base_name
 * @property string $aliasModel
 */
abstract class SettingAttachment extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'setting_attachment';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status'], 'integer'],
            [['content', 'info'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['filename', 'extension', 'mime_type', 'base_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'filename' => Yii::t('app', 'Plik'),
            'extension' => Yii::t('app', 'Rozszerzenie'),
            'type' => Yii::t('app', 'Typ'),
            'status' => Yii::t('app', 'Status'),
            'content' => Yii::t('app', 'Zawartość'),
            'create_time' => Yii::t('app', 'Stworzono'),
            'update_time' => Yii::t('app', 'Zaktualizowano'),
            'info' => Yii::t('app', 'Info'),
            'mime_type' => Yii::t('app', 'Typ Mime'),
            'base_name' => Yii::t('app', 'Nazwa podstawowa'),
        ];
    }




}

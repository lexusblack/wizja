<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "notification".
 *
 * @property integer $id
 * @property string $name
 * @property string $label
 * @property string $title
 * @property string $content
 * @property string $hint
 * @property string $info
 * @property integer $mail
 * @property integer $sms
 * @property integer $push
 * @property integer $type
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property string $aliasModel
 */
abstract class Notification extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['content', 'info'], 'string'],
            [['mail', 'sms', 'push', 'type', 'status'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'label', 'title', 'hint'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Nazwa'),
            'label' => Yii::t('app', 'Etykieta'),
            'title' => Yii::t('app', 'Tytuł'),
            'content' => Yii::t('app', 'Treść'),
            'hint' => Yii::t('app', 'Komentarz'),
            'info' => Yii::t('app', 'Info'),
            'mail' => Yii::t('app', 'E-mail'),
            'sms' => Yii::t('app', 'SMS'),
            'push' => Yii::t('app', 'PUSH'),
            'type' => Yii::t('app', 'Typ'),
            'status' => Yii::t('app', 'Status'),
            'create_time' => Yii::t('app', 'Zaktualizowano'),
            'userIds'=>Yii::t('app', 'Odbiorcy')
        ];
    }

    public function getUsers()
    {
        return $this->hasMany(\common\models\User::className(), ['id' => 'user_id'])->viaTable('notification_recipient', ['notification_id' => 'id']);
    }




}

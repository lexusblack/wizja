<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_additional_statut".
 *
 * @property integer $id
 * @property string $name
 * @property string $permission_users
 * @property string $permission_teams
 * @property integer $active
 */
class EventAdditionalStatut extends \yii\db\ActiveRecord
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
            [['active'], 'integer'],
            [['name', 'permission_users', 'permission_teams'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_additional_statut';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'permission_users' => Yii::t('app', 'Uprawnieni użytkownicy'),
            'permission_teams' => Yii::t('app', 'Uprawnione grupy'),
            'active' => 'Active',
        ];
    }
}

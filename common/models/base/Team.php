<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "team".
 *
 * @property integer $id
 * @property string $name
 * @property integer $active
 *
 * @property \common\models\TeamUser[] $teamUsers
 */
class Team extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'teamUsers'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['active'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'team';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'userIds'=>Yii::t('app', 'Skład'),
            'active' => 'Active',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamUsers()
    {
        return $this->hasMany(\common\models\TeamUser::className(), ['team_id' => 'id']);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(\common\models\User::className(), ['id' => 'user_id'])->viaTable('team_user', ['team_id' => 'id']);
    }
    }

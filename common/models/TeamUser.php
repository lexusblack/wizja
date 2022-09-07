<?php

namespace common\models;

use Yii;
use \common\models\base\TeamUser as BaseTeamUser;

/**
 * This is the model class for table "team_user".
 */
class TeamUser extends BaseTeamUser
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['team_id', 'user_id'], 'integer']
        ]);
    }
	
}

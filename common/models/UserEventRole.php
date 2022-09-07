<?php

namespace common\models;

use common\helpers\ArrayHelper;
use Yii;
use \common\models\base\UserEventRole as BaseUserEventRole;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "user_event_role".
 */
class UserEventRole extends BaseUserEventRole
{

    public static function findEventRoles($userId, $eventId)
    {
//        $models = EventUser::findAll(['user_id'=>$userId, 'event_id'=>$eventId]);


        $models = static::find()
            ->innerJoinWith(['eventUserRoles'=>function($q){
                $q->innerJoinWith('eventUser');
            }])
            ->where([
                'user_id'=>$userId,
                'event_id'=>$eventId,
            ])
            ->all();

        return $models;
    }

    public static function getRolesString($userId, $eventId, $separator = '; ')
    {
        $models = static::findEventRoles($userId, $eventId);
        $list = ArrayHelper::map($models, 'id', 'name');
        return implode($separator, $list);
    }

    public static function getRolesMoney()
    {
        $models = UserEventRole::find()->where(['active'=>1])->asArray()->all();
        $list = ArrayHelper::map($models, 'id', 'salary');
        return $list;
    }

    public static function getRolesCustomerMoney()
    {
        $models = UserEventRole::find()->where(['active'=>1])->asArray()->all();
        $list = ArrayHelper::map($models, 'id', 'salary_customer');
        return $list;
    }

    public static function getRolesCustomerMoneyHour()
    {
        $models = UserEventRole::find()->where(['active'=>1])->asArray()->all();
        $list = ArrayHelper::map($models, 'id', 'salary_customer_hours');
        return $list;
    }

    public static function getCompatibilityList()
    {
        return [
            Yii::t('app', 'Nie'),
            Yii::t('app', 'Tak'),
        ];
    }
    public function getCompatibilityLabel()
    {
        return ArrayHelper::getValue(self::getCompatibilityList(), $this->compatibility, UNDEFINDED_STRING);
    }

    public function getList()
    {
        $models = UserEventRole::find()->where(['active'=>1])->orderBy(['position'=>SORT_ASC])->asArray()->all();
        $list = ArrayHelper::map($models, 'id', 'name');
        return $list;
    }

    public function getTranslates($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getRoleTranslates();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

        public static function getTranslateName($id, $language, $name)
    {
        if (!$language)
            return $name;
        $translate = RoleTranslate::find()->where(['language_id'=>$language])->andWhere(['role_id'=>$id])->one();
        if ($translate)
            return $translate->name;
        else
            return $name;
    }
}

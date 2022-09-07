<?php
namespace common\components;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\helpers\ArrayHelper;
use Yii;

class BaseActiveRecord extends ActiveRecord
{
//    public function afterSave ($insert, $changedAttributes)
//    {
//        parent::afterSave($insert, $changedAttributes);
//        $this->refresh();
//    }

    /**
     *
     * @param $params array
     * @param $insert bool Zapisać nowy w bazie?
     * @return mixed BaseActiveRecord
     */
    public static function loadByParams($params, $insert=false)
    {
        $className = static::className();
        $model = $className::findOne($params);
        if ($model===null)
        {
            $model = new $className($params);
            if ($insert == true)
            {
                $model->insert();
            }
        }
        return $model;
    }

    public function behaviors()
    {
        $behaviors = [];
        if ($this->hasAttribute('create_time'))
        {
            $behaviors['timestampBehavior'] = [
                    'class' => \yii\behaviors\TimestampBehavior::className(),
                    'createdAtAttribute' => 'create_time',
                    'updatedAtAttribute' => 'update_time',
                    'value' => new \yii\db\Expression('NOW()'),
                ];
        }

//        if ($this->hasAttribute('objectUUID'))
//        {
//            $behaviors['objectUUIDBehavior'] =
//                [
//                    'class' => AttributeBehavior::className(),
//                    'attributes' => [
//                        ActiveRecord::EVENT_BEFORE_INSERT => 'objectUUID',
//                        ActiveRecord::EVENT_BEFORE_UPDATE => 'objectUUID',
//                    ],
//                    'value' => function ($event)
//                    {
//                        $value = $this->objectUUID;
//                        if(empty($value))
//                        {
//                            $value = new Expression('UUID()');
//                        }
//                        return $value;
//                    },
//                ];
//        }

        return array_merge(parent::behaviors(), $behaviors);
    }

    public static function getModelList($asModels=false, $value='name',  $key='id')
    {
        $models = static::find()->all();
        $list = [];
        if ($asModels == true)
        {
            $list = $models;
        }
        else
        {
            $list = ArrayHelper::map($models, $key, $value);
        }
        return $list;
    }

    public static function getReminderList()
    {
        //w minutach
        $list = [
            60 => Yii::t('app','Godzina przed'),
            240 => Yii::t('app','4 gdziny przed'),
            360 => Yii::t('app','6 godzin przed'),
            720 => Yii::t('app','12 godzin przed'),
            1440 => Yii::t('app','24 godziny przed'),
        ];
        return $list;
    }

    public function getReminderLabel()
    {
        $list = static::getReminderList();
        $index = $this->reminder;
        return isset($list[$index]) ? $list[$index] : UNDEFINDED_STRING;
    }

    public static function getInvoiceStatusList()
    {
        $list = [
            0=> Yii::t('app','Niewystawiona'),
            10 => Yii::t('app','Wystawiona'),
        ];

        return $list;
    }

    public static function getPaymentList()
    {
        $list = [
            0 => Yii::t('app','Nierozliczona'),
            10 => Yii::t('app','Rozliczona'),
        ];
        return $list;
    }

    public function loadFileUrl($attribute, $alias)
    {
        if ($this->{$attribute} == null)
        {
            return null;
        }
        else
        {
            return Yii::getAlias($alias.$this->{$attribute});
        }
    }

    public static function columnHasValue($column, $where=false)
    {
        $count = static::getDb()->cache(function($db) use ($where, $column) {
            try {
                $query =  static::find()
                    ->where([ 'not', [
                        'or',
                        [$column=>null],
                        [$column=>''],
                    ]]);
                if ($where != false)
                {
                    $query->andWhere($where);
                }
                $count = $query->count();
                return $count;
            }
            catch (\Exception $e)
            {
                return 1;
            }

            });
        $hasValue = false;
        if ($count>0)
        {
            $hasValue = true;
        }
        return $hasValue;
    }

    public function getShortClassName()
    {
	    $name = (new ReflectionClass($this))->getShortName();
	    return $name;
    }
}
<?php
namespace common\models\form;

use Yii;
use yii\base\Model;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class GearSetAssignment extends Model
{


    public $quantity;
    public $gear_set_id;
    public $targetId;
    public $targetClass;

    public function rules()
    {
        return [
            [['quantity', 'gear_set_id'], 'required'],
            [['quantity'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'quantity' => Yii::t('app', 'Ilość'),
        ];
    }

}
<?php

namespace common\models;

use Yii;
use \common\models\base\RentGear as BaseRentGear;

/**
 * This is the model class for table "rent_gear".
 */
class RentGear extends BaseRentGear
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['rent_id', 'gear_id', 'type', 'quantity'], 'integer'],
            [['start_time', 'end_time', 'create_time', 'update_time'], 'safe'],
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $count = Note::find()->where(['rent_id'=>$this->rent_id])->andWhere(['user_id'=>Yii::$app->user->id])->andWhere(['>', 'datetime', date('Y-m-d H')."00:00"])->andWhere(['like', 'text',Yii::t('app', 'Zmieniono rezerwację sprzętu')])->count();
        if (!$count)
            Note::createNote(2, 'rentGearChanged', $this->rent, $this->rent_id);


    }

        public function recalculateQuantity()
    {
        //wydajemy sprzęty, po wydaniu sprawdzamy czy nie wydano więccej sztuk danego sprzętu
        if ($this->gear->no_items)
        {

        }else{
            $ids = \yii\helpers\ArrayHelper::map(GearItem::find()->where(['gear_id'=>$this->gear_id])->asArray()->all(), 'id', 'id');
            $count = RentGearItem::find()->where(['rent_id'=>$this->rent_id])->andWhere(['gear_item_id'=>$ids])->count();
            if ($count>$this->quantity)
            {
                $this->quantity = $count;
                $this->save();
            }
        }

    }
	
}

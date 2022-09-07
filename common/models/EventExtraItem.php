<?php

namespace common\models;

use Yii;
use \common\models\base\EventExtraItem as BaseEventExtraItem;

/**
 * This is the model class for table "event_extra_item".
 */
class EventExtraItem extends BaseEventExtraItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
        [['name', 'quantity', 'gear_category_id'], 'required' ],
            [['offer_extra_item_id', 'quantity', 'gear_category_id'], 'integer'],
            [['weight', 'volume'], 'number'],
            [['name'], 'string', 'max' => 255]
        ]);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacklistGears()
    {
        return $this->hasMany(\common\models\PacklistExtra::className(), ['event_extra_id' => 'id']);
    }

    public function createPacklist($packlist)
    {
        if (!$packlist)
        {
            $p = Packlist::find()->where(['event_id'=>$this->event_id])->orderBy(['main'=>SORT_DESC])->one();
            $packlist = $p->id;
        }
        $pg = new PacklistExtra();
        $pg->packlist_id = $packlist;
        $pg->quantity = $this->quantity;
        $pg->event_extra_id = $this->id;
        $pg->save();
    }

    public function updateCount()
    {
        $all = PacklistExtra::find()->where(['event_extra_id'=>$this->id])->all();
        if (!$all)
        {
            $this->delete();
        }else{
            $sum = 0;
            foreach ($all as $e)
            {
                $sum +=$e->quantity;
            }
                        $this->quantity = $sum;
            $this->save();
        }
    }
	
}

<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EventConflict;
use common\helpers\ArrayHelper;

/**
 * common\models\EventOterGearSearch represents the model behind the search form about `common\models\EventOuterGear`.
 */
 class EventConflictSearch extends EventConflict
{
    /**
     * @inheritdoc
     */

    public $gear_category_id;
    public $manager_id;

    public function rules()
    {
        return [
            [['event_id', 'gear_id', 'quantity', 'resolved', 'added', 'gear_category_id', 'manager_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = EventConflict::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['update_time'=>SORT_DESC]]
        ]);
        $myDate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-14 days" ) );
        $eventIds = ArrayHelper::map(Event::find()->where(['>', 'event_end', $myDate])->asArray()->all(), 'id', 'id');
        $query->andWhere(['in', 'event_id', $eventIds]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        if ($this->gear_category_id)
        {
            $categoryIds = [];
            $ids = [];
            $tmpCat = GearCategory::findOne($this->gear_category_id);

            if ($tmpCat !== null)
            {
                $ids = $tmpCat->children()->column();
            }

            $categoryIds = array_merge([$this->gear_category_id], $ids);
            $gear_ids = ArrayHelper::map(Gear::find()->where(['category_id'=>$categoryIds])->asArray()->all(), 'id', 'id');
            $query->andWhere(['IN', 'gear_id', $gear_ids]);
        }
        if ($this->manager_id)
        {
            $query->leftJoin('event', 'event.id = event_conflict.event_id')
                ->andWhere(['event.manager_id' => $this->manager_id]);
        }
        $query->andFilterWhere([
            'event_id' => $this->event_id,
            'gear_id' => $this->gear_id,
            'quantity' => $this->quantity,
            'resolved' => $this->resolved,
            ]);

        return $dataProvider;
    }
}

<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Attachment;
use yii\helpers\ArrayHelper;
/**
 * AttachmentSearch represents the model behind the search form about `common\models\Attachment`.
 */
class AttachmentSearch extends Attachment
{
    /**
     * @inheritdoc
     */

    public $event_name;
    public function rules()
    {
        return [
            [['id', 'type', 'status', 'event_id'], 'integer'],
            [['filename', 'extension', 'content', 'create_time', 'update_time', 'info', 'mime_type', 'base_name', 'event_name'], 'safe'],
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
        $query = Attachment::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->event_name)
        {
            $ids = ArrayHelper::map(Event::find()->where(['like', 'name', $this->event_name])->orWhere(['like', 'code', $this->event_name])->asArray()->all(), 'id', 'id');
            $query->where(['event_id'=>$ids]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'event_id' => $this->event_id,
        ]);

        $query->andFilterWhere(['like', 'filename', $this->filename])
            ->andFilterWhere(['like', 'extension', $this->extension])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'mime_type', $this->mime_type])
            ->andFilterWhere(['like', 'base_name', $this->base_name]);

        return $dataProvider;
    }
}

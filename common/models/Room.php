<?php

namespace common\models;
use Yii;
use \common\models\base\Room as BaseRoom;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "room".
 */
class Room extends BaseRoom
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['podkowa', 'bankiet', 'teatr', 'location_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ]);
    }
    public function getAssignedPhotos($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getRoomPhotos();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }
	
}

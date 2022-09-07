<?php

namespace common\models;

use Yii;
use \common\models\base\VehicleModel as BaseVehicleModel;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "vehicle_model".
 */
class VehicleModel extends BaseVehicleModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['capacity', 'volume'], 'number'],
            [['capacity_people'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ]);
    }

            public function getTranslates($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getVehicleTranslates();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

	
}

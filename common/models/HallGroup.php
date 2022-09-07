<?php

namespace common\models;

use Yii;
use \common\models\base\HallGroup as BaseHallGroup;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "hall_group".
 */
class HallGroup extends BaseHallGroup
{
    /**
     * @inheritdoc
     */

        public $hallIds;

    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['area', 'width', 'length', 'height'], 'number'],
            [['description'], 'string'],
            [['name', 'main_photo'], 'string', 'max' => 255],
            [['hallIds'], 'each', 'rule'=>['integer']],
        ]);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['link'] = [
            'class' => \common\behaviors\LinkBehavior::className(),
            'attributes' => [
                'hallIds',
            ],
            'relations' => [
                'halls',
            ],
            'modelClasses'=>[
                'common\models\Hall',
            ],
        ];

        return $behaviors;
    }

        public function getPhotoUrl()
    {
        if ($this->main_photo == null)
        {
            return null;
        }
        else
        {
            return Yii::getAlias('@uploads/hall/'.$this->main_photo);
        }

    }

        public function getAssignedHallGroupPhotos()
    {

        $query = $this->getHallGroupPhotos();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

        public function getAssignedHallGroupGears()
    {

        $query = $this->getHallGroupGears();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }
        public function getAssignedHallGroupCosts()
    {

        $query = $this->getHallGroupCosts();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }	
        public function getAssignedHallGroupPrices()
    {

        $query = $this->getHallGroupPrices();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getEventsNear($start, $end)
    {
        return [];
    }

    public function getEventsOverlapping($start, $end)
    {
        $ids = [];
        foreach ($this->halls as $hall)
        {
            $ids[] = $hall->id;
        }
        $hg_ids = \common\helpers\ArrayHelper::map(HallHallGroup::find()->where(['hall_id'=>$ids])->asArray()->all(), 'hall_group_id', 'hall_group_id');
        $overlapping = EventHallGroup::find()->where(['hall_group_id'=>$hg_ids])->andWhere(['<', 'start_time', $end])->andWhere(['>', 'end_time', $start])->all();
        return $overlapping;
    }
}

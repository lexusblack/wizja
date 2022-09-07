<?php

namespace common\models;
use common\helpers\ArrayHelper;

use Yii;
use \common\models\base\GearServiceStatut as BaseGearServiceStatut;

/**
 * This is the model class for table "gear_service_statut".
 */
class GearServiceStatut extends BaseGearServiceStatut
{
    /**
     * @inheritdoc
     */

    public $permissions;

    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name'], 'required'],
            [['type', 'in_menu', 'active'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 45]
        ]);
    }

    public function getPermissionIds()
    {
        return ArrayHelper::map(GearServiceStatutPermission::find()->where(['gear_service_statut_id'=>$this->id])->asArray()->all(), 'permission_group_id', 'permission_group_id');
    }

    public function linkPermissions($ids)
    {
        GearServiceStatutPermission::deleteAll(['gear_service_statut_id'=>$this->id]);

        if ($ids)
        {
            foreach ($ids as $id)
            {
                $p = new GearServiceStatutPermission;
                $p->permission_group_id = $id;
                $p->gear_service_statut_id = $this->id;
                $p->save();
            }           
        }

    }

    public function getUserList()
    {
        $statuts = GearServiceStatut::find()->where(['active'=>1])->all();
        $permissions = ArrayHelper::map(AuthAssignment::find()->where(['user_id'=>Yii::$app->user->id])->asArray()->all(), 'item_name', 'item_name');
        $statusArray = [];
        foreach ($statuts as $s)
        {
            if (!$s->getPermissionIds())
            {
                $statusArray[$s->id] = $s->name;
            }else{
                $perm = AuthAssignment::find()->where(['user_id'=>Yii::$app->user->id])->andWhere(['item_name'=> $s->getPermissionIds()])->one();
                if ($perm)
                {
                    $statusArray[$s->id] = $s->name;
                }
            }
        }
        return $statusArray;
    }

    public function getTypes()
    {
        return[
        0=>Yii::t('app', 'Brak'),
        1=>Yii::t('app', 'Ściąga sprzęt ze stanu magazynowego'),
        2=>Yii::t('app', 'Zwraca sprzęt do stanu magazynowego'),
        3=>Yii::t('app', 'Oznacza sprzęt w magazynie')
        ];
    }

    public function getServices($params=null)
    {
        $searchModel = new GearServiceSearch();
        $searchModel->status = $this->id;

        return count($searchModel->search($params, false)->getModels());
    }

    public function getLabels()
    {
        $statuts = GearServiceStatut::find()->where(['active'=>1])->andWhere(['in_menu'=>1])->all();
        $return = "";
        foreach ($statuts as $statut)
        {
            $return .="<span class='label' style='background-color:".$statut->color."; color:white; margin-left:2px;'>".$statut->getServices()."</span>";
        }
        return $return;
    }
	
}

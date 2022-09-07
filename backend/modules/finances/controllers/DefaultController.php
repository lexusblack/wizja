<?php

namespace backend\modules\finances\controllers;

use backend\components\Controller;
use common\models\GearItem;
use common\models\Gear;
use common\models\Invoice;

/**
 * Default controller for the `finances` module
 */
class DefaultController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout = '@backend/themes/e4e/layouts/main-panel';
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionListItem($q=null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
//        if (!is_null($q)) {
        $data = GearItem::getList($q);
        $out['results'] = [];
        foreach ($data as $id=>$model)
        {
            $out['results'][] = [
                'id' => 'gi_'.$model->id,
                'text' => $model->name.' ['.$model->number.']',
                'item_class'=>GearItem::className(),
                'item_id'=>$model->id,
            ];
        }

//        }
//        elseif ($id > 0) {
//            $out['results'] = ['id' => $id, 'text' => City::find($id)->name];
//        }
        return $out;


//        return Json::encode(Contact::getList($id, $q));
    }

	public function actionListOwners($q=null, $type)
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$out = ['results' => ['id' => '', 'text' => '']];
		$data = Invoice::getOwnerList($type);
		$out['results'] = [];
		foreach ($data as $id=>$name) {
		    if ($q != null) {
		        if (strpos(strtolower($id), strtolower($q)) || strpos(strtolower($name), strtolower($q))) {
                    $out['results'][] = ['id' => $id, 'text' => $name,];
                }
            }
            else {
                $out['results'][] = ['id' => $id, 'text' => $name,];
            }
		}

		return $out;
	}

    public function actionItemData($id)
    {
        //$id = str_replace('gi_', '', $id);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;


        $out = [];
        $model = Gear::findOne($id);
        $out= [
            'id' => $model->id,
            'text' => $model->name,
            'item_class'=>Gear::className(),
            'item_id'=>$model->id,
            'price'=>$model->price,
        ];



        return $out;


//        return Json::encode(Contact::getList($id, $q));
    }

//    public function actionFix()
//    {
//        Invoice::updateAll(['number'=>null]);
//        $models = Invoice::find()->where(['number'=>null])->all();
//        foreach ($models as $model)
//        {
//            $model->attributesUpdate();
//            $model->save();
//        }
//    }
//
//    public function actionStore()
//    {
//        $models = Invoice::find()->all();
//        foreach ($models as $model)
//        {
//            $model->storeData();
//        }
//        $models = Expense::find()->all();
//        foreach ($models as $model)
//        {
//            $model->storeData();
//        }
//    }
}

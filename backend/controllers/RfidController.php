<?php


namespace backend\controllers;


use backend\components\Controller;
use common\models\BarCode;
use common\models\GearGroup;
use common\models\GearGroupSearch;
use common\models\GearItem;
use common\models\GearItemSearch;
use common\models\GearItemsNoItemsRfid;
use common\models\RfidReadings;
use common\models\RfidLog;
use Faker\Provider\DateTime;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\Response;

class RfidController extends Controller {


    public $enableCsrfValidation = false;

    public function actionLastReadings() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $codes = [];
        $gears = [];
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $date = new \DateTime();
        $seconds = 10;
        $add = new \DateInterval("PT" . $seconds . "S");
        $date->sub($add);

        $readings = RfidLog::find()->where([">", "datetime", $date->format("Y-m-d H:i:s")])->all();
        foreach ($readings as $reading) {
            if (!in_array($reading->tag, $codes)) {
                $codes[] = $reading->tag;
                $id = self::findGearByRfid($reading->tag);
                if ($id) {
                    $gears[] = $id;
                }
            }
        }

        return $gears;
    }

    public function actionLastReadingsTest() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [['gear', 13, 'code', 'aaabbb']];
    }

    public static function findGearByRfid($rfid_code) {
        if ($gear = GearItem::find()->where(['rfid_code' => $rfid_code])->one()) {
            return ['gear', $gear->id, 'code', $rfid_code];
        }
        if ($gear = GearItemsNoItemsRfid::find()->where(['rfid_code' => $rfid_code])->one()) {
            return ['gear', $gear->gear_item_id, 'code', $rfid_code];
        }
        if ($case = GearGroup::find()->where(['rfid_code' => $rfid_code])->one()) {
            return ['case', $case->id];
        }
        return null;
    }

    public function actionIndex($tab = "model") {
        $activeModelsTab = true;
        $viewText = "_tabModels";
        $activeNoModelsTab = false;
        $activeCaseTab = false;
        $activeLastCode = false;
        if ($tab == "nomodels") {
            $activeModelsTab = false;
            $activeNoModelsTab = true;
            $viewText = "_tabModelsNoItems";
        }
        if ($tab == "case") {
            $activeModelsTab = false;
            $activeCaseTab = true;
            $viewText = "_tabCases";
        }
        if ($tab == "lastcode") {
            $activeModelsTab = false;
            $activeLastCode = true;
            $viewText = "_lastRfidCode";
        }

        $lastRfidCode = RfidReadings::find()->orderBy(['gr_id' => SORT_DESC])->one();

        $gearItemSearchModel = new GearItemSearch();
        $gearItemDataProvider = $gearItemSearchModel->search(Yii::$app->request->queryParams);
        $gearItemDataProvider->query->andWhere(['active'=>1])->andWhere(['type' => GearItem::TYPE_NORMAL]);

        $casesSearchModel = new GearGroupSearch();
        $casesDataProvider = $casesSearchModel->search(Yii::$app->request->queryParams);
        $casesDataProvider->query->andWhere(['active'=>1]);

        $gearItemNoItemsSearchModel = new GearItemSearch();
        $gearItemNoItemsDataProvider = $gearItemNoItemsSearchModel->search(Yii::$app->request->queryParams);
        $gearItemNoItemsDataProvider->query->andWhere(['active'=>1])->andWhere(['type' => GearItem::TYPE_NO_ITEM]);

        $gearItemNoItemsRfid = [];
        /** @var \common\models\GearItem $item */
        $gearItemNoItemSearch = null;
        if (isset($_GET["search"])) {
            $gearItemNoItemSearch = $_GET["search"];
        }
        foreach ($gearItemNoItemsDataProvider->getModels() as $item) {
            if ($gearItemNoItemSearch) {
                if (strpos(strtolower($item->gear->name), strtolower($gearItemNoItemSearch)) === false ) {
                    continue;
                }
            }
            foreach ($item->gearItemsNoItemsRfid as $gearRfid) {
                $gearItemNoItemsRfid[] = $gearRfid;
            }
            if (count($item->gearItemsNoItemsRfid) !== $item->gear->quantity && count($item->gearItemsNoItemsRfid) < $item->gear->quantity) {
                for ($i = count($item->gearItemsNoItemsRfid); $i < $item->gear->quantity; $i++) {
                    $newRfid = new GearItemsNoItemsRfid();
                    $newRfid->gear_item_id = $item->id;
                    $gearItemNoItemsRfid[] = $newRfid;
                }
            }
        }
        $gearItemNoItemsDataProvider = new ArrayDataProvider([
            'allModels' => $gearItemNoItemsRfid,
        ]);

        return $this->render('index', [
            'activeModels' => $activeModelsTab,
            'activeNoModels' => $activeNoModelsTab,
            'activeCase' => $activeCaseTab,
            'activeLastCode' => $activeLastCode,
            'viewText' => $viewText,

            'casesDataProvider' => $casesDataProvider,
            'casesSearchModel' => $casesSearchModel,

            'gearItemSearchModel' => $gearItemSearchModel,
            'gearItemDataProvider' => $gearItemDataProvider,

            'gearItemNoItemsDataProvider' => $gearItemNoItemsDataProvider,

            'lastRfidCode' => $lastRfidCode,
        ]);
    }

    public function actionUpdateGearItemsRfid() {
        $gearItems = Yii::$app->request->post('model');
        foreach ($gearItems as $id => $rfid_code) {
            if ($model = GearItem::findOne($id)) {
                $model->rfid_code = $rfid_code;
                $model->save();
            }
        }
    }

    public function actionUpdateCasesRfid() {
        $cases = Yii::$app->request->post('model');
        foreach ($cases as $id => $rfid_code) {
            if ($model = GearGroup::findOne($id)) {
                $model->rfid_code = $rfid_code;
                $model->save();
            }
        }
    }

    public function actionUpdateGearItemsNoItemRfid() {
        $gearItems = Yii::$app->request->post('model');
        $gearItemsId = Yii::$app->request->post('model_id');
        foreach ($gearItems as $id => $rfid_code) {
            if (substr($id, 0, 4) == 'null') {
                $model = new GearItemsNoItemsRfid();
                $model->rfid_code = $rfid_code;
                $model->gear_item_id = $gearItemsId[substr($id, 4, strlen($id) - 4)];
                $model->save();
            }
            else {
                if ($model = GearItemsNoItemsRfid::findOne($id)) {
                    $model->rfid_code = $rfid_code;
                    $model->save();
                }
            }
        }
    }
}
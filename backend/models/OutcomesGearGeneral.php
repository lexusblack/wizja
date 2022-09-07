<?php

namespace backend\models;

use common\models\IncomesGearOur;
use common\models\IncomesGearOuter;
use common\models\OutcomesGearOur;
use common\models\OutcomesGearOuter;
use common\models\BarCode;
use common\models\Gear;
use common\models\GearGroup;
use common\models\GearItem;
use common\models\OuterGear;
use yii\base\Exception;

class OutcomesGearGeneral extends \yii\base\Model {
    public $id;
    public $quantity;
    // rodzaj przedmiotu - nasz magazyn, zewnetrzny, albo grupa sprzetow
    public $warehouse;
    public $isNewRecord = true;

    public function rules() {
        return [
          [['quantity', 'id', 'warehouse'], 'required'],
          ['quantity', 'integer'],
        ];
    }

    public function saveOutcomes($outcome_id) {
        // sprzet z naszego magazynu
        if ($this->warehouse == BarCode::SINGEL_PRODUCT . BarCode::OUR_WAREHOUSE) {
            $this->saveOutcomesGearOur($outcome_id);
        }

        // sprzet z zewnetrznego magazynu
        if ($this->warehouse == BarCode::SINGEL_PRODUCT . BarCode::OUTER_WAREHOUSE) {
            $this->saveOutcomesGearOuter($outcome_id);
        }
    }

    private function saveOutcomesGearOur($outcome_id) {
        if ($this->quantity <= Gear::find()->where(['id' => $this->id])->one()->numberOfAvailable()) {
            $new_gear = new OutcomesGearOur();
            $new_gear->gear_id = $this->id;
            $new_gear->outcome_id = $outcome_id;
            $new_gear->gear_quantity = $this->quantity;
            $new_gear->save();
        }
    }

    private function saveOutcomesGearOuter($outcome_id) {
        if ($this->quantity <= OuterGear::find()->where(['id' => $this->id])->one()->numberOfAvailable()) {
            $new_gear = new OutcomesGearOuter();
            $new_gear->outer_gear_id = $this->id;
            $new_gear->outcome_id = $outcome_id;
            $new_gear->gear_quantity = $this->quantity;
            $new_gear->save();
        }
    }

    private function saveOutcomesGearItem($outcome_id, $gear_id) {
        if (Gear::find()->where(['id' => $gear_id])->one()->numberOfAvailable() > 0) {
            $new_gear = new OutcomesGearOur();
            $new_gear->gear_id = $gear_id;
            $new_gear->outcome_id = $outcome_id;
            $new_gear->gear_quantity = 1;
            $new_gear->save();
        }
        else {
            throw new Exception(Yii::t('app', 'Nie można wydać zestawu, brak wystarczającej ilości przedmiotów w magazynie'));
        }
    }


    // =========== incomes

    public function saveIncomes($income_id) {
        // sprzet z naszego magazynu
        if ($this->warehouse == BarCode::SINGEL_PRODUCT . BarCode::OUR_WAREHOUSE) {
            $this->saveIncomesGearOur($income_id);
        }

        // sprzet z zewnetrznego magazynu
        if ($this->warehouse == BarCode::SINGEL_PRODUCT . BarCode::OUTER_WAREHOUSE) {
            $this->saveIncomesGearOuter($income_id);
        }
    }

    private function saveIncomesGearOur($income_id) {
        $new_gear = new IncomesGearOur();
        $new_gear->gear_id = $this->id;
        $new_gear->income_id = $income_id;
        $new_gear->quantity = $this->quantity;
        $new_gear->save();
    }

    private function saveIncomesGearOuter($income_id) {
        $new_gear = new IncomesGearOuter();
        $new_gear->outer_gear_id = $this->id;
        $new_gear->income_id = $income_id;
        $new_gear->gear_quantity = $this->quantity;
        $new_gear->save();
    }

    private function saveIncomesGearItem($income_id, $gear_id) {
        $new_gear = new IncomesGearOur();
        $new_gear->gear_id = $gear_id;
        $new_gear->income_id = $income_id;
        $new_gear->quantity = 1;
        $new_gear->save();
    }
}
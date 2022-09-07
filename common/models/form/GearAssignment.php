<?php
namespace common\models\form;

use common\models\Event;
use common\models\Offer;
use common\models\GearItem;
use common\models\Gear;
use common\models\Rent;
use Yii;
use yii\base\Model;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class GearAssignment extends Model
{
    const SCENARIO_QUANTITY = 'quantity';


    public $quantity;
    public $oldQuantity;

    public $startTime;
    public $endTime;
    public $dateRange;

    /* @var \common\models\Gear */
    public $gear;
    public $itemId;
    public $targetId;
    public $targetClass;
    public $saveNull = false;

    /* @var Event|Rent */
    protected $_owner;

    /* @var GearItem */
    public $gearItem;

    /* @var \common\models\form\WarehouseSearch */
    public $warehouse;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_QUANTITY] = ['quantity', 'itemId', 'oldQuantity'];
        $scenarios[self::SCENARIO_DEFAULT] = ['itemId', 'startTime', 'endTime', 'dateRange'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['quantity', 'itemId'], 'required'],
            [['quantity', 'oldQuantity'], 'integer'],
            [['itemId'], 'exist', 'targetClass'=>Gear::className(), 'targetAttribute'=>'id'],
            [['quantity'], 'validateQuantity'],
            [['startTime', 'endTime', 'dateRange'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'quantity' => Yii::t('app', 'Ilość'),
        ];
    }

    public function save()
    {
        if ($this->validate())
        {
            $className = $this->targetClass;
            if ($this->scenario == self::SCENARIO_QUANTITY)
            {
                if (($this->quantity>0)||($this->saveNull))
                {
                    $conflict = $className::assignGear($this->targetId, $this->itemId, $this->quantity);
                    if ($conflict==='conflict')
                    {
                        $this->addError('conflict', Yii::t('app', 'Sprzęt zarezerwowany częściowo. Rozwiąż konfilt w odpowiedniej zakładce w wydarzeniu.'));
                        return false;
                    }
                    if ((!$conflict)&&(!$this->saveNull))
                    {
                        $this->addError('dateRange', Yii::t('app', 'Brak wystarczającej liczby sztuk w tym terminie'));
                        return false;
                    }

                }
                else
                {
                    if ($this->oldQuantity>0)
                        $className::removeGear($this->targetId, $this->itemId);
                }
            }
            else
            {
                $params = [
                    'start_time' => $this->startTime,
                    'end_time' => $this->endTime,
                ];
                if ($className::assignGearItem($this->targetId, $this->itemId, $this->quantity, $params) == false)
                {
                    $this->addError('dateRange', Yii::t('app', 'Nie można przypisać w tym terminie'));
                    return false;
                }
            }

            return true;
        }
        else
        {
            return false;
        }

    }

    public function validateQuantity($attribute, $params)
    {
        return true;
        $gear = $this->getGear();
        $all = $gear->quantity;
        $available = $this->warehouse->getGearAvailableCount($gear) + $this->oldQuantity;
        $available = $available>0 ? $available :0;
        if ($this->quantity > $available)
        {
            $this->addError($attribute, Yii::t('app', 'Dostępna ilość to: ').$available);
            return false;
        }
    }

    public function getGear()
    {
        $model = Gear::findOne($this->itemId);
        return $model;
    }

    public function getGearItem()
    {
        if ($this->gearItem == null)
        {
            $model = GearItem::findOne($this->itemId);
            if ($model === null)
            {
                throw new NotFoundHttpException();
            }
            $this->gearItem = $model;
        }
        return $this->gearItem;
    }

    public function getOwner()
    {
        if ($this->_owner === null)
        {
            $className = $this->targetClass;
            $model = $className::findOne($this->targetId);
            if ($model === null)
            {
                throw new NotFoundHttpException();
            }
            $this->_owner = $model;
        }
        return $this->_owner;
    }
    public function setOwner($model)
    {
        $this->_owner = $model;
    }

    public function initDates()
    {
        $this->startTime = $this->warehouse->from_date;
        $this->endTime = $this->warehouse->to_date;
    }
}
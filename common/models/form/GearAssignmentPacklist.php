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

class GearAssignmentPacklist extends Model
{

    public $itemId;
    public $quantity;
    public $oldQuantity = 0;

    public $startTime;
    public $endTime;

    /* @var \common\models\Gear */
    public $gear;
    public $packlist;
    public $addOld;


    public function rules()
    {
        return [
            [['quantity', 'itemId'], 'required'],
            [['startTime', 'endTime', 'quantity', 'oldQuantity'], 'safe'],
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

            $conflict = Event::assignGearToPacklist($this->packlist, $this->itemId, $this->quantity, $this->startTime, $this->endTime, $this->addOld);
            return $conflict;
        }
        else
        {
            return false;
        }

    }


}
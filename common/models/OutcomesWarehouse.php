<?php

namespace common\models;

use kartik\mpdf\Pdf;
use Yii;

/**
 * This is the model class for table "outcomes_warehouse".
 *
 * @property integer $id
 * @property integer $user
 * @property string $start_datetime
 * @property string $comments
 *
 * @property OutcomesForEvent[] $outcomesForEvents
 * @property OutcomesForRent[] $outcomesForRents
 * @property OutcomesGearOur[] $outcomesGearOur
 * @property OutcomesGearOuter[] $outcomesGearOuters
 * @property User $user0
 */
class OutcomesWarehouse extends \common\models\base\OutcomesWarehouse
{

    // event/rent/customer -> what kind of event is this outcome for
    public $event_type;
    public $items;
    public $groups;
    // event_id or rent_id or customer_id -> this outcomes is for that client
    public $event_id;
    public $rent_id;
    public $customer_id;

    // dla wyszukiwania
    public $gear;

    const EVENT_TYPE_NONE   = 0;
    const EVENT_TYPE_EVENT  = 1;
    const EVENT_TYPE_RENT   = 2;

    public static function getEventType() {
        return [
            //self::EVENT_TYPE_NONE => Yii::t('app', 'Żadne'),
            self::EVENT_TYPE_EVENT => Yii::t('app', 'Wydarzenie'),
            self::EVENT_TYPE_RENT => Yii::t('app', 'Wypożyczenie')
        ];
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outcomes_warehouse';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user', 'start_datetime'], 'required'],
            [['user'], 'integer'],
            [['start_datetime', 'event_type', 'warehouse_id'], 'safe'],
            [['comments'], 'string'],
            [['user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user' => 'id']],
            [
                'customer_id', 'required', 'when' => function ($model) {
                    return $model->event_type == self::EVENT_TYPE_NONE;
                }, 'whenClient' => "function (attr, val) {
                    return $('#event_type').val() == ".self::EVENT_TYPE_NONE.";
                }"
            ],
            [
                'event_id', 'required', 'when' => function ($model) {
                    return $model->event_type == self::EVENT_TYPE_EVENT;
                }, 'whenClient' => "function (attr, val) {
                        return $('#event_type').val() == ".self::EVENT_TYPE_EVENT.";
                }"
            ],
            [
                'rent_id', 'required', 'when' => function ($model) {
                    return $model->event_type == self::EVENT_TYPE_RENT;
                }, 'whenClient' => "function (attr, val) {
                        return $('#event_type').val() == ".self::EVENT_TYPE_RENT.";
                }"
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user' => Yii::t('app', 'Wydał'),
            'start_datetime' => Yii::t('app', 'Data wydania'),
            'comments' => Yii::t('app', 'Uwagi'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcomesForEvents()
    {
        return $this->hasMany(OutcomesForEvent::className(), ['outcome_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcomesForRents()
    {
        return $this->hasMany(OutcomesForRent::className(), ['outcome_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcomesForCustomers()
    {
        return $this->hasMany(OutcomesForCustomer::className(), ['outcome_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcomesGearOurs()
    {
        return $this->hasMany(OutcomesGearOur::className(), ['outcome_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcomesGearOuters()
    {
        return $this->hasMany(OutcomesGearOuter::className(), ['outcome_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser0()
    {
        return $this->hasOne(User::className(), ['id' => 'user']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomesWarehouses()
    {
        return $this->hasMany(IncomesWarehouse::className(), ['outcome_id' => 'id']);
    }

    public function generatePdf() {
        $content = "
        
            <div style='text-align: center; width: 100%; font-size: 30px; padding-top: 30px;'>".Yii::t('app', 'Wydanie z magazynu')."</div>
        
            <div class='half_page '>
                ".Yii::t('app', 'Numer').": 
                <hr>
            </div>
           
            
            <div class='half_page '>
                ".Yii::t('app', 'Dla kogo').": 
                <hr>
            </div>
            
             <div class='half_page ' style='padding-top: 5px; '>WZ-".$this->id."</div>
             <div class='half_page ' style='padding-top: 5px; '>".$this->getForWhoIsOutcome()."</div>
            
            <div class='whole_page'>
                ".Yii::t('app', 'Sprzęt').":
                <hr>
            </div>
            
            ".$this->getGearWithLeaveWarehouse()."
            
            <div class='whole_page'>
                ".Yii::t('app', 'Uwagi').":
                <hr>
            </div>
            <div>".$this->getComments()."</div>
            

            <div class='half_page '>
                <hr>
                ".Yii::t('app', 'Data').": ".$this->start_datetime."
            </div>
            
            <div class='half_page '>
                <hr>
                ".Yii::t('app', 'Wydał').": ".User::find()->where(['id' => $this->user])->one()->username."
            </div>
            
        ";


        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            'filename' => Yii::t('app', 'wydanie_z_magazynu'),
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@frontend/web/admin/css/pdf.css',
            // any css to be embedded if required
            'cssInline' => '',
            // set mPDF properties on the fly
            'options' => ['title' => Yii::t('app', 'Raport wydania sprzętu z magazynu')],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader'=>[Yii::t('app', 'Raport wydania sprzętu z magazynu')],
                'SetFooter'=>['{PAGENO}'],
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    public function getForWhoIsOutcome() {
        $customer = $this->getOutcomesForCustomers()->one();
        $event = $this->getOutcomesForEvents()->one();
        $rent = $this->getOutcomesForRents()->one();

        if ($customer) {
            return Customer::find()->where(['id' => $customer->customer_id])->one()->name;
        }
        if ($event) {
            return Event::find()->where(['id' => $event->event_id])->one()->name;
        }
        if ($rent) {
            return Rent::find()->where(['id' => $rent->rent_id])->one()->name;
        }
    }

    public function getPM() {
        $event = $this->getOutcomesForEvents()->one();
        $rent = $this->getOutcomesForRents()->one();
        if ($event) {
            $e = Event::find()->where(['id' => $event->event_id])->one();
            if (isset($e->manager))
            {
                return $e->manager->displayLabel;
            }else{
                return "";
            }
        }
        if ($rent) {
            $e = Rent::find()->where(['id' => $rent->rent_id])->one();
            if (isset($e->manager))
            {
                return $e->manager->displayLabel;
            }else{
                return "";
            }
        }
        return "";
    }

     public function getForWhoIsOutcome2() {
        $customer = $this->getOutcomesForCustomers()->one();
        $event = $this->getOutcomesForEvents()->one();
        $rent = $this->getOutcomesForRents()->one();

        if ($customer) {
            return Customer::find()->where(['id' => $customer->customer_id])->one()->name;
        }
        if ($event) {
            return Event::find()->where(['id' => $event->event_id])->one()->customer;
        }
        if ($rent) {
            return Rent::find()->where(['id' => $rent->rent_id])->one()->customer;
        }
    } 

    public function getEventFor()
    {
        $event = $this->getOutcomesForEvents()->one();
        $rent = $this->getOutcomesForRents()->one();
        if ($event) {
            return Event::find()->where(['id' => $event->event_id])->one();
        }
        if ($rent) {
            return Rent::find()->where(['id' => $rent->rent_id])->one();
        }
        return false;
    }  

    public function getComments() {
        if ($this->comments) {
            return $this->comments;
        }
        return "Brak";
    }

    public function getGearWithLeaveWarehouse($values = true) {
        if ($values)
            $class = "width-1-10";
        else
            $class = "width-1-3";

        $result = "
            <div class='".$class." right-border bottom-border'>".Yii::t('app', 'Nr')."</div>
            <div class='width-1-3 right-border bottom-border'>".Yii::t('app', 'Nazwa')."</div>
            <div class='width-1-10 right-border bottom-border'>".Yii::t('app', 'Liczba sztuk')."</div>
            <div class='width-1-10 right-border bottom-border'>".Yii::t('app', 'Magazyn')."</div>
            
        ";
        if ($values)
            $result .="<div class='width-1-10 bottom-border'>".Yii::t('app', 'Wartość szt.')."</div><div class='width-1-10 bottom-border'>".Yii::t('app', 'Wartość')."</div>";
        $result .= "<div style='clear: both;'></div>";

        $total_value = 0;
        $gearOur = $this->getOutcomesGearOurs()->all();
        $gearOuter = $this->getOutcomesGearOuters()->all();
        $gearGroup = [];
        $formatter = Yii::$app->formatter;

        if (count($gearOur)) {
            $gears = [];
            $gear_ilosc_sztuk = [];
            foreach ($gearOur as $gear) {
                $gear_item = GearItem::find()->where(['id' => $gear->gear_id])->one();
                if ($gear_item->group_id == null) {
                    $gears[$gear_item->gear_id][] = $gear_item;
                }
                else {
                    $gearGroup[$gear_item->gear_id][$gear_item->group_id][] = $gear_item;
                }
                if ($gear->gear->gear->no_items == 1) {
                    $gear_ilosc_sztuk[$gear->gear_id] = $gear->gear_quantity;
                }
            }
            foreach ($gears as $gear_id => $gear_list) {
                $number_list = "";
                if ($gear_list[0]->gear->no_items != 1) {
                foreach ($gear_list as $gear_element) {
                    $number_list .= $gear_element->number . ", ";
                }
                }
                $number = count($gear_list);
                if ($gear_list[0]->gear->no_items == 1) {
                    $number = $gear_ilosc_sztuk[$gear_list[0]->id];
                    $number_list = " - ";
                }

                $result .=
                    "<div class='".$class."  right-border padding-top'>".$number_list . "</div>" .
                    "<div class='width-1-3 right-border padding-top'>".Gear::find()->where(['id' => $gear_id])->one()->name ."</div>" .
                    "<div class='width-1-10 right-border padding-top'>".$number."</div>".
                    "<div class='width-1-10 right-border padding-top'>".Yii::t('app', 'Wew.')."</div>";
                    if ($values)
                        $result .="
                            <div class='width-1-10 right-border padding-top'>".$formatter->asCurrency(Gear::find()->where(['id' => $gear_id])->one()->value)."</div>
                            <div class='width-1-10 right-border padding-top'>".$formatter->asCurrency(Gear::find()->where(['id' => $gear_id])->one()->value*$number)."</div>";

                    $result .="<div style='clear: both;'></div>";
                    $total_value+=Gear::find()->where(['id' => $gear_id])->one()->value*$number;

            }
        }
        if (count($gearOuter)) {
            foreach ($gearOuter as $gear) {
                $outerGear = OuterGear::find()->where(['id' => $gear->outer_gear_id])->one();
                $result .=
                    "<div class='".$class."  right-border padding-top'></div>" .
                    "<div class='width-1-3 right-border padding-top'>" .  $outerGear->name . ", ".Yii::t('app', 'firma').": " . $outerGear->company_name . "</div>" .
                    "<div class='width-1-10 right-border padding-top'>".$gear->gear_quantity."</div>".
                    "<div class='width-1-10 right-border padding-top'>".Yii::t('app', 'Zew.')."</div><div class='width-1-10 padding-top'></div>
                    <div style='clear: both;'></div>";
            }
        }
        if (count($gearGroup)) {
            foreach ($gearGroup as $gear_id => $groups) {
                $numbers = null;
                $count = 0;
                foreach ($groups as $group_id => $items) {
                    $numer_list = null;
                    $ids = [];
                    $count +=count($items);
                    foreach ($items as $item) {
                        $numer_list .= $item->number .", ";
                        $ids[] = $item->number;
                    }
                    $in_order = true;
                    for ($i = min($ids); $i < max($ids); $i++) {
                        if (!in_array($i, $ids)) {
                            $in_order = false;
                        }
                    }
                    if ($in_order) {
                        $numer_list = min($ids) . "-" . max($ids).", ";
                    }
                    $numbers .= $numer_list;
                }
                $value = $items[0]->gear->value;
                $result .=
                    "<div class='".$class."  right-border padding-top'>". $numbers . "</div>" .
                    "<div class='width-1-3 right-border padding-top'>" . $items[0]->gear->name . " </div>" .
                    "<div class='width-1-10 right-border padding-top'>".count($groups)."case (".$count." szt.)</div>".
                    "<div class='width-1-10 right-border padding-top'>".Yii::t('app', 'Wew.')."</div>";
                    if ($values)
                        $result .="
                            <div class='width-1-10 right-border padding-top'>".$formatter->asCurrency(Gear::find()->where(['id' => $gear_id])->one()->value)."</div>
                            <div class='width-1-10 right-border padding-top'>".$formatter->asCurrency(Gear::find()->where(['id' => $gear_id])->one()->value*$count)."</div>";
                    $result .="
                    <div style='clear: both;'></div>";
                    $total_value+=Gear::find()->where(['id' => $gear_id])->one()->value*$count;
            }

        }
            if ($values) {
            $result .=
                    "<div class='".$class."  right-border padding-top'></div>" .
                    "<div class='width-1-3 right-border padding-top'></div>" .
                    "<div class='width-1-10 right-border padding-top'></div>".
                    "<div class='width-1-10 right-border padding-top'></div>
                    <div class='width-1-10 right-border padding-top'>".Yii::t('app', 'Razem')."</div>
                    <div class='width-1-10 right-border padding-top'>".$formatter->asCurrency($total_value)."</div>
                    <div style='clear: both;'></div>";
                }
        return $result;
    }   

}

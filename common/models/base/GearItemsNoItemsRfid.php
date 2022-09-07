<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "gear_items_no_items_rfid".
 *
 * @property integer $id
 * @property integer $gear_item_id
 * @property integer $rfid_code
 *
 * @property \common\models\GearItem $gearItem
 * @property string $aliasModel
 */
abstract class GearItemsNoItemsRfid extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_items_no_items_rfid';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gear_item_id'], 'required'],
            [['gear_item_id'], 'integer'],
            [['rfid_code'], 'string'],
            [['gear_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => GearItem::className(), 'targetAttribute' => ['gear_item_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'gear_item_id' => Yii::t('app','ID egzemplarza'),
            'rfid_code' => Yii::t('app','Kod rfid'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearItem()
    {
        return $this->hasOne(\common\models\GearItem::className(), ['id' => 'gear_item_id']);
    }




}
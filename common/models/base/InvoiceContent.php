<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "invoice_content".
 *
 * @property integer $id
 * @property integer $invoice_id
 * @property integer $external_id
 * @property string $name
 * @property string $classification
 * @property string $unit
 * @property string $count
 * @property string $price
 * @property string $discount
 * @property integer $discount_percent
 * @property string $netto
 * @property string $brutto
 * @property integer $vat
 * @property string $tax
 * @property string $lumpcode
 * @property string $create_time
 * @property string $update_time
 * @property integer $item_id
 * @property string $item_class
 * @property string $item_tmp_name
 *
 * @property \common\models\Invoice $invoice
 * @property string $aliasModel
 */
abstract class InvoiceContent extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_content';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_id', 'external_id', 'discount_percent', 'vat', 'item_id'], 'integer'],
            [['name'], 'required'],
            [['count', 'price', 'discount', 'netto', 'brutto', 'tax'], 'number'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'classification', 'unit', 'lumpcode', 'item_class', 'item_tmp_name'], 'string', 'max' => 255],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoice_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'invoice_id' => Yii::t('app', 'ID przychodu'),
            'external_id' => Yii::t('app', 'ID zewnętrzne'),
            'name' => Yii::t('app', 'Nazwa'),
            'classification' => Yii::t('app', 'PKWiU'),
            'unit' => Yii::t('app', 'Jednostka'),
            'count' => Yii::t('app', 'Ilość'),
            'price' => Yii::t('app', 'Cena netto'),
            'discount' => Yii::t('app', 'Rabat?'),
            'discount_percent' => Yii::t('app', 'Rabat %'),
            'netto' => Yii::t('app', 'Wartość netto'),
            'brutto' => Yii::t('app', 'Wartość brutto'),
            'vat' => Yii::t('app', 'Stawka Vat'),
            'tax' => Yii::t('app', 'Wartość VAT'),
            'lumpcode' => Yii::t('app', 'Stawka ryczałtu'),
            'create_time' => Yii::t('app', 'Stworzono'),
            'update_time' => Yii::t('app', 'Zaktualizowano'),
            'item_id' => Yii::t('app', 'ID przedmiotu'),
            'item_class' => Yii::t('app', 'Klasa przedmiotu'),
            'item_tmp_name' => Yii::t('app', 'Nazwa'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(\common\models\Invoice::className(), ['id' => 'invoice_id']);
    }




}

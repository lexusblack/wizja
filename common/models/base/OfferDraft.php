<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "offer_draft".
 *
 * @property integer $id
 * @property string $name
 * @property integer $price_group_id
 * @property integer $firm_id
 * @property string $pdf_fields
 */
class OfferDraft extends \yii\db\ActiveRecord
{
    public $gear_fields;
    public $crew_fields;
    public $transport_fields;
    public $other_fields;
    public $footer_fields;
    public $header_fields;
    public $title_fields;
    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            ''
        ];
    }

    public function setFields()
    {
        $this->gear_fields = explode(";", $this->gear_pdf_fields);
        $this->crew_fields = explode(";", $this->crew_pdf_fields);
        $this->transport_fields = explode(";", $this->transport_pdf_fields);
        $this->other_fields = explode(";", $this->other_pdf_fields);
        $this->title_fields = explode(";", $this->title_pdf_fields);
        $this->footer_fields = explode(";", $this->footer_pdf_fields);
        $this->header_fields = explode(";", $this->header_pdf_fields);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price_group_id', 'firm_id', 'transport_section', 'crew_section', 'other_section', 'gear_section', 'footer_section', 'header_section'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 45],
            [['gear_pdf_fields', 'crew_pdf_fields', 'transport_pdf_fields', 'other_pdf_fields', 'title_pdf_fields', 'footer_pdf_fields', 'header_pdf_fields'], 'string'],
            [['gear_fields', 'crew_fields', 'transport_fields', 'other_fields', 'title_fields', 'header_fields', 'footer_fields'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_draft';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'price_group_id' => Yii::t('app', 'Grupa cenowa'),
            'firm_id' => Yii::t('app', 'Firma skłądająca ofertę'),
            'gear_pdf_fields' => Yii::t('app', 'Pola wyświetlane w pdf'),
            'gear_fields' => Yii::t('app', 'PDF - widoczność kolumn w sekcji sprzętowych'),
            'crew_fields' => Yii::t('app', 'PDF - widoczność kolumn w sekcji Obsługa'),
            'transport_fields' => Yii::t('app', 'PDF - widoczność kolumn w sekcji Transport'),
            'other_fields' => Yii::t('app', 'PDF - widoczność kolumn w sekcji Inne'),
            'gear_section' => Yii::t('app', 'PDF - widoczność sekcji sprzętowych'),
            'crew_section' => Yii::t('app', 'PDF - widoczność sekcji Obsługa'),
            'transport_section' => Yii::t('app', 'PDF - widoczność sekcji Transport'),
            'other_section' => Yii::t('app', 'PDF - widoczność sekcji Inne'),
            'header_section' => Yii::t('app', 'PDF - widoczność nagłówka'),
            'footer_section' => Yii::t('app', 'PDF - widoczność stopki'),
            'title_fields' => Yii::t('app', 'PDF - widoczność kolumn w sekcji tytułowej'),
            'header_fields' => Yii::t('app', 'PDF - widoczność kolumn w nagłówku'),
            'footer_fields' => Yii::t('app', 'PDF - widoczność kolumn w stopce'),
        ];
    }

    public function getGearFieldList()
    {
        return ['quantity'=>Yii::t('app', 'Liczba sztuk'), 'name'=>Yii::t('app', 'Nazwy sprzętów'), 'info'=>Yii::t('app', 'Opis krótki'), 'price'=>Yii::t('app', 'Cena jednostkowa'), 'discount'=>Yii::t('app', 'Rabat'), 'days'=>Yii::t('app', 'Dni pracy'), 'total_price'=>Yii::t('app', 'Cena łącznie'), 'photo'=>Yii::t('app', 'Zdjęcie sprzętu'), 'description'=>Yii::t('app', 'Długi opis sprzętu')];
    }

    public function getCrewFieldList()
    {
        return ['name'=>Yii::t('app', 'Nazwa roli'), 'description'=>Yii::t('app', 'Opis'),  'price_group'=>Yii::t('app', 'Nazwa stawki'), 'price'=>Yii::t('app', 'Cena jednostkowa'), 'days'=>Yii::t('app', 'Okres'), 'total_price'=>Yii::t('app', 'Cena łącznie')];
    }

    public function getTransportFieldList()
    {
        return ['name'=>Yii::t('app', 'Nazwy pojazdów'), 'description'=>Yii::t('app', 'Opis'),  'price_group'=>Yii::t('app', 'Nazwa stawki'), 'price'=>Yii::t('app', 'Cena jednostkowa'),  'km'=>Yii::t('app', 'Km/Okres'), 'total_price'=>Yii::t('app', 'Cena łącznie')];
    }

    public function getOtherFieldList()
    {
        return ['name'=>Yii::t('app', 'Nazwa'), 'price'=>Yii::t('app', 'Cena jednostkowa'), 'discount'=>Yii::t('app', 'Rabat'), 'total_price'=>Yii::t('app', 'Cena łącznie')];
    }

    public function getFooterFieldList()
    {
        return ['address'=>Yii::t('app', 'Dane firmy'), 'email'=>Yii::t('app', 'Telefon i adres mail'), 'bank'=>Yii::t('app', 'Dane do przelewu'), 'manager'=>Yii::t('app', 'Dane kierownika projektu'), 'gear_details'=>Yii::t('app', 'Dane techniczne sprzętu'), 'paying_date'=>Yii::t('app', 'Termin płatności')];
    }

    public function getHeaderFieldList()
    {
        return ['name'=>Yii::t('app', 'Nazwa oferty'), 'page'=>Yii::t('app', 'Strona'), 'number'=>Yii::t('app', 'numer oferty'), 'termin'=>Yii::t('app', 'Termin'), 'datetime'=>Yii::t('app', 'Data sporządzenia'), 'logo'=>Yii::t('app', 'Logo'), 'paying_date'=>Yii::t('app', 'Termin płatności')];
    }

    public function getTitleFieldList()
    {
        return ['name'=>Yii::t('app', 'Nazwa projektu'), 'location'=>Yii::t('app', 'Miejsce projektu'), 'harmonogram'=>Yii::t('app', 'Harmonogram'), 'client_name'=>Yii::t('app', 'Nazwa klienta'), 'client_address'=>Yii::t('app', 'Dane adresowe klienta'), 'manager'=>Yii::t('app', 'Dane project managera'), 'info'=>Yii::t('app', 'Uwagi'), 'number'=>Yii::t('app', 'numer oferty'),  'datetime'=>Yii::t('app', 'Data sporządzenia'), 'paying_date'=>Yii::t('app', 'Termin płatności')];
    }
    public function beforeSave($insert)
    {
        if ($this->gear_fields)
            $this->gear_pdf_fields = implode(';', $this->gear_fields);
        else
            $this->gear_pdf_fields = null;
        if ($this->crew_fields)
            $this->crew_pdf_fields = implode(';', $this->crew_fields);
        else
            $this->crew_pdf_fields = null;

        if ($this->transport_fields)
            $this->transport_pdf_fields = implode(';', $this->transport_fields);
        else
            $this->transport_pdf_fields = null;

        if ($this->other_fields)
            $this->other_pdf_fields = implode(';', $this->other_fields);
        else
            $this->other_pdf_fields = null;
        
        if ($this->footer_fields)
            $this->footer_pdf_fields = implode(';', $this->footer_fields);
        else
            $this->footer_pdf_fields = null;

        if ($this->header_fields)
            $this->header_pdf_fields = implode(';', $this->header_fields);
        else
            $this->header_pdf_fields = null;

        if ($this->title_fields)
            $this->title_pdf_fields = implode(';', $this->title_fields);
        else
            $this->title_pdf_fields = null;
        return parent::beforeSave($insert);
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceGroup()
    {
        return $this->hasOne(\common\models\PriceGroup::className(), ['id' => 'price_group_id']);
    }
}

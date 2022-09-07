<?php
namespace backend\models;

use Yii;
use common\components\SettingsTrait;
use yii\base\Model;
use yii\web\ForbiddenHttpException;

class SettingsForm extends Model
{
    use SettingsTrait;

    public $companyCity;
    public $companyZip;
    public $companyShortName;
    public $companyAddress;
    public $companyCountry;
    public $companyName;
    public $companyLogo;
    public $companyNIP;
    public $companyBankName;
    public $companyBankNumber;
    public $salesDepartmentPhone;
    public $salesDepartmentEmail;
    public $crossRentalEmail;
    public $crossRentalPhone;
    public $crossRentalUsers;
    public $crossRentalUsersArray;
    public $warehouseAddress;
    public $warehouseZip;
    public $warehouseCity;
    public $meetingColor;
    public $meetingLineColor;
    public $personalColor;
    public $rentColor;
    public $rentLineColor;
    public $vacationColor;
    public $vacationAcceptedColor;
    public $vacationRejectedColor;
    public $crewConfirm;
    public $produkcjaColor;
    public $produkcjaLineColor;
    public $biuroColor;
    public $biuroLineColor;
    public $grafikaColor;
    public $grafikaLineColor;
    public $magazynColor;
    public $magazynLineColor;

    public $meetingTextColor;
    public $personalTextColor;
    public $rentTextColor;
    public $vacationTextColor;
    public $vacationTextAcceptedColor;
    public $vacationTextRejectedColor;

    public $packingColor;
    public $montageColor;
    public $disassemblyColor;

    public $eventBaseColor;
    public $eventLineColor;
    public $partyColor;

    public $defaultCurrency;
    public $defaultInvoiceCurrency;
    public $eventNotifications;

    public $eventNumber;
    public $rentNumber;
    public $offerNumber;
    public $blackField;
    public $blackFieldArray;

    public $footerText;
    public $footerSize;
    public function getCurrencyList() {
        $currencies = [
            'PLN' => 'PLN',
            'USD' => 'USD',
            'EUR' => 'EURO',
            'CHF' => 'CHF',
            'GBP' => 'GBP',
            'SEK' => 'SEK'
        ];
        if (!key_exists($this->defaultInvoiceCurrency, $currencies)) {
            $currencies[$this->defaultInvoiceCurrency] = $this->defaultInvoiceCurrency;
        }

        return $currencies;
    }
    public function getCurrencyListAll() {
        $currencies = [
            'PLN' => 'PLN',
            'USD' => 'USD',
            'EUR' => 'EURO',
            'CHF' => 'CHF',
            'GBP' => 'GBP',
            'SEK' => 'SEK'
        ];

        return $currencies;
    }
    public function rules()
    {
        return [
            [[
                'eventNotifications',
                'companyCity',
                'companyShortName',
                'companyZip',
                'companyAddress',
                'companyCountry',
                'meetingColor',
                'meetingLineColor',
                'magazynColor',
                'magazynLineColor',
                'produkcjaColor',
                'produkcjaLineColor',
                'biuroColor',
                'biuroLineColor',
                'grafikaColor',
                'grafikaLineColor',
                'personalColor',
                'rentColor',
                'rentLineColor',
                'vacationColor',
                'vacationAcceptedColor',
                'vacationRejectedColor',
                'warehouseCity',
                'warehouseZip',
                'warehouseAddress',

                'meetingTextColor',
                'personalTextColor',
                'rentTextColor',
                'vacationTextColor',
                'vacationTextAcceptedColor',
                'vacationTextRejectedColor',

                'eventBaseColor',
                'eventLineColor',
                'partyColor',
                'packingColor',
                'montageColor',
                'disassemblyColor',
                'companyName',
                'companyLogo',
                'companyNIP',
                'companyBankNumber',
                'companyBankName',
                'salesDepartmentEmail',
                'salesDepartmentPhone',
                'crossRentalPhone',
                'crossRentalEmail',
                'defaultCurrency',
                'defaultInvoiceCurrency',
                'eventNumber',
                'rentNumber',
                'offerNumber',
                'blackField',
                'crossRentalUsers',
                'footerText',
                'footerSize'
            ], 'string'],
            [['crewConfirm'], 'integer'],
            [['blackFieldArray', 'crossRentalUsersArray'], 'safe']

        ];
    }


    public function getPhotoUrl()
    {
        $settings = \Yii::$app->settings;
		$value = $settings->get('main.companyLogo');
		$url = null;
		if($value){
			$url = \Yii::getAlias('@uploads' . '/settings/').$value;
		}
		return $url;
    }

    public function attributeLabels()
    {
        $labels = [
            'companyCity' => Yii::t('app', 'Miasto'),
            'companyZip' => Yii::t('app', 'Kod pocztowy'),
            'companyShortName' => Yii::t('app', 'Skrócona Nazwa'),
            'companyAddress' => Yii::t('app', 'Adres'),
            'warehouseCity' => Yii::t('app', 'Miasto'),
            'warehouseZip' => Yii::t('app', 'Kod pocztowy'),
            'warehouseAddress' => Yii::t('app', 'Adres'),
            'companyCountry' => Yii::t('app', 'Kraj'),
            'meetingColor' =>Yii::t('app', 'Spotkanie'),
            'produkcjaLineColor' => Yii::t('app', 'Produkcja (kolor paska)'),
            'produkcjaColor' =>Yii::t('app', 'Produkcja'),
            'meetingLineColor' => Yii::t('app', 'Spotkanie (kolor paska)'),
            'magazynColor' =>Yii::t('app', 'Prace magazynowe'),
            'magazynLineColor' => Yii::t('app', 'Prace magazynowe (kolor paska)'),
            'biuroColor' =>Yii::t('app', 'Prace biurowe'),
            'biuroLineColor' => Yii::t('app', 'Prace biurowe (kolor paska)'),
            'grafikaColor' =>Yii::t('app', 'Prace grafika'),
            'grafikaLineColor' => Yii::t('app', 'Prace grafika (kolor paska)'),
            'personalColor' => Yii::t('app', 'Wydarzenie prywatne'),
            'rentColor' => Yii::t('app', 'Wypożyczenie'),
            'rentLineColor' => Yii::t('app', 'Wypożyczenie (kolor paska)'),
            'vacationColor' => Yii::t('app', 'Urlop nowy'),
            'vacationAcceptedColor'=> Yii::t('app', 'Urlop zaakceptowany'),
            'vacationRejectedColor' => Yii::t('app', 'Urlop odrzucony'),

            'meetingTextColor' =>Yii::t('app', 'Spotkanie tekst'),
            'personalTextColor' => Yii::t('app', 'Wydarzenie prywatne  tekst'),
            'rentTextColor' => Yii::t('app', 'Wypożyczenie  tekst'),
            'vacationTextColor' => Yii::t('app', 'Urlop nowy  tekst'),
            'vacationTextAcceptedColor'=> Yii::t('app', 'Urlop zaakceptowany tekst'),
            'vacationTextRejectedColor' => Yii::t('app', 'Urlop odrzucony tekst'),
            'crewConfirm' => Yii::t('app', 'Ekipa potwierdza przypisanie do wydarzenia.'),

            'packingColor' => Yii::t('app', 'Pakowanie'),
            'montageColor' => Yii::t('app', 'Montaż'),
            'disassemblyColor' => Yii::t('app', 'Demontaż'),
            'companyName' => Yii::t('app', 'Nazwa Firmy'),
            'companyLogo' => Yii::t('app', 'Logo Firmy'),
            'companyNIP' => Yii::t('app', 'NIP'),
            'companyBankNumber' => Yii::t('app', 'Konto Bankowe'),
            'companyBankName' => Yii::t('app', 'Nazwa Banku'),
            'salesDepartmentPhone' => Yii::t('app', 'Tel'),
            'salesDepartmentEmail' => Yii::t('app', 'E-mail'),            
            'crossRentalPhone' => Yii::t('app', 'Tel'),
            'crossRentalEmail' => Yii::t('app', 'E-mail'),

            'eventBaseColor' => Yii::t('app', 'Kolor bazy'),
            'eventLineColor' => Yii::t('app', 'Kolor paska'),
            'partyColor' => Yii::t('app', 'Kolor eventu'),
            'defaultCurrency'=>Yii::t('app', 'Domyślna waluta systemowa'),
            'defaultInvoiceCurrency'=>Yii::t('app', 'Domyślna waluta faktur'),
            'eventNotifications' => Yii::t('app', 'Wysyłanie powiadomień dla eventów'),
            'eventNumber' => Yii::t('app', 'Numeracja eventów'),
            'rentNumber' => Yii::t('app', 'Numeracja wypożyczeń'),
            'offerNumber' => Yii::t('app', 'Numeracja ofert'),
            'blackFieldArray' => Yii::t('app', 'Informacje na tooltip w kalendarzu'),
            'crossRentalUsersArray' => Yii::t('app', 'Osoba odpowiedzialna za Cross Rental'),
            'footerText'=>Yii::t('app', 'Dodatkowy tekst w stopce'),
            'footerSize'=>Yii::t('app', 'Wysokość stopki [mm]'),
        ];

        return array_merge(parent::attributeLabels(), $labels);
    }

    public function attributeHints()
    {
        $hints = [

        ];
        return array_merge(parent::attributeHints(), $hints);
    }

    public function formName()
    {
        //settings section
        return 'main';
    }

    public function beforeValidate() {
        if (!Yii::$app->user->can('settingsCompanySave')) {
            throw new ForbiddenHttpException(Yii::t('yii', 'Nie jesteś uprawniony do wykonania tej akcji.'));
        }
        
        return parent::beforeValidate();
    }

}
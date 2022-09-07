<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OfferDraft */
/* @var $form yii\widgets\ActiveForm */

$titlefields = $params['OfferDraft']['title_fields'];
$gear_fields = $params['OfferDraft']['gear_fields'];
$gear_section = $params['OfferDraft']['gear_section'];
$crew_fields = $params['OfferDraft']['crew_fields'];
$crew_section = $params['OfferDraft']['crew_section'];
$header_section = $params['OfferDraft']['header_section'];

$transport_fields = $params['OfferDraft']['transport_fields'];
$transport_section = $params['OfferDraft']['transport_section'];
$header_fields = $params['OfferDraft']['header_fields'];
$footer_fields = $params['OfferDraft']['footer_fields'];
if (!$titlefields)
$titlefields = [];
if (!$gear_fields)
    $gear_fields = [];
if (!$transport_fields)
    $transport_fields = [];
if (!$crew_fields)
    $crew_fields = [];
if (!$header_fields)
    $header_fields = [];
if (!$footer_fields)
    $footer_fields = [];
if (!$title_fields)
    $title_fields = [];
$gear_fields_count = 7 - count($gear_fields);
             if (in_array('photo', $gear_fields)){
                $gear_fields_count++;
             }
             if (in_array('description', $gear_fields)){
                $gear_fields_count++;
             }
?>
<div class=col-lg-12>
<div class="ibox">
<div class="ibox-content">
<?php if ($header_section==1){ ?>
<div class="header">
    <table class="table half_cell">
        <tr>
        <td>
        <?php if (in_array('logo', $header_fields)){ ?>
            <div class="logo"><?= isset($settings['companyLogo']) ? Html::img(\Yii::getAlias('@uploadroot' . '/settings/').$settings['companyLogo']->value,['height'=>'100']) : '';?></div>
        <?php } ?>
        </td>
            <td>
                <table class="table half_cell">
                <?php if (in_array('name', $header_fields)){ ?>
                    <tr>
                        <td><?= Yii::t('app', 'Nazwa') ?>:</td>
                        <td>Nazwa oferty</td>
                    </tr>
                <?php } ?>
                <?php if (in_array('number', $header_fields)){ ?>
                    <tr>
                        <td><?= Yii::t('app', 'Numer oferty') ?>:</td>
                        <td>1</td>
                    </tr>
                <?php } ?>
                <?php if (in_array('termin', $header_fields)){ ?>
                    <tr>
                        <td><?= Yii::t('app', 'Termin') ?>:</td>
                        <td>2018-03-04 <?= Yii::t('app', 'do') ?> 2018-03-05</td>
                    </tr>
                <?php } ?>
                <?php if (in_array('datetime', $header_fields)){ ?>
                    <tr>
                        <td><?= Yii::t('app', 'Data sporządzenia oferty') ?>:</td>
                        <td>2018-02-05</td>
                    </tr>
                <?php } ?>
                <?php if (in_array('page', $header_fields)){ ?>
                    <tr>
                        <td><?= Yii::t('app', 'Strona') ?>:</td>
                        <td>1</td>
                    </tr>
                <?php } ?>
                </table>
            </td>
        </tr>
    </table>
</div>
<?php } ?>
<div class="pdf_box">
        <div class="client_info">
        <?php if (in_array('client_name', $titlefields)){ ?>
                    <div class="hb fl">
                <div class="upf"><b>zamawiający:</b></div>
                <h3><?=Yii::t('app', 'Nazwa kontahenta')?></h3>
        <?php if (in_array('client_address', $titlefields)){ ?>
                <p><?=Yii::t('app', 'Ulica testowa 99')?></p>
                <p>02-000 Warszawa</p>
                <p>NIP: XXX-XXX-XX-XX </p>
                
                <p>e-mail: xx@newsystems.pl</p>
                <p>tel.: +48 999 000 000</p>
        <?php } ?>
            </div>
        <?php } ?>
        <?php if (in_array('manager', $titlefields)){ ?>
                <div class="hb fl">
                <table class="table half_cell">
                    <tbody><tr>
                        <td><?=Yii::t('app', 'Kierownik projektu:')?></td>
                        <td>Jan Kowalski</td>
                    </tr>
                    <tr>
                        <td>tel:</td>
                        <td> +48 999 000 000</td>
                    </tr>
                    <tr>
                        <td>e-mail:</td>
                        <td>xx@newsystems.pl</td>
                    </tr>
                </tbody></table>
            </div>
        <?php } ?>
        </div> 
        
        <div class="main_info">
            <?php if (in_array('name', $titlefields)){ ?>
            <div class="name_box">
                <h1><?=Yii::t('app', 'Nazwa projektu:')?> Projekt testowy </h1>
            </div>
            <?php } ?>
            <?php if (in_array('location', $titlefields)){ ?>
            <p>Warszawa, Stadion Narodowy</p>
            <?php } ?>
            <?php if (in_array('harmonogram', $titlefields)){ ?>
            <div class="hb fl">
                <p><u><?=Yii::t('app', 'Harmonogram:')?></u></p>
                <table class="table table-row-border">
                    <tbody><tr>
                        <th><?=Yii::t('app', 'Typ:')?></th>
                        <th><?=Yii::t('app', 'Od:')?></th>
                        <th><?=Yii::t('app', 'Do:')?></th>
                    </tr>
                        <tr>
                            <td><?=Yii::t('app', 'Montaż:')?></td>
                            <td>2019-01-29 00:00:00</td>
                            <td>2019-01-29 23:00:00</td>
                        </tr>
                        <tr>
                            <td><?=Yii::t('app', 'Event:')?></td>
                            <td>2019-01-29 00:00:00</td>
                            <td>2019-01-29 23:00:00</td>
                        </tr>
                        <tr>
                            <td><?=Yii::t('app', 'Demontaż:')?></td>
                            <td>2019-01-29 00:00:00</td>
                            <td>2019-01-29 23:00:00</td>
                        </tr>                                        
                </tbody></table>
            </div>
            <?php } ?>
        </div>

<table class="table table-row-border offertable" cellpadding="5" cellspacing="0">
            <tbody>                        
                <tr style="background-color:#ffd966;">
                    <td colspan="7"><b><u><?=Yii::t('app', 'Nazwa sekcji:')?></u></b></td>
                </tr>
                <?php if ($gear_section==1){ ?>
                <tr>
                                    <?php if (in_array('name', $gear_fields)){ ?>
                                    <th colspan="<?=$gear_fields_count?>"><?=Yii::t('app', 'Nazwa')?></th>
                                    <?php } ?>
                                    <?php if (in_array('info', $gear_fields)){ ?>
                                    <th><?=Yii::t('app', 'Opis')?></th>
                                    <?php } ?>
                                    <?php if (in_array('price', $gear_fields)){ ?>
                                    <th><?=Yii::t('app', 'Cena')?></th>
                                    <?php } ?>
                                    <th style="text-align: center;"><?=Yii::t('app', 'Liczba')?></th>
                                    <?php if (in_array('discount', $gear_fields)){ ?>
                                    <th style="text-align: center;"><?=Yii::t('app', 'Rabat')?></th>
                                    <?php } ?>
                                    <?php if (in_array('days', $gear_fields)){ ?>
                                    <th style="text-align: center;"><?=Yii::t('app', 'Dni pracy')?></th>
                                    <?php } ?>
                                    <?php if (in_array('total_price', $gear_fields)){ ?>
                                    <th><?=Yii::t('app', 'Razem netto')?></th>
                                    <?php } ?>
                </tr>

                <tr style="background-color:#eee">
                <?php if (in_array('name', $gear_fields)){ ?>
                                                            <td colspan="<?=$gear_fields_count?>">ZESTAW DJ'SKI</td>
                            <?php } ?>
                                    <?php if (in_array('info', $gear_fields)){ ?>
                                                            <td>Opis do czego to</td>
                                    <?php } ?>
                                    <?php if (in_array('price', $gear_fields)){ ?>
                                                            <td>PLN 1,200.00</td>
                                    <?php } ?>
                                                            <td style="text-align: center;">1</td>
                                    <?php if (in_array('discount', $gear_fields)){ ?>
                                                            <td style="text-align: center;">0</td>
                                    <?php } ?>
                                    <?php if (in_array('days', $gear_fields)){ ?>
                                                            <td style="text-align: center;">1</td>
                                    <?php } ?>
                                    <?php if (in_array('total_price', $gear_fields)){ ?>
                                                            <td>PLN 1,200.00</td>
                                    <?php } ?>
                </tr>
                <?php if ((in_array('photo', $gear_fields))||(in_array('description', $gear_fields))){ ?>
                <tr>
                <?php if (in_array('photo', $gear_fields)){ ?>
                    <td colspan="2"><img src="/files/gear/barco-hdx-w20.jpg" height="100" alt=""></td>
                <?php } ?>
                <?php if (in_array('description', $gear_fields)){ ?>
                    <td colspan="5">Długi ois sprzętu, długi opis sprzętu, długi opis sprzętu</td>
                <?php } ?>
                 </tr>
                 <?php } ?>
                 <?php } ?>
                <tr class="warning">
                    <td colspan="6"><b><u>Łącznie sekcja</u></b></td>
                    <td >PLN 1,200.00</td>
                </tr>
            </tbody>
            </table> 
            <table class="table table-row-border offertable" cellpadding="5" cellspacing="0">
            <tbody><tr style="background-color:#8e7cc3;">
                <td colspan="7"><b><u>Transport</u></b></td>
            </tr>
            <?php if ($transport_section==1){ ?>
                        <tr>
                        <?php if (in_array('name', $transport_fields)){ ?>
                            <th>Samochód</th>
                            <?php } ?>
                            <th>Liczba</th>
                        <?php if (in_array('km', $transport_fields)){ ?>
                            <th>Km</th>
                            <?php } ?>
                        <?php if (in_array('price', $transport_fields)){ ?>
                            <th>Cena</th>
                            <?php } ?>
                            <th colspan="2"></th>
                        <?php if (in_array('total_price', $transport_fields)){ ?>
                            <th>Razem netto</th>
                            <?php } ?>
                        </tr>
                        <tr>
                        <?php if (in_array('name', $transport_fields)){ ?>
                                            <td>Samochód dostawczy 10m3</td><?php } ?>
                                            <td>1</td>
                        <?php if (in_array('km', $transport_fields)){ ?>
                                            <td>300</td>
                        <?php } ?>
                        <?php if (in_array('price', $transport_fields)){ ?>
                                            <td>1.80</td>
                        <?php } ?>
                                            <td colspan="2"></td>
                        <?php if (in_array('total_price', $transport_fields)){ ?>
                                            <td>PLN 540.00</td>
                        <?php } ?>
                                        </tr>
            <?php } ?>
            <tr class="warning">
                <td colspan="6"><b><u>Łącznie Transport</u></b></td>
                <td>PLN 580.00</td>
            </tr> 
            <tr>
                <td colspan="7" style="background-color:#93c47d;"><b><u>Obsługa techniczna</u></b></td>
            </tr>
            <?php if ($crew_section==1){ ?>
                        <tr>
                        <?php if (in_array('name', $crew_fields)){ ?>
                            <th>Nazwa</th>
                            <?php } ?>
                            <?php if (in_array('price', $crew_fields)){ ?>
                            <th>Cena</th>
                            <?php } ?>
                            <th style="text-align: center;">Liczba</th>
                            <?php if (in_array('days', $crew_fields)){ ?>
                            <th style="text-align: center;">Okres</th>
                            <?php } ?>
                        <td colspan="2"></td>
                        <?php if (in_array('total_price', $crew_fields)){ ?>
                            <th>Razem netto</th>
                        <?php } ?>
                            

                
            <tr><td colspan="7" style="text-align:center; font-weight:bold; background-color:#eee;">Montaż</td></tr>                                <tr>
                                    <?php if (in_array('name', $crew_fields)){ ?>
                                    <td>Technik</td>
                                    <?php } ?>
                            <?php if (in_array('price', $crew_fields)){ ?>
                                                    <td>PLN 400.00</td>
                                <?php } ?>
                                <td style="text-align: center;">3</td>
                                <?php if (in_array('days', $crew_fields)){ ?>
                                    <td style="text-align: center;">1</td>
                                <?php } ?>
                                <td colspan="2"></td>
                                <?php if (in_array('total_price', $crew_fields)){ ?>
                                    <td>PLN 1,200.00</td>
                                <?php } ?>
                                    
                </tr>
                
            <tr><td colspan="7" style="text-align:center; font-weight:bold; background-color:#eee;">Event</td></tr>                                                                        
                                    <tr>
                                    <?php if (in_array('name', $crew_fields)){ ?>
                                    <td>Technik</td>
                                    <?php } ?>
                            <?php if (in_array('price', $crew_fields)){ ?>
                                                    <td>PLN 400.00</td>
                                <?php } ?>
                                <td style="text-align: center;">3</td>
                                <?php if (in_array('days', $crew_fields)){ ?>
                                    <td style="text-align: center;">1</td>
                                <?php } ?>
                                <td colspan="2"></td>
                                <?php if (in_array('total_price', $crew_fields)){ ?>
                                    <td>PLN 1,200.00</td>
                                <?php } ?>
                                    
                </tr>
                
                                            <tr>
                                    <?php if (in_array('name', $crew_fields)){ ?>
                                    <td>Realizator</td>
                                    <?php } ?>
                            <?php if (in_array('price', $crew_fields)){ ?>
                                                    <td>PLN 400.00</td>
                                <?php } ?>
                                <td style="text-align: center;">3</td>
                                <?php if (in_array('days', $crew_fields)){ ?>
                                    <td style="text-align: center;">1</td>
                                <?php } ?>
                                <td colspan="2"></td>
                                <?php if (in_array('total_price', $crew_fields)){ ?>
                                    <td>PLN 1,200.00</td>
                                <?php } ?>
                                    
                </tr>
                <?php } ?>
                <tr class="warning">
                <td colspan="6"><b><u>Łącznie Obsługa techniczna</u></b></td>
                <td>PLN 3,600.00</td>
            </tr>
            </tbody>
            </table>   

</div>
    <div class="footer-pdf">
        <hr>

        <?php if (in_array('address', $footer_fields)){ ?>
        <div class="b2_5 fl">
            <b><?= isset($settings['companyName']) ? $settings['companyName']->value : ''?></b><br>
            <?= isset($settings['companyAddress']) ? $settings['companyAddress']->value : ''?><br>
            <?= Yii::t('app', 'NIP') ?>: <?= isset($settings['companyNIP']) ? $settings['companyNIP']->value : ''?>
        </div>
        <?php } ?>
        <?php if (in_array('email', $footer_fields)){ ?>
        <div class="b2_5 fl">
            <b> <?= Yii::t('app', 'Dział handlowy') ?>:</b><br>
                <?= Yii::t('app', 'tel') ?>: <?= isset($settings['salesDepartmentPhone']) ? $settings['salesDepartmentPhone']->value : '' ?><br>
                <?= Yii::t('app', 'e-mail') ?>: <?= isset($settings['salesDepartmentEmail']) ? $settings['salesDepartmentEmail']->value : '' ?><br>
        </div>
        <?php } ?>
        <?php if (in_array('bank', $footer_fields)){ ?>
        <div class="b2_5 fl">
            <b><?= Yii::t('app', 'Konto bankowe') ?>:</b><br>
            <?= isset($settings['companyBankName']) ? $settings['companyBankName']->value : '' ?><br>
            <?= isset($settings['companyBankNumber']) ? $settings['companyBankNumber']->value : '' ?>
        </div>
        <?php } ?>
        <?php if (in_array('manager', $footer_fields)){ ?>
        <div class="b2_5 fl">
            <b><?= Yii::t('app', 'Kierownik projektu') ?>:</b><br>
            Jan Kowalski<br>
            kowalski@newsystems.pl<br>
            <?= Yii::t('app', 'tel') ?>:+48 999 000 888
        </div>
        <?php } ?>
    </div>
</div>
</div>
</div>

<?php $this->registerCss('
body {
    font-family: Verdana;
    font-size: 12px;
}
.fl {
    float: left;
}
.fr {
    float: right;
}
.hb {
    width: 50%;
}
.b3 {
    width: 30%;
}
.b4 {
    width: 40%;
}
.b2_5 {
    width: 25%;
}
.cb {
    clear: both;
}
.text-left {
    text-align: left;
}
.text-right {
    text-align: right;
}
.hdib {
    display: inline-block;
    width: 49%;
    vertical-align: top;
}
.header {
    padding-bottom: 15px;
    display: inline-block;
        width:100%;
}
.table {
    width: 100%;
    margin-bottom: 15px;
    border-collapse: inherit;
    border-spacing: 0;
    -webkit-border-horizontal-spacing: 0;
    -webkit-border-vertical-spacing: 0;
}
.table td {
    border-collapse: inherit;
    border-spacing: 0;
    -webkit-border-horizontal-spacing: 0;
    -webkit-border-vertical-spacing: 0;
}
.table-row-border tr td, .table-row-border tr th {
    border-bottom: 1px solid #333;
}
.table th {
    text-align: left;
}
table.half_cell td {
    width: 50%;
}
.upf {
    text-transform: uppercase;
}
.client_info, .main_info{
    padding-bottom: 30px;
        display: inline-block;
        width:100%;
}
.client_info h3 {
    font-size: 18px;

}
.client_info p {
    margin: 0;
}
h1 {
    font-size: 24px;
}
.main_info .name_box p {
    margin: 0;
}
.pdf_box .offertable tr th {
    border-bottom:2px solid #333;
    font-weight: bold;
    font-size: 13px;
}
.footer-pdf {
    text-align: left;
    font-weight: normal;
    font-style: normal;
    padding-bottom: 15px;
    display: inline-block;
        width:100%;
}

.half_page {
    width: 50%;
    float: left;
    padding-top: 50px;
}

.whole_page {
    margin-top: 50px;
    width: 100%;
}

.smaller {
    font-size: 11px;
}

.width-1-5 {
    float: left;
    width: 18%;
    padding-left: 10px;

}

.width-1-2 {
    float: left;
    width: 45%;
    padding-left: 10px;

}
.width-1-3 {
    float: left;
    width: 33%;
    padding-left: 10px;

}

.width-1-10 {
    float: left;
    width: 10%;
    padding-left: 10px;

}

.right-border {
    border-right: 0.5px solid black;
}
.bottom-border {
    border-bottom: 0.5px solid black;
}

.padding-top {
    padding-top: 5px;
}

hr {
  height: 2px;
}

'); ?>


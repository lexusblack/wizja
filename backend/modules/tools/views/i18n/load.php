<?php
use yii\bootstrap\Html;
use kartik\helpers\Enum;
use backend\modules\tools\models\I18n;
use common\helpers\Url;
\common\assets\JqueryQueryObjectAsset::register($this);
/* @var $this \yii\web\View */
?>
<div class="alert alert-warning">
    <?php echo Enum::array2table($msg,true); ?>
</div>
<div>
    <h1><?= Yii::t('app', 'Język') ?>: <?php echo Html::dropDownList('lang',$lang, I18n::getLanguageList(), ['id'=>'language-select']); ?></h1>
    <div class="text-muted">
        <ol>
            <li><?= Yii::t('app', 'Wybrać odpowiedni język z listy (powyżej)') ?></li>
            <li><?= Yii::t('app', 'Po załadowniu nacisnąć \'Kopiuj do schowka\'') ?></li>
            <li><?= Yii::t('app', 'w Excelu wybrać odpowiednią zakładkę np. EN.') ?></li>
            <li><?= Yii::t('app', 'zaznaczyć komórkę A1') ?></li>
            <li><?= Yii::t('app', 'CTRL+A') ?></li>
            <li><?= Yii::t('app', 'CTRL+V') ?></li>
        </ol>
    </div>
<?php
echo Html::a('https://docs.google.com/spreadsheets/d/1H9FyCITatTNps2SM8JSCFZIeRRNuIasjjiNg793C2l8', 'https://docs.google.com/spreadsheets/d/1H9FyCITatTNps2SM8JSCFZIeRRNuIasjjiNg793C2l8', ['target'=>'_blank']);
echo Html::tag('br');
echo \supplyhog\ClipboardJs\ClipboardJsWidget::widget([
    'inputId' => "#data-text",
    'label' => Yii::t('app', 'Kopiuj do schowka').': '.$lang,
]);
echo Enum::array2table($data,true,false,true,['id'=>'data-text','class'=>'table table-bordered table-striped table-condensed']);

?>

</div>
<?php
$url = Url::current(['lang'=>null]);
$this->registerJs('
$("#language-select").on("change", function(){
    var url = window.location.href;
    
    var lang = $(this).val();
    var q = $.query.load(url).set("lang", lang);
    var u =  window.location.pathname + q.toString();
    //console.log(u, url);
    window.location = u;
});
');
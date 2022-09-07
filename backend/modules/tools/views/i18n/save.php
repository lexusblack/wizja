<?php
use yii\bootstrap\Html;
use kartik\helpers\Enum;
use backend\modules\tools\models\I18n;
use common\helpers\Url;
use kartik\form\ActiveForm;
\common\assets\JqueryQueryObjectAsset::register($this);
/* @var $this \yii\web\View */
?>
<div>
    <h1><?= Yii::t('app', 'Język') ?>: <?php echo Html::activeDropDownList($model, 'lang', I18n::getLanguageList(), ['id'=>'language-select']); ?></h1>
    <div class="text-muted" style="color: white;">
        <ol>
            <li><?= Yii::t('app', 'Wybrać odpowiedni język z listy (powyżej)') ?></li>
            <li><?= Yii::t('app', 'w Excelu wybrać odpowiednią zakładkę np. EN.') ?></li>
            <li><?= Yii::t('app', 'zaznaczyć komórkę A1') ?></li>
            <li><?= Yii::t('app', 'CTRL+A') ?></li>
            <li><?= Yii::t('app', 'CTRL+C') ?></li>
            <li><?= Yii::t('app', 'wkleić w polu textowym poniżej') ?></li>
            <li><?= Yii::t('app', 'Zapisz') ?></li>
            <li><?= Yii::t('app', 'Przejść do') ?>: <?php echo Html::a('link', ['load'], ['class'=>'btn btn-primary btn-xs']); ?></li>
        </ol>
    </div>
<?php
echo Html::a('https://docs.google.com/spreadsheets/d/1H9FyCITatTNps2SM8JSCFZIeRRNuIasjjiNg793C2l8', 'https://docs.google.com/spreadsheets/d/1H9FyCITatTNps2SM8JSCFZIeRRNuIasjjiNg793C2l8', ['target'=>'_blank']);

$form = ActiveForm::begin([]);
echo $form->field($model, 'text')->textarea();
echo Html::submitButton(Yii::t('app', 'Zapisz'));
ActiveForm::end();

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
    console.log(u, url);
    window.location = u;
});
');
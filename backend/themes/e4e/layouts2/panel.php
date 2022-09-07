<?php
use backend\assets\MainPanelAsset;
use kartik\tabs\TabsX;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use kartik\nav\NavX;

/* @var $this \yii\web\View */
/* @var $content string */
$user = Yii::$app->user;
MainPanelAsset::register($this);
\kartik\icons\Icon::map($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/favicon.png" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Html::img('/admin/themes/e4e/images/header-logo.png', ['class' => 'header-logo']),
        'brandUrl' => Yii::$app->homeUrl,
        'innerContainerOptions'=>['class'=>'container-fluid'],
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = require_once '_menuItems.php';
    echo NavX::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
        'encodeLabels' => false
    ]);
    echo Html::beginTag('div', ['class'=>'navbar-center navbar-text']);
    echo \lajax\languagepicker\widgets\LanguagePicker::widget([
        'skin' => \lajax\languagepicker\widgets\LanguagePicker::SKIN_BUTTON,
        'size' => \lajax\languagepicker\widgets\LanguagePicker::SIZE_SMALL
    ]);
    echo Html::endTag('div');
    NavBar::end();
    ?>

    <div class="container-fluid">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?php
        use kartik\widgets\AlertBlock;
        echo AlertBlock::widget([
            'type' => AlertBlock::TYPE_GROWL,
            'useSessionFlash' => true
        ]);

        $items = [
            [
                'label'=>'Rola ne evencie',
                'url' => ['/user-event-role/index'],
            ],
            [
                'label'=>'Dodatki finansowe',
                'url' => ['/addon-rate/users'],
            ],
            [
                'label'=>'Dane firmy',
                'url' => ['/setting/index'],
            ],
            [
                'label'=>'Personalizacja',
                'url' => ['/setting/personalize'],
            ],
            [
                'label'=>'Oferty',
                'url' => ['/setting/offer'],
            ],
            [
                'label'=>'Powiadomienia',
                'url' => ['/setting/notification'],
            ],
            [
                'label'=>'Uprawnienia',
                'url' => ['/permission/default/manage-roles'],
            ],
            [
                'label'=>'Działy firmy',
                'url' => ['/department/index'],
            ],
            [
                'label'=>'Język',
                'url' => ['/tools/i18n/save'],
            ],
        ];

        $items[$this->params['active_tab']]['active'] = true;

        ?>

        <div style="display: table-row;" >
            <div style="width: 9vw; display: table-cell;">
                <?= TabsX::widget([
                'items'=>$items,
                'position'=>TabsX::POS_LEFT,
                'encodeLabels'=>false,
                'enableStickyTabs'=>true
                ]);
                ?>
            </div>
            <div style=" width: 91vw; display: table-cell; vertical-align: top;">
                <?= $content ?>
            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6">
                        &copy; Softwebo.pl <?= date('Y') ?>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-right">
                            <?= \lajax\languagepicker\widgets\LanguagePicker::widget([
                                'skin' => \lajax\languagepicker\widgets\LanguagePicker::SKIN_BUTTON,
                                'size' => \lajax\languagepicker\widgets\LanguagePicker::SIZE_SMALL
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>


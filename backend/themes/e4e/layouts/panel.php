<?php
/* @var $this \yii\web\View */
/* @var $content string */
use common\assets\AppAsset;
use backend\assets\MainPanelAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use yii\bootstrap\Alert;
use kartik\tabs\TabsX;


AppAsset::register($this);
MainPanelAsset::register($this);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/jcabanillas/yii2-inspinia/assets');
$user = Yii::$app->user;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>

        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="/favicon.png" />
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>

    <body><?php $this->beginBody() ?>

        <div id="wrapper" class="">

            <?= $this->render('sidebar', ['directoryAsset' => $directoryAsset]) ?>

            <div id="page-wrapper" class="gray-bg">
                <div class="row border-bottom">
                    <?= $this->render('header', ['directoryAsset' => $directoryAsset]) ?>
                </div>
                <div class="row wrapper border-bottom newsystem-bg page-heading">
                    <?php if (isset($this->blocks['content-header'])) { ?>
                        <?= $this->blocks['content-header'] ?>
                    <?php } else { ?>
                        <div class="col-sm-<?= isset($this->blocks['content-header-actions']) ? 6 : 12 ?>">
                            

                            <?=
                            Breadcrumbs::widget([
                                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                                'activeItemTemplate' => "<li class=\"active\"><strong>{link}</strong></li>\n"
                            ])
                            ?>
                        </div>
    <?php if (isset($this->blocks['content-header-actions'])): ?>
                            <div class="col-sm-6">
                                <div class="title-action">
        <?= $this->blocks['content-header-actions'] ?>
                                </div>
                            </div>
                        <?php endif ?>
<?php } ?>

                </div>

                <div class="wrapper wrapper-content">

<?//=Alert::widget() ?>

                    <div class="row">
                        <div class="col-lg-12">
       <?php
        $items = [
            [
                'label'=>Yii::t('app', 'Dane firmy'),
                'url' => ['/setting/index'],
                'visible' => $user->can('settingsCompany')
            ],
            [
                'label'=>Yii::t('app', 'Działy firmy'),
                'url' => ['/department/index'],
                'visible' => $user->can('settingsCompanyDepartments')
            ],
            [
                'label'=>Yii::t('app', 'Rola ne evencie'),
                'url' => ['/user-event-role/index'],
                'visible' => $user->can('settingsRole')
            ],
            [
                'label'=>Yii::t('app', 'Oferty'),
                'url' => ['/setting/offer'],
                'visible' => $user->can('settingsOffers')
            ],

            [
                'label'=>Yii::t('app', 'Finanse'),
                'url' => ['/finances/settings/index'],
                'visible' => $user->can('settingsFinances')
            ],

            [
                'label'=>Yii::t('app', 'Dodatki finansowe'),
                'url' => ['/addon-rate/users'],
                'visible' => $user->can('settingsAddons')
            ],
            [
                'label'=>Yii::t('app', 'Powiadomienia'),
                'url' => ['/setting/notification'],
                'visible' => $user->can('settingsNotifications')
            ],
            [
                'label'=>Yii::t('app', 'Uprawnienia'),
                'url' => ['/permission/default/manage-roles2'],
                'visible' => $user->can('settingsAccessControl')
            ],

            [
                'label'=>Yii::t('app', 'Personalizacja'),
                'url' => ['/setting/personalize'],
                'visible' => $user->can('settingsPersonalization')
            ],
            [
                'label'=>Yii::t('app', 'Język'),
                'url' => ['/i18n/default/index'],
                'visible' => $user->can('settingsLanguage')
            ],
            [
                'label'=>Yii::t('app', 'Firmy'),
                'url' => ['/firm/index'],
                'visible' => $user->can('settingsCompany2')
            ],
        ];

        $items[$this->params['active_tab']]['active'] = true;

        ?>

        <div class="row">
            <div class="col-md-12">
            <?php 
                echo TabsX::widget([
                    'items'=>$items,
                    'encodeLabels'=>false,
                    'enableStickyTabs'=>true,
                ]);
            ?>

            </div>
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-content">
                    <?= $content ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <?= $this->render('footer', ['directoryAsset' => $directoryAsset]) ?>
    </div>
 </div>


<?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>

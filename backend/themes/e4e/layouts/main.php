<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use kartik\nav\NavX;

/* @var $this \yii\web\View */
/* @var $content string */
$user = Yii::$app->user;
AppAsset::register($this);
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
<?php
$this->registerCss('

body {font-size: 11px;}

');
?>

<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
             NavBar::begin([
                 'brandLabel' => \kartik\icons\Icon::show('home').'HOME',
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
                  'encodeLabels'=>false,
              ]);
             echo Html::beginTag('div', ['class'=>'navbar-center navbar-text']);
            echo \common\widgets\LanguagePicker::widget();
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
        ?>
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
                        <?php echo \common\widgets\LanguagePicker::widget(); ?>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

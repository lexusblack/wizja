<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <div class="container-fluid">
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
        <p class="pull-left">&copy; Softwebo.pl <?= date('Y') ?></p>
        <p class="pull-right"></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

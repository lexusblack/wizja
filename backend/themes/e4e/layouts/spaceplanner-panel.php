<?php

    /* @var $this \yii\web\View */
    /* @var $content string */

    use common\assets\SpaceplannerAsset;
    use yii\helpers\Html;
    use yii\bootstrap\Nav;
    use yii\bootstrap\NavBar;
    use yii\widgets\Breadcrumbs;
    use common\widgets\Alert;

    SpaceplannerAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>

    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <script type="text/javascript">
    if (navigator.userAgent.indexOf('MSIE') !== -1 ||
      navigator.appVersion.indexOf('Trident/') > 0) {
      alert('Przepraszamy, ta przeglądarka nie jest kompatybilna. Skorzystaj z innej (chrome, mozilla, safari, opera, itp..)');
    }
  </script>
</head>

<body class="gray-bg">
<?php $this->beginBody() ?>
    <?=$content?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<?php
use yii\helpers\Html;

/* @var $this yii\web\View */


?>
<h3><?php echo $title; ?></h3>

<?php echo $content; ?>

<br />
<br />
----
<br />
<?= Yii::t('app', 'Wiadomość wysłana z serwisu') . " " . Html::a(Yii::$app->request->hostInfo, Yii::$app->request->hostInfo); ?>

<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>

Hello <?= Html::encode($user->username) ?>,

Follow the link below to reset your password:

<?= Yii::t('app', 'Wiadomość wysłana z serwisu') . " " . Html::a(Yii::$app->request->hostInfo, Yii::$app->request->hostInfo); ?>

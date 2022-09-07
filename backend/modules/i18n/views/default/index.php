<?php

use yii\bootstrap\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Language */
$user = Yii::$app->user;
?>

<div class="i18n-default-index panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?=  Yii::t('app', 'Wybierz dział') ?></h3>
    </div>
    <div class="panel-body">
        <?php

        if ($user->can('settingsLanguageLanguages')) {
            echo Html::a(Html::icon('flag') . ' ' . Yii::t('app', 'Języki'), ['/i18n/language/index'], ['class' => 'btn btn-default']);
        }
        if ($user->can('settingsLanguageTranslate')) {
            echo Html::a(Html::icon('comment') . ' ' . Yii::t('app', 'Tłumaczenia'), ['/i18n/message/index'], ['class' => 'btn btn-default']);
        }
        if ($user->can('settingsLanguageRefresh')) {
            echo Html::a(Html::icon('refresh') . ' ' . Yii::t('app', 'Załaduj nowe'), ['/i18n/default/refresh'], ['class' => 'btn btn-default']);
        }

        ?>
    </div>
</div>

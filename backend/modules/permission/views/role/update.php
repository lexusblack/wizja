<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @var $model dektrium\rbac\models\Role
 * @var $this  yii\web\View
 */

$this->title = Yii::t('app', 'Aktualizuj rolę');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Grupy użytkowników/Role'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>

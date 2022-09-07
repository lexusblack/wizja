<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Event */

$this->title = Yii::t('app', 'Automatycznie rozwiązane konflikty');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydarzenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-create">
<div class="row">
<div class="ibox">
<div class="ibox-content">
<h1><?=$this->title?></h1>
<p><?=Yii::t('app', 'Usunięcie wydarzenie spowodowało zwolnienie zarezerwowanego na nie sprzętu. Dzięki temu udało się automatycznie rozwiązać konflikty w wydarzeniach:')?></p>
<?php foreach ($resolved as $r){ ?>
<p><?=Yii::t('app', 'Konflikt w wydarzeniu ').$r['event']->name.Yii::t('app', ' na sprzęt ').$r['gear']->name?></p>
<?php } ?>
    

</div>
</div>
</div>
</div>

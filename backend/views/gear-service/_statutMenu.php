<?php

use kartik\helpers\Html;
?>

<ul id="w0" class="nav-pills newsystem-bg nav">
<?php $statuts = common\models\GearServiceStatut::find()->where(['active'=>1])->orderBy(['order'=>SORT_ASC])->all();
if (!isset($params['GearServiceSearch']))
{
	$params['GearServiceSearch'] = [];
}
foreach ($statuts as $status)
{ 
    if ($status->id==$statut){
        $active = 'active';
        $name = $status->name;
    }
    else
        $active = '';
    ?>
<li class="dropdown <?=$active?>"><?=Html::a($status->name." <span class='label' style='background-color:".$status->color."; color:white;'>".$status->getServices($params)."</span>", ['/gear-service/index', 'statut'=>$status->id, 'GearServiceSearch'=>$params['GearServiceSearch']], ['class'=>'category-menu-link']) ?></li>

<?php
}
?>


</ul>
<h1><?=$name?></h1>
<?php
/* @var $this yii\web\View */
/* @var $model common\models\Offer */
/* @var $offerForm \backend\modules\offers\models\OfferForm */

use yii\helpers\Html;

?>

<table style="width:100%; background-color:white;">
<?php foreach ($gears as $gear){ ?>
<tr>
<td style="border-bottom:2px solid black; text-align:center; width:50%;"><?php echo Html::img(\Yii::getAlias('@uploads' . '/gear/').$gear->photo,['height'=>'150']);  ?><br/><strong> <?=$gear->name?></strong></td>
<td style="border-bottom:2px solid black;"><?php if ($gear->no_items){
        $no_item = \common\models\GearItem::find()->where(['active'=>1, 'gear_id'=>$gear->id])->one(); 
       ?>
       <div style="width:126px;"><?=$no_item->generateBarCode()?></div>
       <?php } else{ 
                $items =  \common\models\GearItem::find()->where(['active'=>1, 'gear_id'=>$gear->id])->all(); 
                foreach ($items as $item)
                { ?>
                                <div style="width:126px; margin-right:50px; margin-bottom:20px; float:left;"><?=$item->generateBarCode()?></div>
            <?php    }
        ?>

  <?php  } ?></td>
</tr>
<?php } ?>
</table>



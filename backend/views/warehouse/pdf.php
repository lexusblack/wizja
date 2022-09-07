<?php
/* @var $this yii\web\View */
/* @var $model common\models\Offer */
/* @var $offerForm \backend\modules\offers\models\OfferForm */

use yii\helpers\Html;
$width=100;
if (count($data)<$columns)
    $width = floor(100/$columns)*count($data);
?>
<div style="width:<?=$width?>%">
<table style="width:100%">
<?php 
$i=0;
foreach ($data as $qr)
{ 
if ($i==0){
    ?>
    <tr>
    <?php } ?>
    <td style="text-align:center; padding:30px; border:1px solid black; font-size:40px; color:black; font-weight:bold;">
        <?=$qr[0]?><br/>
        <?=$qr[1]?><br/>
        <?php if($type==1){
            echo $qr[2];
            }else{
            echo $qr[3]; 
                }
        ?>
    </td>
<?php 
$i++;
if ($i==$columns)
{ 
    $i=0;
    ?>
    </tr>
<?php } ?>

    
<?php } ?>
<?php
if ($i>0) {
        echo "</tr>";
            }
?>
<?php if ($i==0){ echo "<tr><td>Brak egzemplarzy</td></tr>";} ?>
</table>
</div>



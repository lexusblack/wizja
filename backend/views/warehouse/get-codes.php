<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Customer; */

?>
<table style="width:100%;">
<?php 
$i = 0;
echo "<tr>";
foreach ($warehouse->getGearDataProvider(false)->getModels() as $gear)
{
    if ($i==4)
    {
        $i=0;
        echo "</tr><tr>";
    }
    if (!$gear->no_items)
    {
        $i=0;
        echo "</tr><tr>";
    }
    $i++;
    ?>
    <td style="width:25%; text-align:center; padding:30px;">
    <h2 style="margin:0px;"><?= $gear->name ?></h2>
    <?php if (($gear->getPhotoUrl())&&($photos)) { ?>
                                <img alt="image" class="img-responsive" style="width:150px; margin:20px;" src="<?php echo $gear->getPhotoUrl(); ?>"/>
                        <?php } ?>
    <?php if ($gear->no_items){ 
                                    if (isset($gear->gearItems[0])){
                                    $no_item = $gear->gearItems[0];
                                    ?>
                                    <?php if ($type==1){ ?>
                                <div style="width:130px; margin:auto;"><?=$no_item->generateBarCode()?></div>
                                    <?php }else{ 
                                    echo '<div style="width:130px; margin:auto;" class="qrcode">'.$no_item->generateQrCodeAsLink().'</div>';
                                     } ?>
                                <?php } }else{ ?>
                                 <?php   } ?>
    </td>
    <?php
    if (!$gear->no_items)
    {
        foreach ($gear->gearItems as $item)
        {
            if ($i==4)
            {
                $i=0;
                echo "</tr><tr>";
            }
            $i++; ?>
            <td style="width:25%; text-align:center; padding:30px;">
            <h2 style="margin:0px;"><?= $gear->name ?> [<?=$item->number?>]</h2>
            
            <?php if ($type==1){ ?>
                                <div style="width:130px; margin:auto;"><?=$item->generateBarCode()?></div>
                                    <?php }else{ 
                                    echo '<div style="width:130px; margin:auto;" class="qrcode">'.$item->generateQrCodeAsLink().'</div>';
                                     } ?>
            </td>
     <?php   }
     $i=0;
                echo "</tr><tr>";
    }
}
echo "</tr>"
?>
</table>

<?php
$this->registerCss('
.qrcode img {width:100%;}

    ');
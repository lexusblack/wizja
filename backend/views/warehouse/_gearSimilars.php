<?php
use common\models\GearService;
use common\models\GearItem;
use common\models\Event;
use yii\bootstrap\Html;
use yii\helpers\Url;
?>
    <div class="row">
        <div class="col-md-12">
        <p><?=Yii::t('app', 'Sprzęt, który chcesz zarezerwować, nie ma w tym czasie wolnych egzemplarzy. Wybierz sposób w jaki chcesz rozwiązać ten problem')?></p>
        <?php if ($similars){ ?>
        <div class="ibox">
        <div class="ibox-title navy-bg">
        <h4><?=Yii::t('app', 'Wybierz alternatywę')?></h4>
        </div>
        <div class="ibox-content">
        <form id="similarForm">
        <table class="table">
        <tr><th><?= Yii::t('app', 'Nazwa') ?></th><th style="text-align:center;"><?= Yii::t('app', 'Dostępnych') ?></th><th><?= Yii::t('app', 'Rezerwacja') ?></th></tr>
        <tr><td><?=$gear->name?></td>
        <td style="text-align:center;">
                    <?php
                    if ($gear->no_items)
                    {
                        $serwisNumber = $gear->getNoItemSerwis();
                        $number = $gear->getAvailabe($start, $end)-$serwisNumber+$oldQuantity;
                        echo $number;
                    }
                    else
                    {
                        $serwisNumber = 0;
                        foreach ($gear->gearItems as $item) {
                            if ($item->active == 1 && $item->status === GearItem::STATUS_SERVICE) {
                                $serwisNumber++;
                            }
                        }
                        $number = $gear->getAvailabe($start, $end)-$serwisNumber+$oldQuantity;
                        echo $number;
                    } 
                    if ($number<0){ $number = 0;}
                    ?>   
        </td>
        <td><input name="item-<?=$gear->id?>" type="number" value=0 min="0" max="<?=$number?>"/><input name="gear_id" type="hidden" value=<?=$gear->id?> /></td></tr>
        <?php foreach ($similars as $similar){ ?>
        <tr><td><?=$similar->similar->name?></td>
        <td style="text-align:center;">
                    <?php
                    if ($similar->similar->no_items)
                    {
                        $serwisNumber = $similar->similar->getNoItemSerwis();
                        $number = $similar->similar->getAvailabe($start, $end)-$serwisNumber;
                    }
                    else
                    {
                        $serwisNumber = 0;
                        foreach ($similar->similar->gearItems as $item) {
                            if ($item->active == 1 && $item->status === GearItem::STATUS_SERVICE) {
                                $serwisNumber++;
                            }
                        }
                        $number = $similar->similar->getAvailabe($start, $end)-$serwisNumber;
                    } 
                    echo $number;
                    ?>   
        </td>
        <td><input name="item-<?=$similar->similar->id?>" type="number" value=0 min="0" max="<?=$number?>"/></td></tr>
        <?php } ?>
        </table>
        </form>
        <?= Html::a(Yii::t('app', 'Zarezerwuj'), '#', ['class' => 'btn btn-primary', 'onclick'=>'bookSimilars('.$packlist.',\''.$start.'\', \''.$end.'\'); return false;']) ?>
        </div>
        </div>
        <?php } ?>
        <div class="ibox">
        <div class="ibox-title blue-bg">
        <h4><?=Yii::t('app', 'Zarezerwuj częściowo i stwórz konflikt')?></h4>
        </div>
        <div class="ibox-content">
        <form id="conflictForm">
        <table class="table">
        <tr><th><?= Yii::t('app', 'Nazwa') ?></th><th style="text-align:center;"><?= Yii::t('app', 'Zapotrzebowanie') ?></th><th style="text-align:center;"><?= Yii::t('app', 'Dostępnych') ?></th><th><?= Yii::t('app', 'Rezerwacja') ?></th></tr>
        <tr><td><?=$gear->name?></td>
        <td style="text-align:center;"><?=$quantity?></td>
        <td style="text-align:center;">
                    <?php
                    if ($gear->type!=1)
                    {
                        $number = $gear->quantity+$oldQuantity;
                        echo $number;
                    }else{
                    if ($gear->no_items)
                    {
                        $serwisNumber = $gear->getNoItemSerwis();
                        $number = $gear->getAvailabe($start, $end)-$serwisNumber+$oldQuantity;
                        echo $number;
                    }
                    else
                    {
                        $serwisNumber = 0;
                        foreach ($gear->gearItems as $item) {
                            if ($item->active == 1 && $item->status === GearItem::STATUS_SERVICE) {
                                $serwisNumber++;
                            }
                        }
                        $number = $gear->getAvailabe($start, $end)-$serwisNumber+$oldQuantity;
                        echo $number;
                    }                         
                    }
                    if ($number<0){ $number = 0;}

                    ?>   
        </td>
        <td><input name="quantity" type="number" value=<?=$number?> min="0" max="<?=$number?>"/><input name="gear_id" type="hidden" value=<?=$gear->id?> /><input name="full" type="hidden" value=<?=$quantity?> /></td></tr>
        </table>
        </form>
        <?= Html::a(Yii::t('app', 'Zarezerwuj i stwórz konflikt'), '#', ['class' => 'btn btn-success', 'onclick'=>'bookConflicts('.$packlist.',\''.$start.'\', \''.$end.'\'); return false;']) ?>
        </div>
        </div>
        </div>
    </div>

    <?php
    $this->registerJs('
        $(":input[type=number]").on("change", function(){
            val = $(this).val();
            if (isNaN(val))
            {
                $(this).val(0);
            }else{
                max = $(this).attr("max");
                if (parseInt(val)>parseInt(max))
                {
                    $(this).val(max);
                }
                if (val<0)
                {
                    $(this).val(0);
                }
            }

        });
        ');
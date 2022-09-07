<?php
/* @var $this \yii\web\View */
/* @var $model \common\models\Event */
$formatter = Yii::$app->formatter;
?>

<table class="table table-condensed table-stripped">
    <?php $sum =0; ?>
    <tr>
        <th><?php echo Yii::t('app', 'Użytkownik'); ?></th>
        <th><?php echo Yii::t('app', 'Stawka'); ?></th>
        <th><?php echo Yii::t('app', 'Wynagrodzenie'); ?></th>
        <th><?php echo Yii::t('app', 'Działy'); ?></th>
        <th><?php echo Yii::t('app', 'Koszty dodatkowe'); ?></th>
        <th><?php echo Yii::t('app', 'Diety'); ?></th>
        <th><?php echo Yii::t('app', 'Dodatki za role'); ?></th>
        <th><?php echo Yii::t('app', 'Suma'); ?></th>
    </tr>
    <?php
    foreach ($model->getWorkingTimeSummaryAll() as $item):
        $sum += $item['rate'];
        ?>
        <tr>
            <td><?php echo $item['user']; ?></td>
            <td><?php echo $formatter->asCurrency($item['rate']); ?></td>
            <td><?php echo $formatter->asCurrency($item['salary']); ?></td>
            <td><?php echo implode(', ', $item['departments']); ?></td>
            <td><?php echo $formatter->asCurrency($item['addons']); ?></td>
            <td><?php echo $formatter->asCurrency($item['allowances']); ?></td>
            <td><?php echo $formatter->asCurrency( $item['roleAddons']); ?></td>
            <td><?php echo $formatter->asCurrency($item['sum']); ?></td>
        </tr>
    <?php endforeach; ?>
    <tfoot>
    <?php $sums = $model->getWorkingTimeSummaryAllSums(); ?>
    <tr>
        <th><?php echo $sums['user']; ?></th>
        <th><?php echo $formatter->asCurrency($sums['rate']); ?></th>
        <th><?php echo $formatter->asCurrency($sums['salary']); ?></th>
        <th><?php echo $sums['departments']; ?></th>
        <th><?php echo $formatter->asCurrency($sums['addons']); ?></th>
        <th><?php echo $formatter->asCurrency($sums['allowances']); ?></th>
        <th><?php echo $formatter->asCurrency( $sums['roleAddons']); ?></th>
        <th><?php echo $formatter->asCurrency($sums['sum']); ?></th>
    </tr>
    </tfoot>
</table>
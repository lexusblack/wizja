<?php

/** @var \common\models\RfidReadings $lastRfidCode */

?>

<table class="table table-stripped">
    <thead>
        <tr>
            <th><?=  Yii::t('app', 'Kod RFID') ?></th>
            <th><?=  Yii::t('app', 'Data pierwszej detekcji') ?></th>
            <th><?=  Yii::t('app', 'Data ostatniej detekcji') ?></th>
            <th><?=  Yii::t('app', 'Data zapisu') ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= $lastRfidCode->tag_code ?></td>
            <td><?= $lastRfidCode->first_detection_date ?></td>
            <td><?= $lastRfidCode->last_detection_date ?></td>
            <td><?= $lastRfidCode->save_date ?></td>
        </tr>
    </tbody>
</table>

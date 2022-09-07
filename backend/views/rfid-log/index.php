<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\RfidLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
date_default_timezone_set(Yii::$app->params['timeZone']);
$this->title = 'NEIS Log';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rfid-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="ibox">
    <div class="ibox-content">
    <table class="table" id="rfidLogs">
    <thead>
        <tr><th><?=Yii::t('app', 'Data')?></th><th><?=Yii::t('app', 'Czytnik')?></th><th><?=Yii::t('app', 'Kod NEIS')?></th></tr>
    </thead>
    <tbody></tbody>
    </table>
    </div>
    </div>
</div>

<?php
$this->registerJs("
    loadNeis('".date("y-m-d H:i:s")."', 0);
    ")
?>

<script type="text/javascript">
    


    function loadNeis(date, id)
    {
        $.ajax({
                                                    type: 'POST',
                                                    url: '/admin/rfid-log/load-new?datetime='+date+"&id="+id,
                                                    success: function(response) {
                                                        for (i=0; i<response.logs.length; i++)
                                                        {
                                                            log = response.logs[i];
                                                            $("#rfidLogs tbody").prepend("<tr><td>"+log.datetime+"</td><td>"+log.reader+"</td><td>"+log.tag+"</td></tr>");
                                                            id = log.id;
                                                            
                                                        }
                                                        sleep(2000).then(() => {
                                                            loadNeis(response.datetime, id);
                                                        });
                                                        
                                                    }
                                                });
    }

    const sleep = (milliseconds) => {
        return new Promise(resolve => setTimeout(resolve, milliseconds))
    }
</script>

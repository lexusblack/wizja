<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
use yii\bootstrap\Modal;
use yii\helpers\Url;

?>
<div id="dd">
</div>
<script type="text/javascript">
    function repair()
    {
        $.post( "<?=Url::to(['/event/repair-events'])?>", { })
          .done(function( data ) {
            if (data.success){
                $("#dd").append("R");
                repair();
            }else{
                $("#dd").append("DONE");
            }
          });
    }
    function repair2()
    {
        $.post( "<?=Url::to(['/offer/default/update-offers'])?>", { })
          .done(function( data ) {
            if (data.success){
                $("#dd").append("R");
                repair2();
            }else{
                $("#dd").append("DONE");
            }
          });
    }
</script>

<?php
$this->registerJs('

repair();
repair2();


');

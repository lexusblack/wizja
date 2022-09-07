<?php
use kartik\helpers\Enum;
?>

<div class="alert alert-warning">
    <?php echo Enum::array2table($msg,true); ?>
</div>
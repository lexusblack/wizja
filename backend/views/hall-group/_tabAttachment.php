<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">

<div class="row">
    <div class="col-md-12">
    <h3><?php echo Yii::t('app', 'Załączniki'); ?></h3>
        <div class="ibox">
        <div class="row">
        <div class="col-md-3">
        <?php
        $user = Yii::$app->user;
            echo Html::a(Yii::t('app', 'Dodaj'), ['hall-group-photo/create', 'hall_group_id' => $model->id], ['class' => 'btn btn-success btn-xs'])." ";
            echo Html::a(Yii::t('app', 'Pobierz'), ['hall-group-photo/download-all'], ['class' => 'btn btn-success btn-xs download-files-button'])." ";
            echo Html::a(Yii::t('app', 'Wyślij'), ['hall-group-photo/send'], ['class' => 'btn btn-success btn-xs send-files-button']);
        ?>
        </div>
        <div class="col-md-7">
        <div class="file-manager">
                                <h5><?=Yii::t('app', 'Foldery')?>: 
                                <a href="#" class="file-control active" data-type="1"  style="color:#1ab394"><i class="fa fa-folder"></i> <?=Yii::t('app', 'Wszystkie')?></a>
                                <?php foreach (\common\models\HallGroupPhotoType::find()->where(['active'=>1])->asArray()->all() as $typ){ ?> 
                                    <a href="#" class="file-control" data-type="<?=$typ['id']?>"   style="color:#1ab394"><i class="fa fa-folder"></i> <?=$typ['name']?></a>
                                    <?php } ?></h5>
        </div>
        </div>
        <div class="col-md-2">
        <a href="#" class="btn btm-xs btn-default show-list"><i class="fa fa-th-list"></i></a> <a href="#" class="btn btm-xs btn-default show-icons"><i class="fa fa-th-large"></i></a>
        </div>
        </div>
    </div>
</div>
</div>
<div class="row file-icons" style="margin-top:20px;">
<div class="col-lg-12">
<?php foreach ($model->hallGroupPhotos as $file){ ?>
                            <div class="file-box folder-<?=$file->type?>" >
                                <div class="file">
                                    <a href="<?=$file->getFileUrl()?>">
                                        <span class="corner"></span>
<?php if(($file->extension=='jpg')||($file->extension=='png')||($file->extension=="gif"))
        {
            ?>
            <div class="image" style="background-size:cover; background-image:url(<?=$file->getFileUrl()?>)">
            </div>
        <?php }else{ 
            switch ($file->extension) {
            case "psd": ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_psd.jpg)">
            </div>
            <?php
                break;
            case "tif":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_tif.jpg)">
            </div>
            <?php
                break;
            case "cdr":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_cdr.jpg)">
            </div>
            <?php
                break;
            case "fla":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_fla.jpg)">
            </div>
            <?php
                break;
            case "dwg":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_owg.jpg)">
            </div>
            <?php
                break;
            case "raw":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_raw.jpg)">
            </div>
            <?php
                break;
            case "svg":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_svg.jpg)">
            </div>
            <?php
                break;
            case "swf":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_swf.jpg)">
            </div>
            <?php
                break;
            case "wmf":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_wmf.jpg)">
            </div>
            <?php
                break;
            case "ai":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_ai.jpg)">
            </div>
            <?php
                break;
            case "bmp":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_bmp.jpg)">
            </div>
            <?php
                break;
            case "3ds":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_3ds.jpg)">
            </div>
            <?php
                break;
            case "mp4":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_mp4.jpg)">
            </div>
            <?php
                break;
            case "eps":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_eps.jpg)">
            </div>
            <?php
                break;
            case "pdf":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_pdf.jpg)">
            </div>
            <?php
                break;
            case "doc":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_doc.jpg)">
            </div>
            <?php
             break;
            case "docx":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_doc.jpg)">
            </div>
            <?php
                break;
            case "zip":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_zip.jpg)">
            </div>
            <?php
                break;
            case "xml":
            ?>
                <div class="image" style="background-size:cover; background-image:url(/img/icon_xml.jpg)">
            </div>
            <?php
                break;
            default: ?>
                <div class="icon">
                <i class="fa fa-file"></i>
            </div>
           <?php break;
        }

            ?>
            
        <?php } ?>
                                        
                                        <div class="file-name">
                                            <?=substr($file->filename, 0, 25)?>
                                            <br>
                                            <small><?=$file->create_time?></small>
                                            <br>
                                            <small style="color:#1ab394"><?=$file->getTypeName()?><input type="checkbox" name="file-<?=$file->id?>" class="pull-right file-checkbox" data-id="<?=$file->id?>"></small>
                                        </div>
                                    </a>
                                </div>
        <?php
            echo Html::a("<i class='fa fa-pencil'></i>", ['hall-group-photo/update', 'id' => $file->id], ['class' => "badge badge-primary pull-right confirm-bagde"]);
            echo Html::a("<i class='fa fa-trash'></i>", ['hall-group-photo/delete', 'id' => $file->id], ['class' => "badge badge-danger pull-right delete-bagde", 'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],]);
        ?>
                            </div>
<?php } ?>
                           

                        </div>
</div>
<div class="row file-list" style="margin-top:20px; display:none;">
<div class="col-lg-12">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedHallGroupPhotos(),
            'rowOptions' => function ($model, $index, $widget, $grid){
                return ['class' => 'file-list-row folder-'.$model->type];
              },
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'attribute' => 'filename',
                    'format' => 'raw',
                    'value'=>function($model) use ($user)
                    {
                        if ($user->can('gearAttachmentsView')) {
                            return Html::a($model->filename, ['gear-attachment/show', 'id' => $model->id], ['target' => '_blank']);
                        }
                        return $model->filename;
                    },
                ],
                [
                    'attribute'=>'type',
                    'label'=>Yii::t('app', 'Folder'),
                    'value'=>function($model)
                    {
                        return $model->getTypeName();
                    }
                ],
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'buttons' => [
                        'download' => function ($url, $model) {
                            if (strtolower($model->extension) == 'png'  || strtolower($model->extension) == 'jpg' || strtolower($model->extension) == 'pdf') {
                                return ' '.Html::a(Html::icon('download'), ['gear-attachment/download', 'id'=>$model->id], ['target' => '_blank']);
                            }
                        }
                    ],
                    'template'=>'{delete}{download}',
                    'controllerId'=>'hall-group-photo',
                ]
            ],
        ]);
        ?>
                                </div>
</div>
</div>

<?php $this->registerCss('
.file-box { position: relative; }
.badge.confirm-bagde {right:35px; top:0px;}
.badge.delete-bagde  {position: absolute; right:10px; top:0px;}
.file.checked{ border-color:#1ab394;}

input[type="checkbox"] {
  transform: scale(2);
  -ms-transform: scale(2);
  -webkit-transform: scale(2);
  -o-transform: scale(2);
  -moz-transform: scale(2);
  transform-origin: 0 0;
  -ms-transform-origin: 0 0;
  -webkit-transform-origin: 0 0;
  -o-transform-origin: 0 0;
  -moz-transform-origin: 0 0;
}
    ');


$this->registerJs('
$(".show-list").click(function(e)
{
    e.preventDefault();
    $(".file-list").show();
    $(".file-icons").hide();
});

$(".show-icons").click(function(e)
{
    e.preventDefault();
    $(".file-list").hide();
    $(".file-icons").show();
});

$(".file-control").click(function(e){
    e.preventDefault();
    $(".file-control").removeClass("active");
    $(this).addClass("active");
    $type = $(this).data("type");
    $class = ".folder-"+$type;
    $(".file-box").hide();
    $(".file-list-row").hide();
    if ($type==1){
        $(".file-box").show();
        $(".file-list-row").show();
    }else
        $($class).show();

});

$(".file-checkbox").click(function(){
    if($(this). prop("checked") == true)
    {
        $(this).parent().parent().parent().parent().addClass("checked");
    }else{
        $(this).parent().parent().parent().parent().removeClass("checked");
    }
});

$(".download-files-button").click(function(e){
e.preventDefault();
    var data = [];
    $i = 0;
    
    $(".file-checkbox").each(function(){
        if($(this). prop("checked") == true)
        {
            data[$i] = $(this).data("id");
            $i++;
        }
    })
    
    //alert($(this).attr("href")+"?data="+JSON.stringify(data));
    location.href = $(this).attr("href")+"?data="+JSON.stringify(data);

});
$(".send-files-button").click(function(e){
    e.preventDefault();
    var data = [];
    $i = 0;
    
    $(".file-checkbox").each(function(){
        if($(this). prop("checked") == true)
        {
            data[$i] = $(this).data("id");
            $i++;
        }
    })
    
    //alert($(this).attr("href")+"?data="+JSON.stringify(data));
    location.href = $(this).attr("href")+"?data="+JSON.stringify(data);
});
    ');

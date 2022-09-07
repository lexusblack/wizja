<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Załączniki'); ?></h3>
<div class="row">
    <div class="col-md-12">
            <div class="ibox">
        <?php

        if ($user->can('eventEventEditEyeAttachmentAdd')) {
            echo Html::a(Html::icon('plus') . ' ' . Yii::t('app', 'Dodaj'), ['attachment/create', 'eventId' => $model->id], ['class' => 'btn btn-success']);
        } ?>
        <?php

        if ($user->can('eventEventEditEyeAttachmentDelete')) {
            echo Html::a(Html::icon('trash') . ' ' . Yii::t('app', 'Usuń zaznaczone'), ['attachment/delete-more', 'eventId' => $model->id], ['class' => 'btn btn-danger delete-more-button',
                ]);
        } ?>
        <?php //echo Html::a(Html::icon('picture').' Galeria', ['attachment/gallery', 'eventId'=>$model->id], ['class'=>'btn btn-warning']); ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedAttachements(),
            'id'=>'attachmentTable',
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'columns' => [
                    [
                'class' => 'yii\grid\CheckboxColumn',
                'multiple'=>true, 
                ],
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'attribute' => 'filename',
                    'value'=>function($model)
                    {
                        $name = explode(".", $model->filename);
                        $icon = "";
                            if (($name[1]=='doc')||($name[1]=='docx'))
                            {
                                $icon = "<i class='fa fa-file-word-o'></i>";
                            }
                            if (($name[1]=='xls')||($name[1]=='xlsx'))
                            {
                                $icon = "<i class='fa fa-file-excel-o'></i>";
                            }
                            if (($name[1]=='jpg')||($name[1]=='png'))
                            {
                                $icon = '<a href="'.$model->getFileUrl().'" data-gallery=""><img class="room-photo" src="'.$model->getFileUrl().'" alt=""></a>';
                            }
                            if (($name[1]=='ppt')||($name[1]=='pptx'))
                            {
                                $icon = "<i class='fa fa-file-powerpoint-o'></i>";
                            }
                            if ($name[1]=='pdf')
                            {
                                $icon = "<i class='fa fa-file-pdf-o'></i>";
                            }
                        return $icon." ".Html::a($model->filename, $model->getFileUrl(), ['target'=>'_blank']);
                    },
                    'format' => 'raw',
                ],
                'typeLabel:text:'.Yii::t('app', 'Typ'),
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'controllerId'=>'attachment',

                    'template'=>'{show} {download} {update} {delete}',
                    'buttons'=>[
                        'show'=>function($url, $model, $key)
                        {
                            $options =  [];
                            $route = $url;
                            if ($model->type == \common\models\LocationAttachment::TYPE_FILE)
                            {
                                $options['target'] = '_blank';
                                $route = $model->getFileUrl();
                            }

                            return Html::a(Html::icon('eye-open'), $route, $options);
                        },
                        'download'=>function($url, $model, $key)
                        {
                            if ($model->type == \common\models\LocationAttachment::TYPE_PANORAMA)
                            {
                                return false;
                            }
                            return Html::a(Html::icon('save-file'), $url);
                        },
                        'delete' => function ($url, $model, $key)
                        {
                            return Html::a(Html::icon('trash'), $url, [
                                'data' => [
                                    'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                                    'method' => 'post',
                                ],
                            ]);
                        },
                    ],
                    'visibleButtons' => [
                        'show' => $user->can('eventEventEditEyeAttachmentDownload'),
                        'download' => $user->can('eventEventEditEyeAttachmentDownload'),
                        'update' => $user->can('eventEventEditEyeAttachmentEdit'),
                        'delete' => $user->can('eventEventEditEyeAttachmentDelete'),
                    ]

                ],
            ],
        ]);
        ?>
    </div>
</div>
    </div>
</div>
</div>
                            <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
                                <div class="slides"></div>
                                <h3 class="title"></h3>
                                <a class="prev">‹</a>
                                <a class="next">›</a>
                                <a class="close">×</a>
                                <a class="play-pause"></a>
                                <ol class="indicator"></ol>
                            </div>

<?php $this->registerJs('
    $(".delete-more-button").click(function(e){
        e.preventDefault();
        var data = $("#attachmentTable").yiiGridView("getSelectedRows");
        if (data.length>0)
        {
                $(this).attr("disabled", true);
                $.ajax({
                    url: $(this).attr("href"),
                    type: "post",
                    async: false,
                    data: {items:data},
                    success: function(data) {
                        location.reload();
                    },
                    error: function(data) {
                            
                    }
                }); 
        }else{
            alert("'.Yii::t('app', 'Zaznacz pozycje!').'")
        }
        
    })

    ');
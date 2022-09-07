<?php

use common\components\grid\GridView;
$user = Yii::$app->user;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AttachmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Załączniki');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attachment-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
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
                    'attribute'=>'event_name',
                    'format'=>'html',
                    'label'=>Yii::t('app', 'Wydarzenie'),
                    'value'=> function($model){
                        $content = Html::a($model->event->name.' ['.$model->event->code.']', ['view', 'id' => $model->event_id]);
                        return $content;
                    }
                ],
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
    ]); ?>
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
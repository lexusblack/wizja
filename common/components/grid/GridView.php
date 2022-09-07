<?php
namespace common\components\grid;

use Yii;

class GridView extends \kartik\grid\GridView
{
    public function init()
    {
        $this->panel = array_merge([
            'heading' => false,
//            'footer' => false,
        ], $this->panel);
        $this->exportConfig = [
            GridView::EXCEL=>[
                'filename'=>'export',
                'config' => [
                    'worksheet'=> Yii::t('app','Dane'),
                ]
            ],
            GridView::CSV => [],
            GridView::HTML => [],
            GridView::PDF => [],
            GridView::TEXT=>[],
        ];
        $this->export = [
            'target'=>GridView::TARGET_SELF,
            'showConfirmAlert'=> false,
        ];
        return parent::init();
    }
}
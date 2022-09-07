<?php
namespace common\widgets;

use yii\redactor\widgets\Redactor;

class RedactorField extends Redactor
{
    public function init()
    {
       /* $this->clientOptions = [
            'buttons' => ['html','formatting', 'bold', 'italic', 'deleted',
                'unorderedlist', 'orderedlist','outdent', 'indent', 'alignment', 'link', 'horizontalrule'],
    //                    'plugins' => ['clips', 'fontcolor','imagemanager']
        ];*/
        parent::init();
    }
}
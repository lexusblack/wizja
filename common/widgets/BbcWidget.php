<?php
namespace common\widgets;

use common\helpers\ArrayHelper;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\VarDumper;
use Zend\Feed\Reader\Extension\Content\Entry;
use Zend\Feed\Reader\Feed\Rss;
use Zend\Feed\Reader\Reader;

class BbcWidget extends Widget
{
    public $data = [];

    public function init()
    {
        parent::init();
        $feed = Reader::import('//feeds.bbci.co.uk/news/rss.xml?edition=int');
        $data = [];
        foreach ($feed as $entry)
        {
            /* @var $entry Entry */
            $edata = array(
                'title'        => $entry->getTitle(),
                'description'  => $entry->getDescription(),
                'dateModified' => $entry->getDateModified(),
                'authors'      => $entry->getAuthors(),
                'link'         => $entry->getLink(),
                'content'      => $entry->getContent(),
//                'thumbnail' => $entry->getXpath()->query($entry->getXpathPrefix().'/media:thumbnail')->item(0)->getAttribute('url'),

            );
            if ($entry->getXpath()->query($entry->getXpathPrefix().'/media:thumbnail')->item(0) !== null)
            {
                $tUrl = $entry->getXpath()->query($entry->getXpathPrefix().'/media:thumbnail')->item(0)->getAttribute('url');
                $tUrl = preg_replace('@^http\:@', '', $tUrl);
                $edata['thumbnail'] = $tUrl;
            }
            $data['entries'][] = $edata;
        }
        $this->data = $data;

    }

   public function run()
   {
       parent::run();
       echo Html::beginTag('div', ['class' => 'bbc_news']);
       echo Html::beginTag('dl');
       foreach ($this->data['entries'] as $index => $data)
       {
           if ($index>2) break;
           echo Html::tag('dt', Html::a(Html::img($data['thumbnail'], ['width'=>'100%']).'<br /><span>'.$data['title'].'</span>', $data['link']));
           echo Html::tag('dd', $data['content']);
       }
       echo Html::endTag('dl');
       echo Html::endTag('div');
   }
}
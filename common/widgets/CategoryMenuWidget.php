<?php
namespace common\widgets;

use common\models\Gear;
use common\models\GearCategory;
use yii\base\Widget;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use Yii;
use yii\helpers\Url;

class CategoryMenuWidget extends Widget
{
    public $route = null;

    protected $_currentMainId = false;
    protected $_currentSubId = false;

    public $btnOptions = ['class' => 'category-menu-link'];

    public function init()
    {
        parent::init();
        if ($this->route === null)
        {
            $this->route =Yii::$app->controller->route;
        }
        $this->_currentMainId =  Yii::$app->request->get('c', false);
        $this->_currentSubId =  Yii::$app->request->get('s', false);
    }

    public function run()
    {

        $request = Yii::$app->request;
        if (Yii::$app->user->identity->gear_category_id)
        {
            $mainCategories = GearCategory::find()->where(['id'=>Yii::$app->user->identity->gear_category_id])->all();
        }else{
            $mainCategories = GearCategory::getMainList(true);
        }
        

        if ($this->_currentMainId=== false)
        {
            Yii::$app->controller->redirect(Url::current(['c'=>$mainCategories[0]->id, 's'=>null, 'page'=>null]));
        }

        $mainItems = [];
        foreach ($mainCategories as $category)
        {
            /* @var $catgory GearCategory */
            $subItems = [];
            $subItems[] = [
                'label'=>$category->name." ".Yii::t('app', 'WSZYSTKIE'),
                'url' => Url::current(['c'=>$category->id, 's'=>null, 'page'=>null, 's2'=>null]),
                'active'=>$request->get('c') == $category->id,
                'linkOptions'=> $this->btnOptions,
            ];           
            foreach ($category->children(1)->andWhere(['active'=>1])->all() as $sub)
            {
                $subItems[] = [ 
                'label'=>$sub->name,
                'url' => Url::current(['c'=>$category->id, 's'=>$sub->id, 'page'=>null, 's2'=>null]),
                'active'=>$request->get('s')==$sub->id,
                'linkOptions'=> $this->btnOptions];
            }
            $mainItems[] = [
                'label'=>$category->name,
                'url' => Url::current(['c'=>$category->id, 's'=>null, 'page'=>null, 's2'=>null]),
                'active'=>$request->get('c') == $category->id,
                'linkOptions'=> $this->btnOptions,
                'items' => $subItems
            ];


        }
        $mainItems[] = [
                'label'=>"<i class='fa fa-heart'></i> ".Yii::t('app', 'Ulubione'),
                'url' => Url::current(['c'=>'favorite', 's'=>null, 'page'=>null, 's2'=>null]),
                'active'=>$request->get('c') == 'favorite',
                'linkOptions'=> ['class' => 'category-menu-link favorite'],
            ];
//        $subCategories = GearCategory::find()
//            ->subcategories($this->_currentMainId)
//            ->orderBy(['name'=>SORT_ASC])
//            ->all();

        $cat = GearCategory::findOne($this->_currentMainId);
        $subCategories = [];
        if ($cat !== null) {
            $subCategories = $cat->children(1)->andWhere(['active'=>1])->all();

//            if ($this->_currentSubId === false && sizeof($subCategories) > 0) {
//                Yii::$app->controller->redirect(Url::current(['c' => $this->_currentMainId, 's' => $subCategories[0]->id]));
//            }
        }


        $subItems = [];
        $index = 0;
        foreach ($subCategories as $category)
        {
            /* @var $catgory GearCategory */
            $subItems[$index][] = [
                'label'=>$category->name,
                'url' => Url::current(['c'=>$this->_currentMainId, 's'=>$category->id, 'page'=>null, 's2'=>null]),
                'active'=>$request->get('s')==$category->id,
                'linkOptions'=> $this->btnOptions,
            ];
        }

        //FIXME: rekurencja!!!
        $index = 1;
        $cat = GearCategory::findOne($this->_currentSubId);
        if ($cat !== null)
        {
            $subCategories = $cat->children(1)->andWhere(['active'=>1])->all();
            foreach ($subCategories as $category)
            {
                /* @var $catgory GearCategory */
                $subItems[$index][] = [
                    'label'=>$category->name,
                    'url' => Url::current(['c'=>$this->_currentMainId, 's'=>$this->_currentSubId, 's2'=>$category->id, 'page'=>null]),
                    'active'=>$request->get('s2')==$category->id,
                    'linkOptions'=> $this->btnOptions,
                ];
            }
        }





        $content =  Nav::widget([
            'items' => $mainItems,
            'encodeLabels' => false,
            'options' => ['class' =>'nav-pills newsystem-bg'], // set this to nav-tab to get tab-styled navigation
        ]);

        foreach ($subItems as $items)
        {
            $content .= Nav::widget([
                'items' => $items,
                'options' => ['class' =>'nav-pills newsystem-second-bg'], // set this to nav-tab to get tab-styled navigation
            ]);
        }

        return $content;
    }
}
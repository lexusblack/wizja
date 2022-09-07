<?php

namespace common\models;

use common\helpers\ArrayHelper;
use common\models\query\GearCategoryQuery;
use kartik\tree\models\TreeTrait;
use Yii;
use \common\models\base\GearCategory as BaseGearCategory;

/**
 * This is the model class for table "gear_category".
 */
class GearCategory extends BaseGearCategory
{
    use TreeTrait;

    /**
     * @var string the classname for the TreeQuery that implements the NestedSetQueryBehavior.
     * If not set this will default to `kartik	ree\models\TreeQuery`.
     */
    public static $treeQueryClass; // change if you need to set your own TreeQuery

    /**
     * @var bool whether to HTML encode the tree node names. Defaults to `true`.
     */
    public $encodeNodeNames = true;

    /**
     * @var bool whether to HTML purify the tree node icon content before saving.
     * Defaults to `true`.
     */
    public $purifyNodeIcons = true;

    /**
     * @var array activation errors for the node
     */
    public $nodeActivationErrors = [];

    /**
     * @var array node removal errors
     */
    public $nodeRemovalErrors = [];

    /**
     * @var bool attribute to cache the `active` state before a model update. Defaults to `true`.
     */
    public $activeOrig = true;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return parent::tableName();
    }
    public static function find()
    {
        return new GearCategoryQuery(get_called_class());
    }

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        $this->removable_all = 1;
        return true;
    }

    public function getDiscount()
    {
        return $this->hasOne(\common\models\CustomerDiscount::className(), ['gear_cat_id' => 'id']);
    }

    public static function getFullList($asModels = false){

	    $roots = self::find()->indexBy('id')->where(['active'=>1, 'visible'=>1, 'readonly'=>0])->addOrderBy('root, lft')->all();

        if ($asModels == false)
        {
            $list = ArrayHelper::map($roots, 'id', 'name');
        }
        else
        {
            $list = $roots;
        }
        return $list;
	}

    public static function getMainList($asModels = false)
    {
        $root = static::findOne(1);
        $models = $root->children(1)->andWhere(['active'=>1])->all();
        $list = [];
        if ($asModels == false)
        {
            $list = ArrayHelper::map($models, 'id', 'name');
        }
        else
        {
            $list = $models;
        }
        return $list;

    }

    public static function getTreeList()
    {
        $mainCategories = GearCategory::getMainList(true);

        $mainItems = [];
        foreach ($mainCategories as $category)
        {
            /* @var $catgory GearCategory */
            $mainItems[$category->id] = [];
            $mainItems[$category->id]['name'] = $category->name;
            $subItems = [];
            $subItems[$category->id] = $category->name." ".Yii::t('app', 'WSZYSTKIE');           
            foreach ($category->children(1)->andWhere(['active'=>1])->all() as $sub)
            {
                $subItems[$sub->id] = $sub->name; 
            }
            $mainItems[$category->id]['subCategories']= $subItems;


        }
        return $mainItems;
    }
    public static function getMainRootList($asModels = false)
    {
        $root = static::findOne(1);
        $models = $root->children(1)->andWhere(['active'=>1])->all();
        $roots = self::find()->indexBy('id')->where(['active'=>1, 'visible'=>1, 'readonly'=>0])->addOrderBy('root, lft')->all();
        $list = [];        
        if ($asModels == false)
        {
            $list +=[1=>Yii::t('app', 'Wszystkie')];
            $list += ArrayHelper::map($roots, 'id', 'name');


        }
        else
        {
            $list = $models;
        }
        return $list;

    }
//
//    public static function getGroupedList()
//    {
//        $models = static::find()
//            ->where(['not', ['parent_id'=>null]])
//            ->all();
//
//        $list = ArrayHelper::map($models, 'id', 'name', 'parent.name');
//        return $list;
//    }

    /**
     * @param $name Nazwa kategorii
     * @return static Kategoria
     */
    public static function loadMainByName($name)
    {
        return static::findOne([
           'name'=>$name,
            'lvl' => 1,
        ]);
    }

    public function getMainCategory()
    {
        $model = null;
        if($this->lvl == 1)
        {
            $model = $this;
        }
        else
        {
            $model = $this->parents()->andWhere(['lvl'=>1])->one();
        }

        return $model;

    }

    public static function getTranslateName($language, $name)
    {
        $model = GearCategory::findOne(['name'=>$name]);
        if (!$model)
            return $name;
        if (!$language)
            return $model->name;
        else{
            $translate = GearCategoryTranslate::find()->where(['language_id'=>$language])->andWhere(['gear_category_id'=>$model->id])->one();
            if ($translate)
                return $translate->name;
            else
                return $model->name;
        }
    }


    public function getStyle()
    {
        $style = "style='";
        if ($this->color)
        {
            $style .="background-color:".$this->color."; ";
        }
        if ($this->font_color)
        {
            $style .="color:".$this->font_color.";";
        }
        $style .="'";
        return $style;
    }
}

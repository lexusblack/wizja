<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\tree\TreeView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GearCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Kategorie sprzętu');
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
<?= Html::a(Yii::t('app', 'Tłumaczenia'), ['/gear-category-translate/index'], ['class' => 'btn btn-info']) ?>
</p>
<div class="gear-category-tree">
    <?php
    echo TreeView::widget([
        // single query fetch to render the tree
        // use the Product model you have in the previous step
        'query' => $query,
//        'nodeView'=>'@app/views/gear-category/_treeForm',
        'iconEditSettings'=>[
            'show'=>'none',
        ],
        'allowNewRoots' => false,
        'headingOptions' => ['label' => Yii::t('app', 'Kategorie')],
        'fontAwesome' => false,     // optional
        'isAdmin' => false,         // optional (toggle to enable admin mode)
        'displayValue' => 1,        // initial display value
        'softDelete' => true,       // defaults to true
        'cacheSettings' => [
            'enableCache' => true   // defaults to true
        ]
    ]);
    ?>
</div>
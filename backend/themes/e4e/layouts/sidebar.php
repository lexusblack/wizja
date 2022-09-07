<?php
use yii\helpers\Url;
?>
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
    <ul class="nav metismenu">
    <li class="nav-header" style="padding:0">
        <div class="profile-element white-bg"><img alt="image" class="img-responsive" src="/themes/e4e/img/newemwhite.png"></div>
    </li>
        <li class="nav-header">
                    <div class="dropdown profile-element"> <span>
                            <?php echo Yii::$app->user->identity->getUserPhoto("img-circle img-small"); ?>
                             </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?=\Yii::$app->user->identity->displayLabel?></strong>
                             </span> <span class="text-muted text-xs block"><?php echo Yii::$app->user->identity->getRoleName(); ?><b class="caret"></b></span> </span> </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li>            
                                <a href="<?=Url::to(['/site/logout'])?>" data-method="post"><?= Yii::t('app', 'Wyloguj') ?></a>
                            </li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        <?php echo Yii::$app->user->identity->getInitials(); ?>
                    </div>
                </li>
    </ul>
        <?php
            $menu = require_once '_menuItems.php';
            if(isset($this->context->menu))
                $menu = array_merge([['label' => \Yii::$app->name, 'options' => ['class' => 'nav-header']]], $this->context->menu);

        ?>
        <?= jcabanillas\inspinia\widgets\Menu::widget(
            [
                'options' => ['class' => 'nav metismenu', 'id'=>'side-menu'],
                'submenuTemplate' => "\n<ul class='nav nav-second-level collapse' {show}>\n{items}\n</ul>\n",
                'items' => $menu,
                'encodeLabels'=>false
            ]
        ) ?>
    </div>
</nav>
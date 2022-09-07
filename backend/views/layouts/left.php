<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/avatar5.png" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?php echo Yii::$app->user->identity->username; ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
<!--        <form action="#" method="get" class="sidebar-form">-->
<!--            <div class="input-group">-->
<!--                <input type="text" name="q" class="form-control" placeholder="Search..."/>-->
<!--              <span class="input-group-btn">-->
<!--                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>-->
<!--                </button>-->
<!--              </span>-->
<!--            </div>-->
<!--        </form>-->
        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => [
                    ['label' => 'Kalendarz', 'icon'=>'fa fa-calendar', 'url'=>['site/index']],
                    ['label' => 'Menu', 'options' => ['class' => 'header']],
                    ['label' => 'Wydarzenie', 'icon'=>'fa fa-list', 'url'=>['event/index']],
                    ['label' => 'Załączniki', 'icon'=>'fa fa-paperclip', 'url'=>['attachment/index']],
                    ['label' => 'Oddział', 'icon'=>'fa fa-file-image-o', 'url'=>['department/index']],
                    ['label' => 'Klienci', 'icon'=>'fa fa-user', 'url'=>['customer/index'], 'items'=>[
                        ['label' => 'Klient', 'icon'=>'fa fa-user', 'url'=>['customer/index']],
                        ['label' => 'Kontakt', 'icon'=>'fa fa-envelope', 'url'=>['contact/index']],

                    ]],
                    ['label' => 'Miejsca', 'icon'=>'fa fa-map-marker', 'url'=>['location/index']],
                    [
                        'label' => 'Sprzęt',
                        'icon' => 'fa fa-cogs',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Model', 'icon' => 'fa fa-object-group', 'url' => ['gear/index'],],
                            ['label' => 'Sprzęt', 'icon' => 'fa fa-square', 'url' => ['gear-item/index'],],
                            ['label' => 'Zestawy', 'icon' => 'fa fa-archive', 'url' => ['gear-group/index'],],
                            ['label' => 'Kategorie', 'icon' => 'fa fa-list', 'url' => ['gear-category/index'],],
//                            [
//                                'label' => 'Level One',
//                                'icon' => 'fa fa-circle-o',
//                                'url' => '#',
//                                'items' => [
//                                    ['label' => 'Level Two', 'icon' => 'fa fa-circle-o', 'url' => '#',],
//                                    [
//                                        'label' => 'Level Two',
//                                        'icon' => 'fa fa-circle-o',
//                                        'url' => '#',
//                                        'items' => [
//                                            ['label' => 'Level Three', 'icon' => 'fa fa-circle-o', 'url' => '#',],
//                                            ['label' => 'Level Three', 'icon' => 'fa fa-circle-o', 'url' => '#',],
//                                        ],
//                                    ],
//                                ],
//                            ],
                        ],
                    ],
                    ['label' => 'Użytkownicy', 'icon'=>'fa fa-users', 'url'=>['user/index']],
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                ],
            ]
        ) ?>

    </section>

</aside>

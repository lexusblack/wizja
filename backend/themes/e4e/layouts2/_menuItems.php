<?php
use yii\bootstrap\Html;
$user = Yii::$app->user;
$menuItems = [
    ['label' => 'Home', 'url' => ['/site/index']],
];
if (Yii::$app->user->isGuest) {
    $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
} else {
    $menuItems = [
        ['label' => Html::tag('span', Yii::t('app', 'Kalendarz')) , 'icon'=>'fa fa-calendar', 'url'=>['/site/calendar'], 'visible'=>$user->can('menuCalendar')],
        ['label' => Html::tag('span', Yii::t('app', 'Planboard')) , 'icon'=>'fa fa-calendar', 'url'=>['/planboard'], 'visible'=>$user->can('menuPlanboard')],
        ['label' => Html::tag('span', Yii::t('app', 'Wydarzenia')), 'icon'=>'fa fa-list', 'url'=>'#', 'items'=>[
            ['label' => Html::tag('span', Yii::t('app', 'Wydarzenie')), 'icon'=>'fa fa-list', 'url'=>['/event/index'], 'visible'=>$user->can('menuEventsEvent')],
            ['label' => Html::tag('span', Yii::t('app', 'Spotkanie')), 'icon'=>'fa fa-list', 'url'=>['/meeting/index'], 'visible'=>$user->can('menuEventsMeeting')],
            ['label' => Html::tag('span', Yii::t('app', 'Wydarzenie prywatne')), 'icon'=>'fa fa-list', 'url'=>['/personal/index'], 'visible'=>$user->can('menuEventsPrivate')],
            ['label' => Html::tag('span', Yii::t('app', 'Wypożyczenie')), 'icon'=>'fa fa-list', 'url'=>['/rent/index'], 'visible'=>$user->can('menuEventsRent')],
            ['label' => Html::tag('span', Yii::t('app', 'Urlop')), 'icon'=>'fa fa-list', 'url'=>['/vacation/index'], 'visible'=>$user->can('menuEventsVacation')],
            ],
            'visible'=>$user->can('menuEvents'),
        ],
        ['label' => Html::tag('span', Yii::t('app', 'Klienci')), 'icon'=>'fa fa-user', 'url'=>['/customer/index'], 'items'=>[
            ['label' => Html::tag('span', Yii::t('app', 'Klient')), 'icon'=>'fa fa-user', 'url'=>['/customer/index'], 'visible'=>$user->can('menuCustomersCustomer')],
            ['label' => Html::tag('span', Yii::t('app', 'Rabaty')), 'icon'=>'fa fa-user', 'url'=>['/customer-discount'], 'visible'=>$user->can('menuCustomersDiscount')],
            ['label' => Html::tag('span', Yii::t('app', 'Kontakt')), 'icon'=>'fa fa-envelope', 'url'=>['/contact/index'], 'visible'=>$user->can('menuCustomersContact')],

            ],
            'visible'=>$user->can('menuCustomers'),
        ],
//                        ['label' => 'Miejsca', 'icon'=>'fa fa-map-marker', 'url'=>['location/index']],
        [
            'label' => Html::tag('span', Yii::t('app','Miejsca')),
            'icon' => 'fa fa-map-marker',
            'url' => '#',
            'items' => [
                ['label' => Html::tag('span', Yii::t('app','Miejsce')), 'icon' => 'fa fa-object-group', 'url' => ['/location/index'], 'visible'=>$user->can('menuLocationsLocation')],
                ['label' => Html::tag('span', Yii::t('app','Załączniki')), 'icon' => 'fa fa-square', 'url' => ['/location-attachment/index'], 'visible'=>$user->can('menuLocationsAttachment')],
            ],
            'visible'=>$user->can('menuLocations'),
        ],
        [
            'label' => Html::tag('span', Yii::t('app','Sprzęt')),
            'icon' => 'fa fa-cogs',
            'url' => '#',
            'items' => [
                ['label' => Html::tag('span', Yii::t('app','Magazyn')), 'icon' => 'fa fa-object-group', 'url' => ['/warehouse/index'], 'visible'=>$user->can('menuGearsWarehouse')],
                ['label' => Html::tag('span', Yii::t('app','Magazyn zewnętrzny')), 'icon' => 'fa fa-object-group', 'url' => ['/outer-warehouse/index'], 'visible'=>$user->can('menuGearsOuterWarehouse')],
                ['label' => Html::tag('span', Yii::t('app','Wydanie z magazynu')), 'icon' => 'fa fa-object-group', 'url' => ['/outcomes-warehouse/index'], 'visible'=>$user->can('menuGearsOutcome'),],
                ['label' => Html::tag('span', Yii::t('app','Przyjęcie do magazynu')), 'icon' => 'fa fa-object-group', 'url' => ['/incomes-warehouse/index'], 'visible'=>$user->can('menuGearsIncome'),],
                ['label' => Html::tag('span', Yii::t('app','Model')), 'icon' => 'fa fa-object-group', 'url' => ['/gear/index'], 'visible'=>$user->can('menuGearsGear')],
                ['label' => Html::tag('span', Yii::t('app','Sprzęt')), 'icon' => 'fa fa-square', 'url' => ['/gear-item/index'], 'visible'=>$user->can('menuGearsGearItem')],
                ['label' => Html::tag('span', Yii::t('app','Zestawy')), 'icon' => 'fa fa-archive', 'url' => ['/gear-group/index'], 'visible'=>$user->can('menuGearsGearGroup')],
                ['label' => Html::tag('span', Yii::t('app','Kategorie')), 'icon' => 'fa fa-list', 'url' => ['/gear-category/index'], 'visible'=>$user->can('menuGearsCategory')],
                ['label' => Html::tag('span', Yii::t('app','Załączniki modeli')), 'icon' => 'fa fa-object-group', 'url' => ['/gear-attachment/index'], 'visible'=>$user->can('menuGearsGearAttachment')],
                ['label' => Html::tag('span', Yii::t('app','Serwis')), 'icon' => 'fa fa-object-group', 'url' => ['/gear-service/index'], 'visible'=>$user->can('menuGearsService')],

            ],
            'visible'=>$user->can('menuGears'),
        ],
        ['label' => Html::tag('span', Yii::t('app','Użytkownicy')), 'icon'=>'fa fa-users', 'url'=>'#', 'items'=>[
            ['label' => Html::tag('span', Yii::t('app','Użytkownicy')), 'icon'=>'fa fa-users', 'url'=>['/user/index'], 'visible'=>$user->can('menuUsersUser')],
            ['label' => Html::tag('span', Yii::t('app','Umiejętności')), 'icon'=>'fa fa-users', 'url'=>['/skill/index'], 'visible'=>$user->can('menuUsersSkill')],
            ['label' => Html::tag('span', Yii::t('app','Rozliczenia')), 'icon'=>'fa fa-users', 'url'=>['/settlement/user/index'], 'visible'=>$user->can('menuUsersSettlement')],
            ],
            'visible'=>$user->can('menuUsers'),
        ],
        ['label' => Html::tag('span', Yii::t('app','Flota')), 'icon'=>'fa fa-calendar', 'url'=>'#', 'items'=>[
                ['label' => Html::tag('span', Yii::t('app','Pojazdy')), 'icon'=>'fa fa-calendar', 'url'=>['/vehicle'], 'visible'=>$user->can('menuVehiclesVehicle')],
                ['label' => Html::tag('span', Yii::t('app','Załączniki')), 'icon'=>'fa fa-calendar', 'url'=>['/vehicle-attachment'], 'visible'=>$user->can('menuVehiclesAttachment')],
            ],
            'visible'=>$user->can('menuVehicles'),
        ],
        [
            'label'=>Html::tag('span', Yii::t('app', 'Ustawienia')), 'icon'=>'fa fa-wrench', 'url'=>['/user-event-role/index'],
            'visible'=>$user->can('menuSettings'),
        ],
        ['label' => Html::tag('span', Yii::t('app', 'Oferty')), 'icon'=>'fa fa-shopping-cart', 'url'=>['/offer'], 'visible'=>$user->can('menuOffers'),],
        ['label' => Html::tag('span', Yii::t('app', 'Taski')), 'icon'=>'fa fa-tasks', 'url'=>['/task/index'], 'visible'=>$user->can('menuTasks'),],
        ['label' => Html::tag('span', Yii::t('app', 'Rozliczenia')), 'icon'=>'fa fa-users', 'url'=>['/settlement/user/show'], 'visible'=>$user->can('menuSettlements')],
        ['label' => Html::tag('span', Yii::t('app','Faktury')), 'icon'=>'fa fa-money', 'url'=>'#', 'items'=>[
                ['label' => Html::tag('span', Yii::t('app','Przychody')), 'icon'=>'fa fa-arrow-leftr', 'url'=>['/finances/invoice/index'], 'visible'=>$user->can('menuFinancesInvoice')],
                ['label' => Html::tag('span', Yii::t('app','Wydatki')), 'icon'=>'fa fa-arrow_right', 'url'=>['/finances/expense/index'],'visible'=>$user->can('menuFinancesExpense')],
                ['label' => Html::tag('span', Yii::t('app','Serie')), 'icon'=>'fa fa-wrench', 'url'=>['/finances/invoice-serie/index'], 'visible'=>$user->can('menuFinancesSerie')],
            ],
            'visible'=>$user->can('menuFinances'),
        ],
        ['label'=> Html::tag('span', Yii::t('app', 'Wyloguj')).' ('.$user->identity->username.')', 'url'=>['/site/logout'],
            'linkOptions'=>['data-method' => 'post'], 'visible'=>!Yii::$app->user->isGuest],

    ];

}
return $menuItems;
<?php
use yii\bootstrap\Html;
$user = Yii::$app->user;
         $mineNotDone = $user->identity->getNotDoneCount();
         $mineAfterTime = $user->identity->getAfterTimeDoneCount();
         $myDate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-3 days" ) );
         $myDate2 = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "+ 10 days" ) );
        $eventIds = \common\helpers\ArrayHelper::map(\common\models\Event::find()->where(['>', 'event_end', $myDate])->andWhere(['<', 'event_start', $myDate2])->asArray()->all(), 'id', 'id');
        $conflictsCount = \common\models\EventConflict::find()->where(['resolved'=>0])->andWhere(['in', 'event_id', $eventIds])->count();
        $warehouses = \common\models\Warehouse::find()->asArray()->all();
        if (count($warehouses)>2)
        {
            $wa = [['label' =>Yii::t('app','Magazyn łącznie'), 'icon' => 'fa fa-home', 'url' => ['/warehouse/index'], 'visible'=>$user->can('gearOurWarehouse')],];
            foreach ($warehouses as $w)
            {
                $wa[] = ['label' =>$w['name'], 'icon' => 'fa fa-home', 'url' => ['/warehouse/warehouse', 'w'=>$w['id'], 'c'=>1], 'visible'=>$user->can('gearOurWarehouse')];
            }
                                $w_array = [
                ['label' =>Yii::t('app','Magazyn wewn.'), 'icon' => 'fa fa-home', 'url' => ['/warehouse/index'], 'visible'=>$user->can('gearOurWarehouse'), 'items'=>$wa],
                ['label' =>Yii::t('app','Magazyn zewnętrzny'), 'icon' => 'fa fa-globe', 'url' => ['/outer-warehouse/index'], 'visible'=>$user->can('gearOuterWarehouse')],
                                ['label' =>Yii::t('app','Ceny'), 'icon' => 'fa fa-dollar', 'url' => ['/gear/prices'], 'visible'=>$user->can('gearPrice')],
                ['label' =>Yii::t('app','Wydanie z magazynu'), 'icon' => 'fa fa-arrow-right', 'url' => ['/outcomes-warehouse/index'], 'visible'=>$user->can('gearWarehouseOutcomes'),],
                ['label' =>Yii::t('app','Przyjęcie do magazynu'), 'icon' => 'fa fa-arrow-left', 'url' => ['/incomes-warehouse/index'], 'visible'=>$user->can('gearWarehouseIncomes'),],
                                ['label' =>Yii::t('app','Niezwrócony sprzęt'), 'icon' => 'fa fa-arrow-right', 'url' => ['/outcomes-warehouse/get-all'], 'visible'=>$user->can('gearOurWarehouse'),],
                ['label' =>Yii::t('app','Zamówienia'), 'icon' => 'fa fa-shopping-cart', 'url' => ['/order/index'], 'visible'=>$user->can('menuOrders'),],
                ['label' =>Yii::t('app','Modele'), 'icon' => 'fa fa-desktop', 'url' => ['/gear/index'], 'visible'=>$user->can('gearModel')],
                ['label' =>Yii::t('app','Egzemplarze'), 'icon' => 'fa fa-plug', 'url' => ['/gear-item/index'], 'visible'=>$user->can('gearGear')],
                ['label' =>Yii::t('app','Case'), 'icon' => 'fa fa-archive', 'url' => ['/gear-group/index'], 'visible'=>$user->can('gearCase')],
                ['label' =>Yii::t('app','Zestawy'), 'icon' => 'fa fa-archive', 'url' => ['/gear-set/index'], 'visible'=>$user->can('gearSet')],
                ['label' =>Yii::t('app','Kategorie'), 'icon' => 'fa fa-list', 'url' => ['/gear-category/index'], 'visible'=>$user->can('gearCategories')],
                ['label' =>Yii::t('app','Załączniki modeli'), 'icon' => 'fa fa-paperclip', 'url' => ['/gear-attachment/index'], 'visible'=>$user->can('gearAttachments')],
                ['label' =>Yii::t('app','Baza sprzętu'), 'icon' => 'fa fa-plug', 'url' => ['/gear-model/index'], 'visible'=>$user->can('gearOurWarehouseAddFromGearBase')],
                ['label' =>Yii::t('app','Producenci sprzętu'), 'icon' => 'fa fa-plug', 'url' => ['/gear-company/index'], 'visible'=>$user->can('gearProducer')],
                ['label' =>Yii::t('app','NEIS Log'), 'icon' => 'fa fa-plug', 'url' => ['/rfid-log/index'], 'visible'=>$user->can('gearRfid')],
                ['label' =>Yii::t('app','Zarządzaj magazynami'), 'icon' => 'fa fa-plug', 'url' => ['/warehouse/indexw'], 'visible'=>$user->can('gearManageWarehouse')]];
        }else{
                    $w_array = [
                ['label' =>Yii::t('app','Magazyn wewnętrzny'), 'icon' => 'fa fa-home', 'url' => ['/warehouse/index'], 'visible'=>$user->can('gearOurWarehouse')],
                ['label' =>Yii::t('app','Magazyn zewnętrzny'), 'icon' => 'fa fa-globe', 'url' => ['/outer-warehouse/index'], 'visible'=>$user->can('gearOuterWarehouse')],
                                ['label' =>Yii::t('app','Ceny'), 'icon' => 'fa fa-dollar', 'url' => ['/gear/prices'], 'visible'=>$user->can('gearPrice')],
                ['label' =>Yii::t('app','Wydanie z magazynu'), 'icon' => 'fa fa-arrow-right', 'url' => ['/outcomes-warehouse/index'], 'visible'=>$user->can('gearWarehouseOutcomes'),],
                ['label' =>Yii::t('app','Przyjęcie do magazynu'), 'icon' => 'fa fa-arrow-left', 'url' => ['/incomes-warehouse/index'], 'visible'=>$user->can('gearWarehouseIncomes'),],
                                ['label' =>Yii::t('app','Niezwrócony sprzęt'), 'icon' => 'fa fa-arrow-right', 'url' => ['/outcomes-warehouse/get-all'], 'visible'=>$user->can('gearOurWarehouse'),],
                ['label' =>Yii::t('app','Zamówienia'), 'icon' => 'fa fa-shopping-cart', 'url' => ['/order/index'], 'visible'=>$user->can('menuOrders'),],
                ['label' =>Yii::t('app','Modele'), 'icon' => 'fa fa-desktop', 'url' => ['/gear/index'], 'visible'=>$user->can('gearModel')],
                ['label' =>Yii::t('app','Egzemplarze'), 'icon' => 'fa fa-plug', 'url' => ['/gear-item/index'], 'visible'=>$user->can('gearGear')],
                ['label' =>Yii::t('app','Case'), 'icon' => 'fa fa-archive', 'url' => ['/gear-group/index'], 'visible'=>$user->can('gearCase')],
                ['label' =>Yii::t('app','Zestawy'), 'icon' => 'fa fa-archive', 'url' => ['/gear-set/index'], 'visible'=>$user->can('gearSet')],
                ['label' =>Yii::t('app','Kategorie'), 'icon' => 'fa fa-list', 'url' => ['/gear-category/index'], 'visible'=>$user->can('gearCategories')],
                ['label' =>Yii::t('app','Załączniki modeli'), 'icon' => 'fa fa-paperclip', 'url' => ['/gear-attachment/index'], 'visible'=>$user->can('gearAttachments')],
                ['label' =>Yii::t('app','Baza sprzętu'), 'icon' => 'fa fa-plug', 'url' => ['/gear-model/index'], 'visible'=>$user->can('gearOurWarehouseAddFromGearBase')],
                ['label' =>Yii::t('app','Producenci sprzętu'), 'icon' => 'fa fa-plug', 'url' => ['/gear-company/index'], 'visible'=>$user->can('gearProducer')],
                ['label' =>Yii::t('app','NEIS Log'), 'icon' => 'fa fa-plug', 'url' => ['/rfid-log/index'], 'visible'=>$user->can('gearRfid')],
                ['label' =>Yii::t('app','Zarządzaj magazynami'), 'icon' => 'fa fa-plug', 'url' => ['/warehouse/indexw'], 'visible'=>$user->can('gearManageWarehouse')]];
        }


$menuItems = [
    ['label' => 'Home', 'url' => ['/site/index']],
];
if (Yii::$app->user->isGuest) {
    $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
} else {
    if (Yii::$app->session->get('company')!=1)
    $menuItems = [
        ['label' => Yii::t('app', 'Kokpit') , 'icon'=>'fa fa-home', 'url'=>['/site/index'], 'visible'=>$user->can('menuCockpit')],
        ['label' =>Yii::t('app', 'Zadania').' <span class="label  pull-right" style="margin-left:10px">'.$mineNotDone.'</span>'.' <span class="label label-danger pull-right" style="margin-left:10px">'.$mineAfterTime.'</span>', 'url'=>'#', 'visible'=>$user->can('menuTasks'), 'items'=>[
                ['label' =>Yii::t('app', 'Zadania'), 'icon'=>'fa fa-tasks', 'url'=>['/task/index'], 'visible'=>true],
                ['label' =>Yii::t('app', 'Schematy zadań'), 'icon'=>'fa fa-tasks', 'url'=>['/tasks-schema/index'], 'visible'=>true]]
            ],
        ['label' => Yii::t('app', 'Kalendarz') , 'icon'=>'fa fa-calendar', 'url'=>['/site/calendar'], 'visible'=>$user->can('menuCalendar')],
        //['label' => Yii::t('app', 'Plan Timeline') , 'icon'=>'fa fa-calendar-o', 'url'=>['/planboard'], 'visible'=>$user->can('menuPlanboard')],
        ['label' => Yii::t('app', 'Plan Timeline') , 'icon'=>'fa fa-calendar-o', 'url'=>['/planboard/test'], 'visible'=>$user->can('menuPlanboard')],
        ['label' => Yii::t('app', 'Wydarzenia'), 'icon'=>'fa fa-star', 'url'=>'#', 'items'=>[
            ['label' => Yii::t('app', 'Projekty BETA'), 'icon'=>'fa fa-star', 'url'=>['/project/index'], 'visible'=>$user->can('eventsProjects')],
            ['label' => Yii::t('app', 'Wydarzenia'), 'icon'=>'fa fa-star', 'url'=>['/event/index'], 'visible'=>$user->can('eventsEvents')],
            ['label' => Yii::t('app', 'Spotkania'), 'icon'=>'fa fa-coffee', 'url'=>['/meeting/index'], 'visible'=>$user->can('eventsMeetings')],
            ['label' => Yii::t('app', 'Wydarzenia prywatne'), 'icon'=>'fa fa-star-o', 'url'=>['/personal/index'], 'visible'=>$user->can('eventsMeetingsPrivate')],
            ['label' => Yii::t('app', 'Wypożyczenia'), 'icon'=>'fa fa-list', 'url'=>['/rent/index'], 'visible'=>$user->can('eventRents')],
            ['label' => Yii::t('app', 'Urlopy'), 'icon'=>'fa fa-glass', 'url'=>['/vacation/index'], 'visible'=>$user->can('eventVacations')],

            ],
            'visible'=>$user->can('menuEvents'),
        ],
        ['label' => Yii::t('app', 'Kontrahenci'), 'icon'=>'fa fa-user', 'url'=>['/customer/index'], 'items'=>[
            ['label' => Yii::t('app', 'Kontrahenci'), 'icon'=>'fa fa-user', 'url'=>['/customer/index'], 'visible'=>$user->can('clientClients')],
            //['label' => Yii::t('app', 'Rabaty'), 'icon'=>'fa fa-money', 'url'=>['/customer-discount'], 'visible'=>$user->can('clientDiscount')],
            ['label' => Yii::t('app', 'Kontakty'), 'icon'=>'fa fa-envelope', 'url'=>['/contact/index'], 'visible'=>$user->can('clientContacts')],
             ['label' => Yii::t('app', 'Grupy kontrahentów'), 'icon'=>'fa fa-group', 'url'=>['/customer-type/index'], 'visible'=>$user->can('clientClients')],
            ],
            'visible'=>$user->can('menuClients'),
        ],
        [
            'label' => Yii::t('app','Miejsca'),
            'icon' => 'fa fa-map-marker',
            'url' => '#',
            'items' => [
                ['label' => Yii::t('app','Miejsca'), 'icon' => 'fa fa-map-marker', 'url' => ['/location/index'], 'visible'=>$user->can('locationLocations')],
                ['label' => Yii::t('app','Załączniki'), 'icon' => 'fa fa-chain', 'url' => ['/location-attachment/index'], 'visible'=>$user->can('locationAttachments')],
            ],
            'visible'=>$user->can('menuLocations'),
        ],
        [
            'label' => Yii::t('app','Event Network'),
            'icon' => 'fa fa-globe',
            'url' => '#',
            'items'=>[
            ['label' =>Yii::t('app','Cross Rental Network'), 'icon' => 'fa fa-globe', 'url' => ['/cross-rental/index'], 'visible'=>$user->can('gearCrossRental')],
            ['label' => Yii::t('app','Miejsca eventowe'), 'icon' => 'fa fa-map-marker', 'url' => ['/location/public'], 'visible'=>$user->can('locationLocations')],
            ],
             'visible'=>$user->can('gearCrossRental')
        ],
        [
            'label' =>Yii::t('app','Magazyn'),
            'icon' => 'fa fa-plug',
            'url' => '#',
            'items' => [
                ['label' =>Yii::t('app','Magazyn wewnętrzny'), 'icon' => 'fa fa-home', 'url' => ['/warehouse/index'], 'visible'=>$user->can('gearOurWarehouse')],
                ['label' =>Yii::t('app','Usługi zewnętrzne'), 'icon' => 'fa fa-globe', 'url' => ['/outer-warehouse/index'], 'visible'=>$user->can('gearOuterWarehouse')],
                //['label' =>Yii::t('app','Ceny'), 'icon' => 'fa fa-coin', 'url' => ['/gear/prices'], 'visible'=>$user->can('gearOurWarehouse')],
                ['label' =>Yii::t('app','Wydanie z magazynu'), 'icon' => 'fa fa-arrow-right', 'url' => ['/outcomes-warehouse/index'], 'visible'=>$user->can('gearWarehouseOutcomes'),],
                ['label' =>Yii::t('app','Przyjęcie do magazynu'), 'icon' => 'fa fa-arrow-left', 'url' => ['/incomes-warehouse/index'], 'visible'=>$user->can('gearWarehouseIncomes'),],
                ['label' =>Yii::t('app','Niezwrócony sprzęt'), 'icon' => 'fa fa-arrow-right', 'url' => ['/outcomes-warehouse/get-all'], 'visible'=>$user->can('gearOurWarehouse'),],
                ['label' =>Yii::t('app','Modele'), 'icon' => 'fa fa-desktop', 'url' => ['/gear/index'], 'visible'=>$user->can('gearModel')],
                ['label' =>Yii::t('app','Egzemplarze'), 'icon' => 'fa fa-plug', 'url' => ['/gear-item/index'], 'visible'=>$user->can('gearGear')],
                ['label' =>Yii::t('app','Case'), 'icon' => 'fa fa-archive', 'url' => ['/gear-group/index'], 'visible'=>$user->can('gearCase')],
                ['label' =>Yii::t('app','Zestawy'), 'icon' => 'fa fa-archive', 'url' => ['/gear-set/index'], 'visible'=>$user->can('gearSet')],
                ['label' =>Yii::t('app','Kategorie'), 'icon' => 'fa fa-list', 'url' => ['/gear-category/index'], 'visible'=>$user->can('gearCategories')],
                ['label' =>Yii::t('app','Załączniki modeli'), 'icon' => 'fa fa-paperclip', 'url' => ['/gear-attachment/index'], 'visible'=>$user->can('gearAttachments')],
                ['label' =>Yii::t('app','Serwis'), 'icon' => 'fa fa-cogs', 'url' => ['/gear-service/index'], 'visible'=>$user->can('gearService')],
                ['label' =>Yii::t('app','Baza sprzętu'), 'icon' => 'fa fa-plug', 'url' => ['/gear-model/index'], 'visible'=>$user->can('gearOurWarehouseAddFromGearBase')],
                ['label' =>Yii::t('app','Producenci sprzętu'), 'icon' => 'fa fa-plug', 'url' => ['/gear-company/index'], 'visible'=>$user->can('gearProducer')],
                ['label' =>Yii::t('app','Zarządzaj magazynami'), 'icon' => 'fa fa-plug', 'url' => ['/warehouse/indexw'], 'visible'=>$user->can('gearManageWarehouse')],

                //['label' =>Yii::t('app','RFID'), 'icon' => 'fa fa-plug', 'url' => ['/rfid/index'], 'visible'=>$user->can('gearRfid')],
            ],
            'visible'=>$user->can('menuGear'),
        ],
        ['label' =>Yii::t('app','Użytkownicy'), 'icon'=>'fa fa-users', 'url'=>'#', 'items'=>[
            ['label' =>Yii::t('app','Użytkownicy'), 'icon'=>'fa fa-users', 'url'=>['/user/index'], 'visible'=>$user->can('usersUsers')],
                        ['label' =>Yii::t('app','Zespoły'), 'icon'=>'fa fa-users', 'url'=>['/team/index'], 'visible'=>$user->can('usersUsers')],

            ['label' =>Yii::t('app','Umiejętności'), 'icon'=>'fa fa-cogs', 'url'=>['/skill/index'], 'visible'=>$user->can('usersSkills')],
            ['label' =>Yii::t('app','Rozliczenia'), 'icon'=>'fa fa-umoney', 'url'=>['/settlement/user/index'], 'visible'=>$user->can('usersPayments')],
            ],
            'visible'=>$user->can('menuUsers'),
        ],
        ['label' =>Yii::t('app','Flota'), 'icon'=>'fa fa-truck', 'url'=>'#', 'items'=>[
                ['label' =>Yii::t('app','Pojazdy'), 'icon'=>'fa fa-car', 'url'=>['/vehicle'], 'visible'=>$user->can('fleetVehicles')],
                ['label' =>Yii::t('app','Modele pojazdów'), 'icon'=>'fa fa-car', 'url'=>['/vehicle-model'], 'visible'=>$user->can('fleetVehicles')],
                ['label' =>Yii::t('app','Przejazdy'), 'icon'=>'fa fa-car', 'url'=>['/ride'], 'visible'=>$user->can('fleetVehicles')],
                ['label' =>Yii::t('app','Załączniki'), 'icon'=>'fa fa-paperclip', 'url'=>['/vehicle-attachment'], 'visible'=>$user->can('fleetAttachments')],
            ],
            'visible'=>$user->can('menuFleet'),
        ],
        [
            'label'=>Yii::t('app', 'Ustawienia'), 'url'=>'#', 'icon'=>'fa fa-wrench',
            'visible'=>$user->can('menuSettings'), 'items'=>[
            [
            'label'=>Yii::t('app', 'Ustawienia'), 'url'=>['/setting/menu-settings'], 'icon'=>'fa fa-wrench',
            'visible'=>$user->can('menuSettings'),],
                        ['label' => Yii::t('app', 'Dodatkowe pola w wydarzeniu'), 'url'=>['/event-field-setting/index'], 'visible'=>$user->can('menuSettings')],

            ['label' => Yii::t('app', 'Rodzaje wydarzeń'), 'url'=>['/event-type/index'], 'visible'=>$user->can('menuSettings')],
                        ['label' => Yii::t('app', 'Typy wydarzeń'), 'url'=>['/event-model/index'], 'visible'=>$user->can('menuSettings')],
            ['label' => Yii::t('app', 'Statusy wydarzeń'), 'url'=>['/event-statut/index', 'type'=>1], 'visible'=>$user->can('menuSettings')],
            ['label' => Yii::t('app', 'Dodatkowe statusy wydarzeń'), 'url'=>['/event-additional-statut/index'], 'visible'=>$user->can('menuSettings')],
            ['label' => Yii::t('app', 'Statusy wypożyczeń'), 'url'=>['/event-statut/index', 'type'=>2], 'visible'=>$user->can('menuSettings')],
            ]
        ],
        ['label' =>Yii::t('app', 'Oferty agencje'), 'icon'=>'fa fa-shopping-cart', 'url'=>'#', 'visible'=>$user->can('menuOffers'), 'items'=>[
                ['label' =>Yii::t('app','Oferty'), 'icon'=>'fa fa-shopping-cart', 'url'=>['/agency-offer/index'], 'visible'=>true],
                ['label' =>Yii::t('app','Szablony'), 'icon'=>'fa fa-list', 'url'=>['/offer-schema'], 'visible'=>true],
                ['label'=>Yii::t('app', 'Statusy'), 'icon'=>'fa fa-check', 'url'=>['/offer-statut', 'visible'=>true]]
            ],
            ],
        ['label' =>Yii::t('app','Wypożyczenia i konflikty'), 'icon'=>'fa fa-shopping-cart', 'url'=>['/order/list'],
            'visible'=>$user->can('menuOrders'),
        ],

        ['label' =>Yii::t('app', 'Rozliczenia'), 'icon'=>'fa fa-trophy', 'url'=>['/settlement/user/show'], 'visible'=>$user->can('menuSettlement')],
        ['label' =>Yii::t('app','Finanse'), 'icon'=>'fa fa-money', 'url'=>'#', 'items'=>[
                ['label' =>Yii::t('app','Faktury przychodowe'), 'icon'=>'fa fa-arrow-left', 'url'=>['/finances/invoice/index'], 'visible'=>$user->can('menuInvoicesInvoice')],
                ['label' =>Yii::t('app','Faktury kosztwe'), 'icon'=>'fa fa-arrow-right', 'url'=>['/finances/expense/index'],'visible'=>$user->can('menuInvoicesExpense')],
                ['label' =>Yii::t('app','Koszty dodatkowe'), 'icon'=>'fa fa-arrow-right', 'url'=>['/event-expense/index'],'visible'=>$user->can('menuInvoicesExpense')],
                ['label' =>Yii::t('app','Serie'), 'icon'=>'fa fa-wrench', 'url'=>['/finances/invoice-serie/index'], 'visible'=>$user->can('financesInvoiceSeries')],
                                ['label' =>Yii::t('app','Koszty miesięczne'), 'icon'=>'fa fa-arrow-left', 'url'=>['/month-cost/index'], 'visible'=>$user->can('menuInvoicesInvoice')],
            ],
            'visible'=>$user->can('menuInvoices'),
        ],
        ['label' =>Yii::t('app','Toolbox'), 'icon'=>'fa fa-wrench', 'url'=>'#', 'items'=>[
                ['label' =>Yii::t('app','Blend Calculator'), 'icon'=>'fa fa-desktop', 'url'=>['/calculator'], 'visible'=>$user->can('menuToolboxBlend')],
                ['label' =>Yii::t('app','ShowTime'), 'icon'=>'fa fa-desktop', 'url'=>['/timer'], 'visible'=>$user->can('menuToolboxShowTime')],
                ['label' =>Yii::t('app','SpacePlanner'), 'icon'=>'fa fa-desktop', 'url'=>['/spaceplanner'], 'visible'=>$user->can('menuToolboxSpacePlanner')]
            ],
            'visible'=>$user->can('menuToolbox'),
        ],
        ['label' => Yii::t('app', 'Instrukcja') , 'icon'=>'fa fa-question', 'url'=>['/site/first-use'], 'visible'=>true],


    ];
    else
     $menuItems = [
        ['label' => Yii::t('app', 'Kokpit') , 'icon'=>'fa fa-home', 'url'=>['/site/index'], 'visible'=>$user->can('menuCockpit')],
        ['label' =>Yii::t('app', 'Zadania').' <span class="label  pull-right" style="margin-left:5px">'.$mineNotDone.'</span>'.' <span class="label label-danger pull-right">'.$mineAfterTime.'</span>', 'url'=>'#', 'visible'=>$user->can('menuTasks'), 'items'=>[
                ['label' =>Yii::t('app', 'Zadania'), 'icon'=>'fa fa-tasks', 'url'=>['/task/index'], 'visible'=>true],
                ['label' =>Yii::t('app', 'Schematy zadań'), 'icon'=>'fa fa-tasks', 'url'=>['/tasks-schema/index'], 'visible'=>$user->can('menuTasksSchema')]]
            ],
        ['label' => Yii::t('app', 'Kalendarz') , 'icon'=>'fa fa-calendar', 'url'=>['/site/calendar'], 'visible'=>$user->can('menuCalendar')],
              //  ['label' => Yii::t('app','Kalendarz powierzchni'), 'icon'=>'fa fa-calendar', 'url' => ['/hall-group/calendar'], 'visible'=>true],

        ['label' => Yii::t('app', 'Plan dnia') , 'icon'=>'fa fa-calendar', 'url'=>['/site/calendar-plan'], 'visible'=>$user->can('menuCalendar')],
        //['label' => Yii::t('app', 'Plan Timeline') , 'icon'=>'fa fa-calendar-o', 'url'=>['/planboard'], 'visible'=>$user->can('menuPlanboard')],
        ['label' => Yii::t('app', 'Plan Timeline') , 'icon'=>'fa fa-calendar-o', 'url'=>['/planboard/test'], 'visible'=>$user->can('menuPlanboard')],
        ['label' => Yii::t('app', 'Wydarzenia'), 'icon'=>'fa fa-star', 'url'=>'#', 'items'=>[
            ['label' => Yii::t('app', 'Projekty'), 'icon'=>'fa fa-star', 'url'=>['/project/index'], 'visible'=>$user->can('eventsProjects')],
            ['label' => Yii::t('app', 'Wydarzenia'), 'icon'=>'fa fa-star', 'url'=>['/event/index'], 'visible'=>$user->can('eventsEvents')],
            ['label' => Yii::t('app', 'Spotkania'), 'icon'=>'fa fa-coffee', 'url'=>['/meeting/index'], 'visible'=>$user->can('eventsMeetings')],
            ['label' => Yii::t('app', 'Wydarzenia prywatne'), 'icon'=>'fa fa-star-o', 'url'=>['/personal/index'], 'visible'=>$user->can('eventsMeetingsPrivate')],
            ['label' => Yii::t('app', 'Wypożyczenia'), 'icon'=>'fa fa-list', 'url'=>['/rent/index'], 'visible'=>$user->can('eventRents')],
            ['label' => Yii::t('app', 'Urlopy'), 'icon'=>'fa fa-glass', 'url'=>['/vacation/index'], 'visible'=>$user->can('eventVacations')],
            ['label' => Yii::t('app', 'Kalendarz Produkcji') , 'icon'=>'fa fa-calendar', 'url'=>['/site/calendar-produkcja'], 'visible'=>$user->can('menuCalendarProdukcja')],
            ['label' => Yii::t('app', 'Kalendarz Wydruki') , 'icon'=>'fa fa-calendar', 'url'=>['/site/calendar-wydruki'], 'visible'=>$user->can('menuCalendarProdukcja')],
            ],
            'visible'=>$user->can('menuEvents'),
        ],
        ['label' => Yii::t('app', 'Kontrahenci'), 'icon'=>'fa fa-user', 'url'=>['/customer/index'], 'items'=>[
            ['label' => Yii::t('app', 'Kontrahenci'), 'icon'=>'fa fa-user', 'url'=>['/customer/index'], 'visible'=>$user->can('clientClients')],
            //['label' => Yii::t('app', 'Rabaty'), 'icon'=>'fa fa-money', 'url'=>['/customer-discount'], 'visible'=>$user->can('clientDiscount')],
            ['label' => Yii::t('app', 'Kontakty'), 'icon'=>'fa fa-envelope', 'url'=>['/contact/index'], 'visible'=>$user->can('clientContacts')],
            ['label' => Yii::t('app', 'Grupy kontrahentów'), 'icon'=>'fa fa-group', 'url'=>['/customer-type/index'], 'visible'=>$user->can('clientClients')],
            ],
            'visible'=>$user->can('menuClients'),
        ],
      /*  [
            'label' => Yii::t('app','Powierzchnie'),
            'icon' => 'fa fa-bank',
            'url' => '#',
            'items' => [
                ['label' => Yii::t('app','Kalendarz'), 'url' => ['/hall-group/calendar'], 'visible'=>true],
                ['label' => Yii::t('app','Powierzchnie'), 'url' => ['/hall-group'], 'visible'=>true],
                ['label' => Yii::t('app','Segmenty'), 'url' => ['/hall'], 'visible'=>true],
                ['label' => Yii::t('app','Typy ustawień widowni'), 'url' => ['/hall-audience-type'], 'visible'=>true],
                ['label' => Yii::t('app','Foldery załączników'), 'url' => ['/hall-group-photo-type'], 'visible'=>true],
            ],
            'visible'=>true,
        ],*/
        [
            'label' => Yii::t('app','Miejsca'),
            'icon' => 'fa fa-map-marker',
            'url' => '#',
            'items' => [
                ['label' => Yii::t('app','Miejsca'), 'icon' => 'fa fa-map-marker', 'url' => ['/location/index'], 'visible'=>$user->can('locationLocations')],
                ['label' => Yii::t('app','Załączniki'), 'icon' => 'fa fa-chain', 'url' => ['/location-attachment/index'], 'visible'=>$user->can('locationAttachments')],
            ],
            'visible'=>$user->can('menuLocations'),
        ],
        [
            'label' => Yii::t('app','Event Network')." ".\common\models\EnNote::getLabels(),
            
            'url' => '#',
            'items'=>[
            ['label' =>Yii::t('app','Aktualności')." ".\common\models\EnNote::getLabels(), 'icon' => 'fa fa-globe', 'url' => ['/en-note/index'], 'visible'=>$user->can('gearCrossRental')],
            ['label' =>Yii::t('app','Cross Rental Network'), 'icon' => 'fa fa-globe', 'url' => ['/cross-rental/index'], 'visible'=>$user->can('gearCrossRental')],
            //['label' =>Yii::t('app','Freelancers Network'), 'icon' => 'fa fa-globe', 'url' => ['/free-offer/index'], 'visible'=>true],
            ['label' => Yii::t('app','Miejsca eventowe'), 'icon' => 'fa fa-map-marker', 'url' => ['/location/public'], 'visible'=>$user->can('locationLocations')],
            ],
             'visible'=>$user->can('gearCrossRental')
        ],
        [
            'label' =>Yii::t('app','Magazyn'),
            'icon' => 'fa fa-plug',
            'url' => '#',
            'items' => $w_array,
            'visible'=>$user->can('menuGear'),
        ],
        ['label' =>Yii::t('app','Serwis')." ".\common\models\GearServiceStatut::getLabels(), 'icon' => 'fa fa-cogs', 'url'=>'#', 'items'=>[
            ['label' =>Yii::t('app','Serwis')." ".\common\models\GearServiceStatut::getLabels(), 'icon'=>'fa fa-cogs', 'url'=>['/gear-service/index'], 'visible'=>$user->can('gearService')],
            ['label' =>Yii::t('app','Statusy serwisów'), 'icon'=>'fa fa-cogs', 'url'=>['/gear-service-statut/index'], 'visible'=>$user->can('gearServiceStatut')],
            ],
            'visible'=>$user->can('gearService'),
        ],       
        ['label' =>Yii::t('app','Użytkownicy'), 'icon'=>'fa fa-users', 'url'=>'#', 'items'=>[
            ['label' =>Yii::t('app','Użytkownicy'), 'icon'=>'fa fa-users', 'url'=>['/user/index'], 'visible'=>$user->can('usersUsers')],
            ['label' =>Yii::t('app','Zespoły'), 'icon'=>'fa fa-users', 'url'=>['/team/index'], 'visible'=>$user->can('usersUsers')],
            ['label' =>Yii::t('app','Umiejętności'), 'icon'=>'fa fa-cogs', 'url'=>['/skill/index'], 'visible'=>$user->can('usersSkills')],
            ['label' =>Yii::t('app','Rozliczenia'), 'icon'=>'fa fa-money', 'url'=>['/settlement/user/index'], 'visible'=>$user->can('usersPayments')],
            ['label' =>Yii::t('app','Grupy prowizyjne'), 'icon'=>'fa fa-money', 'url'=>['/provision-group/index'], 'visible'=>$user->can('usersPayments')],
            ['label' =>Yii::t('app','Stawki pracowników'), 'icon'=>'fa fa-money', 'url'=>['/salary/index'], 'visible'=>$user->can('usersPayments')],
            ['label' =>Yii::t('app','Historia aktualności'), 'icon'=>'fa fa-clock-o', 'url'=>['/note/index'], 'visible'=>$user->can('usersUsers')],
            ],
            'visible'=>$user->can('menuUsers'),
        ],
        ['label' =>Yii::t('app','Flota'), 'icon'=>'fa fa-truck', 'url'=>'#', 'items'=>[
                ['label' =>Yii::t('app','Pojazdy'), 'icon'=>'fa fa-car', 'url'=>['/vehicle'], 'visible'=>$user->can('fleetVehicles')],
                                ['label' =>Yii::t('app','Modele pojazdów'), 'icon'=>'fa fa-car', 'url'=>['/vehicle-model'], 'visible'=>$user->can('fleetVehicles')],

                ['label' =>(Yii::$app->params['companyID']=="imagination")?Yii::t('app','Kilometrówka') : Yii::t('app','Przejazdy'), 'icon'=>'fa fa-car', 'url'=>['/ride'], 'visible'=>$user->can('fleetVehicles')],
                ['label' =>Yii::t('app','Załączniki'), 'icon'=>'fa fa-paperclip', 'url'=>['/vehicle-attachment'], 'visible'=>$user->can('fleetAttachments')],
            ],
            'visible'=>$user->can('menuFleet'),
        ],
        [
            'label'=>Yii::t('app', 'Ustawienia'), 'url'=>'#', 'icon'=>'fa fa-wrench',
            'visible'=>$user->can('menuSettings'), 'items'=>[
            [
            'label'=>Yii::t('app', 'Ustawienia'), 'url'=>['/setting/menu-settings'], 'icon'=>'fa fa-wrench',
            'visible'=>$user->can('menuSettings'),],
            ['label' => Yii::t('app', 'Dodatkowe pola w wydarzeniu'), 'url'=>['/event-field-setting/index'], 'visible'=>$user->can('menuSettings')],
            ['label' => Yii::t('app', 'Rodzaje wydarzeń'), 'url'=>['/event-type/index'], 'visible'=>$user->can('settingsEventTypes')],
                        ['label' => Yii::t('app', 'Typy wydarzeń'), 'url'=>['/event-model/index'], 'visible'=>$user->can('settingsEventModels')],
            ['label' => Yii::t('app', 'Statusy wydarzeń'), 'url'=>['/event-statut/index', 'type'=>1], 'visible'=>$user->can('settingsStatuts')],
                        ['label' => Yii::t('app', 'Dodatkowe statusy wydarzeń'), 'url'=>['/event-additional-statut/index'], 'visible'=>$user->can('menuSettings')],

            ['label' => Yii::t('app', 'Statusy wypożyczeń'), 'url'=>['/event-statut/index', 'type'=>2], 'visible'=>$user->can('settingsStatuts')],
            ['label' => Yii::t('app', 'Statusy sprzętu'), 'url'=>['/event-gear-status/index'], 'visible'=>$user->can('settingsStatuts')],
            ['label'=>Yii::t('app', 'Statusy ofert'), 'icon'=>'fa fa-check', 'url'=>['/offer-statut', 'visible'=>$user->can('settingsStatuts')]],
            ['label' =>Yii::t('app','Schematy Ofert'), 'icon'=>'fa fa-shopping-cart', 'url'=>['/offer-draft'], 'visible'=>$user->can('settingsOfferDrafts')],
            ['label' => Yii::t('app', 'Foldery załączników w sprzęcie'), 'url'=>['/gear-attachment-type/index'], 'visible'=>$user->can('menuSettings')],
            ['label' => Yii::t('app', 'Domyślne harmonogramy'), 'url'=>['/schedule/all'], 'visible'=>$user->can('menuSettings')],
            ['label' => Yii::t('app', 'Schematy grup sprzętowych'), 'url'=>['/packlist-schema/index'], 'visible'=>$user->can('menuSettings')],
            ['label' => Yii::t('app', 'Statusy wynajmu powierzchni'), 'url'=>['/hall-group-statut/index'], 'visible'=>$user->can('settingsStatuts')],
            ]
        ],
                ['label' =>Yii::t('app', 'Oferty'), 'icon'=>'fa fa-shopping-cart', 'url'=>['/offer'], 'visible'=>$user->can('menuOffers')
                

            ],
        ['label' =>Yii::t('app','Wypożyczenia i konflikty').'<span class="label label-danger pull-right" style="margin-left:10px">'.$conflictsCount.'</span>', 'icon'=>'fa fa-shopping-cart', 'url'=>['/order/list'],
            'visible'=>$user->can('menuOrders'),
        ],
        ['label' =>Yii::t('app', 'Statystyki'), 'icon'=>'fa fa-line-chart', 'url'=>'#', 'visible'=>$user->can('menuStats'), 'items'=>[
                ['label' =>Yii::t('app','Sprzęt'), 'icon'=>'fa fa-plug', 'url'=>['/stat/chart1'], 'visible'=>true],
                ['label' =>Yii::t('app','Sprzęt zewnętrzny'), 'icon'=>'fa fa-plug', 'url'=>['/stat/chart2'], 'visible'=>true],
                ['label' =>Yii::t('app','Pojazdy'), 'icon'=>'fa fa-truck', 'url'=>['/stat/chart3'], 'visible'=>true],
                ['label' =>Yii::t('app','Klienci'), 'icon'=>'fa fa-user', 'url'=>['/stat/chart4'], 'visible'=>true],
            ],
        ],
        ['label' =>Yii::t('app', 'Rozliczenia'), 'icon'=>'fa fa-trophy', 'url'=>['/settlement/user/show'], 'visible'=>$user->can('menuSettlement')],
        ['label' =>Yii::t('app','Finanse'), 'icon'=>'fa fa-money', 'url'=>'#', 'items'=>[
                ['label' =>Yii::t('app','Faktury przychodowe'), 'icon'=>'fa fa-arrow-left', 'url'=>['/finances/invoice/index'], 'visible'=>$user->can('menuInvoicesInvoice')],
                ['label' =>Yii::t('app','Faktury kosztowe'), 'icon'=>'fa fa-arrow-right', 'url'=>['/finances/expense/index'],'visible'=>$user->can('menuInvoicesExpense')],
                ['label' =>Yii::t('app','Koszty dodatkowe'), 'icon'=>'fa fa-arrow-right', 'url'=>['/event-expense/index'],'visible'=>$user->can('menuInvoicesExpense')],
                ['label' =>Yii::t('app','Serie'), 'icon'=>'fa fa-wrench', 'url'=>['/finances/invoice-serie/index'], 'visible'=>$user->can('financesInvoiceSeries')],
                ['label' =>Yii::t('app','Koszty miesięczne'), 'icon'=>'fa fa-arrow-left', 'url'=>['/month-cost/index'], 'visible'=>$user->can('menuInvoicesMonthCost')],
                ['label' =>Yii::t('app','Inwestycje'), 'icon'=>'fa fa-arrow-left', 'url'=>['/investition/index'], 'visible'=>$user->can('menuInvoicesInvestition')],
                ['label' =>Yii::t('app','Analizy'), 'icon'=>'fa fa-arrow-left', 'url'=>['/stat/finance'], 'visible'=>$user->can('menuInvoicesAnalize')],
                ['label' =>Yii::t('app','Wydarzenia - raport'), 'icon'=>'fa fa-star', 'url'=>['/event-report'], 'visible'=>(($user->can('menuInvoices'))&&(Yii::$app->params['companyID']=="wizja"))],
                ['label' =>Yii::t('app','Zakupy'), 'icon'=>'fa fa-arrow-right', 'url'=>['/purchase'], 'visible'=>$user->can('menuInvoicesPurchase')],
                ['label' =>Yii::t('app','Typy zakupów'), 'icon'=>'fa fa-arrow-right', 'url'=>['/purchase-type'], 'visible'=>$user->can('menuInvoicesPurchase')],
                ['label' =>Yii::t('app','Typy wydatków'), 'icon'=>'fa fa-arrow-right', 'url'=>['/expense-type'], 'visible'=>$user->can('menuInvoicesExpense')],
            ],
            'visible'=>$user->can('menuInvoices'),
        ],
        ['label' =>Yii::t('app','Toolbox'), 'icon'=>'fa fa-wrench', 'url'=>'#', 'items'=>[
                ['label' =>Yii::t('app','Blend Calculator'), 'icon'=>'fa fa-desktop', 'url'=>['/calculator'], 'visible'=>$user->can('menuToolboxBlend')],
                ['label' =>Yii::t('app','ShowTime'), 'icon'=>'fa fa-desktop', 'url'=>['/timer'], 'visible'=>$user->can('menuToolboxShowTime')],
                ['label' =>Yii::t('app','SpacePlanner'), 'icon'=>'fa fa-desktop', 'url'=>['/spaceplanner'], 'visible'=>$user->can('menuToolboxSpacePlanner')],
                ['label' =>Yii::t('app','Mask Creator'), 'icon'=>'fa fa-desktop', 'url'=>['/site/mask-creator'], 'visible'=>$user->can('menuToolbox')]
            ],
            'visible'=>$user->can('menuToolbox'),
        ],
        ['label' => Yii::t('app', 'Instrukcja') , 'icon'=>'fa fa-question', 'url'=>['/site/first-use'], 'visible'=>true],


    ];       

}
return $menuItems;
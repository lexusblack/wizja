<?php
namespace backend\modules\permission\models;

use common\helpers\ArrayHelper;
use common\models\User;
use Yii;
use yii\helpers\VarDumper;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\web\NotFoundHttpException;

class PermissionForm extends \yii\base\Model
{
    public $users;
    public $assignedUsers;
    public $permissions;

    /* @var Role */
    public $role;
    /* @var \yii\rbac\DbManager */
    public $manager;


    public function rules()
    {
        $rules = [
            [['users', 'role', 'permissions', 'assignedUsers'], 'safe'],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function init()
    {
        parent::init();
        $this->manager = \Yii::$app->authManager;
    }

    public function getUserItems()
    {
        $items = [];

        $users = User::getList();
        $ids = $this->manager->getUserIdsByRole($this->role->name);
        $users = array_diff_key($users, array_flip($ids));

        foreach ($users as $id=>$name)
        {
            $items[$id] = [
                'content' => $name,
            ];
        }
        return $items;
    }

    public function getAssignedItems()
    {
        $items = [];
        $ids = $this->manager->getUserIdsByRole($this->role->name);
        $users = User::find()
            ->where(['id'=>$ids])
            ->andWhere(['active'=>1])
            ->orderBy(['last_name'=>SORT_ASC, 'first_name'=>SORT_ASC])
            ->all();
        foreach ($users as $user)
        {
            $items[$user->id] = [
                'content' => $user->getDisplayLabel(),
            ];
        }

        return $items;
    }

    public function save()
    {

        $this->_savePermissions();

        $notAssignedIds = explode(',', $this->users);
        $assignedIds = explode(',', $this->assignedUsers);

        foreach ($notAssignedIds as $userId)
        {
            $this->manager->revoke($this->role, $userId);
        }

        $assigned = $this->manager->getUserIdsByRole($this->role->name);
        foreach ($assignedIds as $userId)
        {
            if (in_array($userId, $assigned) == false)
            {
                $this->manager->assign($this->role, $userId);
            }
        }

        return true;
    }

    protected function _savePermissions()
    {
        $manager = $this->manager;
        $permissions = $this->manager->getPermissionsByRole($this->role->name);
        $assigned = ArrayHelper::map($permissions, 'name', 'name');
        foreach ($this->permissions as $name => $assign)
        {
            $permission = $manager->getPermission($name);
            if ($assign == 0)
            {
                $manager->removeChild($this->role, $permission);
            }
            else
            {
                if (in_array($permission->name, $assigned) == false)
                {
                    $manager->addChild($this->role, $permission);
                }
            }
        }
    }

    public function setRole($roleName)
    {
        $this->role = $this->manager->getRole($roleName);
        if ($this->role === null)
        {
            throw new NotFoundHttpException(Yii::t('app', 'Rola!'));
        }

        $permissions = $this->manager->getPermissionsByRole($roleName);
        foreach ($permissions as $p)
        {
            $this->permissions[$p->name] = 1;
        }
    }

    public function getPermissionList()
    {
        $labels = [
            Yii::t('app', 'Zakładki eventu'),
            Yii::t('app', 'Zakładki główne')
        ];
        $descriptions = [
            [
                Yii::t('app', 'sprzęt'),
                Yii::t('app', 'sprzęt zewnętrzny'),
                Yii::t('app', 'załączniki'),
                Yii::t('app', 'ekipa'),
                Yii::t('app', 'flota'),
                Yii::t('app', 'godziny pracy'),
                Yii::t('app', 'powiadomienia'),
                Yii::t('app', 'finanse'),
                Yii::t('app',   'oferty')
            ],
            [
                Yii::t('app', 'Wydarzenia (w formie listy)'),
                Yii::t('app', 'Klienci'),
                Yii::t('app', 'Miejsca'),
                Yii::t('app', 'Sprzęt'),
                Yii::t('app', 'Użytkownicy'),
                Yii::t('app', 'Flota'),
                Yii::t('app', 'Ustawienia'),
                Yii::t('app', 'Oferty'),
                Yii::t('app', 'Skany Faktur'),
                Yii::t('app', 'Dodatkowe koszty'),
                Yii::t('app', 'Finanse'),
                Yii::t('app', 'Rozliczenia'),
                Yii::t('app', 'Toolbox')

            ],
        ];

        $l = [
          [
                'eventTabGear',
                'eventTabOuterGear',
                'eventTabAttachment',
                'eventTabCrew',
                'eventTabVehicle',
                'eventTabWorkingTime',
                'eventTabNotification',
                'eventTabFinances',
                'eventTabOffer',
                [
                    'description'=>Yii::t('app', 'opis'),
                    'base'=>'eventTabDescription',
                    'actions'=>[false, 'Update', false, false],
                ]

            ],
            [
                'event',
                'customer',
                'location',
                'gear',
                'user',
                'vehicle',
                'settings',
                'offer',
                'eventInvoice',
                'eventExpense',
                'finances',
                'settlement',
                'toolbox'
            ],
        ];
        $list = [];
        $actions = ['View', 'Update', 'Create', 'Delete'];
        $auth = $this->manager;
        foreach ($labels as $index => $label)
        {
            $list[$label] = [];


            foreach ($l[$index] as $i =>$item)
            {
                if (is_array($item) == false)
                {
                    $desc = $descriptions[$index];
                    $d = $desc[$i];
                    foreach ($actions as $action)
                    {
                        $pName = $item.$action;
                        $permission = $auth->getPermission($pName);
                        if ($permission==null)
                        {
                            $permission = new Permission();
                            $permission->name = $pName;
                            $permission->description = $d.' '.$action;
                            $auth->add($permission);
                        }
                        $list[$label][$d][] = $item.$action;
                    }
                }
                else
                {

                    $d = $item['description'];

                    foreach ($item['actions'] as $action)
                    {
                        $pName = $item['base'].$action;
                        $permission = $auth->getPermission($pName);
                        if ($permission==null)
                        {
                            $permission = new Permission();
                            $permission->name = $pName;
                            $permission->description = $d.' '.$action;
                            $auth->add($permission);
                        }
                        $list[$label][$d][] = $action==false ? false : $item['base'].$action;
                    }
                }

            }
        }

//        VarDumper::dump($list, 10, true); die;
        return $list;
    }

    public function getOtherPermissionList()
    {
        $auth = $this->manager;
        $list = [];
        $permissions = [
            'vacationAccept',
            'outgoingGearAddUnplanned',
        ];
        foreach ($permissions as $name)
        {
            $permission = $auth->getPermission($name);
            $list[$permission->name] = $permission->description;
        }

        return $list;
    }

    public function getGroupedPermissionList()
    {
        //TODO: Zmienić $permissions['permissions'] na ['label'=>'', ['name']=>'']
        //TODO: i18n
        $auth = $this->manager;
        $list = [];
        $permissions = [
            //zakładki
            [
                'group'=>Yii::t('app', 'Wydarzenie'),
                'permissions'=>['eventTabFinancesInvoiceAdd']
            ],
            [
                'group'=>Yii::t('app', 'Przyciski'),
                'permissions'=>[
//                    'eventButtonCreate',
                    'eventButtonUpdate',
                    'eventButtonDelete']
            ],
            [
                'group'=>Yii::t('app', 'Sprzęt'),
                'permissions'=>['eventGearManage', 'eventGearRemove', 'eventGearManageWorkingTime', 'eventGearSuggestion']
            ],
            [
                'group'=>Yii::t('app', 'Sprzęt zewnętrzny'),
                'permissions'=>['eventOuterGearManage', 'eventOuterGearRemove', 'eventOuterGearManageWorkingTime', 'eventOuterGearExpenseAdd'],
            ],
            [
                'group'=>Yii::t('app', 'Załączniki'),
                'permissions'=>['eventAttachmentDownload',],
            ],
            [
                'group'=>Yii::t('app', 'Ekipa'),
                'permissions'=>['eventCrewManageRole', 'eventCrewWorkingTime'],
            ],
            [
                'group'=>Yii::t('app', 'Oferty'),
                'permissions'=>['offerImport', 'offerGearAdd'],
            ],
            [
                'group'=>Yii::t('app', 'Powiadomienia'),
                'permissions'=>['notificationMail','notificationSms', 'notificationPush'],
            ],
            [
                'group'=>Yii::t('app', 'Godziny pracy'),
                'permissions'=>['workingTimeViewSelf'],

            ],
            [
                'group'=>Yii::t('app', 'Finanse'),
                'permissions'=>['eventTabFinancesProjectValueView', 'eventTabFinancesExpensesView', 'eventTabFinancesExpensesWorkingTimeView',
                    'eventTabFinancesProfitView', 'eventTabFinancesNotesView', 'eventTabFinancesProvisionView', 'eventTabFinancesStatusView',
                    'eventTabFinancesInvoiceNumberView', 'eventTabFinancesInvoiceFileView'],
            ],
            [
                'group'=>Yii::t('app', 'Koszty dodatkowe'),
                'permissions'=>['eventExpenseImportOuter']
            ],
            [
                'group'=>Yii::t('app', 'Flota'),
                'permissions'=>['vehicleFinanceData', 'vehicleOtherData']
            ],
            [
                'group'=>Yii::t('app', 'Magazyn'),
                'permissions' => [
                    Yii::t('app', 'Dodawanie modeli')=>'warehouseGearCreate',
                    Yii::t('app', 'Edycja modeli')=>'warehouseGearUpdate',
                    Yii::t('app', 'Kasowanie modeli')=>'warehouseGearDelete',
                    Yii::t('app', 'Podgląd modeli')=>'warehouseGearView',
                    Yii::t('app', 'Dodawanie case')=>'warehouseCaseCreate',
                    Yii::t('app', 'Dodawanie zestawów')=>'warehousePackageCreate',
                    Yii::t('app', 'Widok ceny modelu')=>'warehouseGearPriceView',
                    '-',
                    Yii::t('app', 'Podgląd case')=>'warehouseCaseView',
                    Yii::t('app', 'Edycja case')=>'warehouseCaseUpdate',
                    Yii::t('app', 'Kasowanie case')=>'warehouseCaseDelete',
                    Yii::t('app', 'Dodawanie do case')=>'warehouseAddToCase',
                    Yii::t('app', 'Dodawanie egzemplarzy')=>'warehouseGearItemCreate',
                    Yii::t('app', 'Edycja egzemplarzy')=>'warehouseGearItemUpdate',
                    Yii::t('app', 'Kasowanie egzemplarzy')=>'warehouseGearItemDelete',
                    '-',
                    Yii::t('app', 'Podgląd egzemplarzy')=>'warehouseGearItemView',
                    Yii::t('app', 'Historia egzemplarzy')=>'warehouseGearItemHistory',
                    Yii::t('app', 'Dane finansowe egzemplarzy')=>'warehouseGearItemFinanceData',
                    Yii::t('app', 'Inne dane egzemplarzy')=>'warehouseGearItemOtherData',
                    Yii::t('app', 'Cena egzemplarzy')=>'warehouseGearItemPriceView',
                    Yii::t('app', 'Serwis egzemplarzy')=>'warehouseGearItemService',
                ]
            ],
            [
                'group'=>Yii::t('app', 'Chat'),
                'permissions' => [
                    Yii::t('app', 'Tworzenie chatu')=>'chatCreate',
                    Yii::t('app', 'Edycja chatu')=>'chatUpdate',
                ]
            ],
            [
                'group'=>Yii::t('app', 'Magazyn zewnętrzny'),
                'permissions' => [
                    Yii::t('app', 'Dodawanie modeli')=>'outerWarehouseCreate',
                    Yii::t('app', 'Edycja modeli')=>'outerWarehouseUpdate',
                    Yii::t('app', 'Kasowanie modeli')=>'outerWarehouseDelete',
                    Yii::t('app', 'Podgląd modeli')=>'outerWarehouseView',
                    Yii::t('app', 'Importowanie danych podczas dodawania')=>'outerWarehouseCreateImport',
                    '-',
                    Yii::t('app', 'Dane finansowe wypożyczenie')=>'outerWarehouseFinanceRent',
                    Yii::t('app', 'Dane finansowe cena sprzedaży')=>'outerWarehouseFinanceSellPrice',
                    Yii::t('app', 'Dane finansowe zysk')=>'outerWarehouseFinanceProfit',
                    Yii::t('app', 'Dane finansowe')=>'outerWarehouseFinanceDataView',
                    Yii::t('app', 'Inne dane')=>'outerWarehouseOtherDataView',
                ]
            ],
            [
                'group'=>Yii::t('app', 'Strona domowa'),
                'permissions' => [
                    Yii::t('app', 'Dzisiaj')=>'homeTodayBlock',
                    Yii::t('app', 'Najbliższe wydarzenia')=>'homeRecentEventsBlock',
                    Yii::t('app', 'Najbliższe wydarzenia Twojego działu') => 'homeRecentDepartmentEventsBlock',
                    Yii::t('app', 'Najbliższe wydarzenia Twojej firmy') => 'homeRecentCompanyEventsBlock',
                    Yii::t('app', 'Powiadomienia') => 'homeMessagesBlock',
                    Yii::t('app', 'Taski') => 'homeTasksBlock',
                    Yii::t('app', 'Forum') => 'homeForumBlock',
                    Yii::t('app', 'Checklista') => 'homeChecklistBlock',
                    Yii::t('app', 'Wiadomości') => 'homeNewsBlock',
                    '-',
                    Yii::t('app', 'Zmiana kolejności i skalowanie okien')=>'homeBlocksSizeChange',
                    Yii::t('app', 'Zapisywanie konfiguracji okien')=>'homeBlocksLayoutSave',
                    Yii::t('app', 'Zmiana tła programu')=>'homeBackgroundChange',

                ],
            ],
            [
                'group'=>Yii::t('app', 'Kalendarz filtry'),
                'permissions' => [
                    Yii::t('app', 'Typy')=>'calendarFilterType',
                    Yii::t('app', 'Działy')=>'calendarFilterDepartment',
                    Yii::t('app', 'PM')=>'calendarFilterPM',
                    Yii::t('app', 'Klienci')=>'calendarFilterCustomer',
                    Yii::t('app', 'Kontakty')=>'calendarFilterContact',
                    Yii::t('app', 'Pracownicy')=>'calendarFilterUser',
                    Yii::t('app', 'Status projektu')=>'calendarFilterProjectStatus',
                    Yii::t('app', 'Status wypożyczenia')=>'calendarFilterRentStatus',
                ],
            ],
            [
                'group'=>Yii::t('app', 'Kalendarz'),
                'permissions' => [
                    Yii::t('app', 'Wyszukiwanie')=>'calendarSearch',
                    Yii::t('app', 'Spotkanie')=>'calendarTypeMeeting',
                    Yii::t('app', 'Wydarzenie')=>'calendarTypeEvent',
                    Yii::t('app', 'Wypożyczenie')=>'calendarTypeRent',
                    Yii::t('app', 'Urlop')=>'calendarTypeVacation',
                    '-',
                    Yii::t('app', 'Moje wydarzenia')=>'calendarUserEvents',
                    Yii::t('app', 'Wydarzenia działu')=>'calendarDepartmentEvents',
                    Yii::t('app', 'Wszystkie wydarzenia')=>'calendarAllEvents',
                    Yii::t('app', 'Wydarzenia firmy')=>'calendarCustomerEvents',
                ],
            ],
            [
                'group'=>Yii::t('app', 'Menu'),
                'permissions' => [
                    Yii::t('app', 'Kalendarz')=>'menuCalendar',
                    '-',
                    Yii::t('app', 'Planboard')=>'menuPlanboard',
                    '-',
                    Yii::t('app', 'Wydarzenia')=>'menuEvents',
                    Yii::t('app', 'Wydarzenie')=>'menuEventsEvent',
                    Yii::t('app', 'Spotkanie')=>'menuEventsMeeting',
                    Yii::t('app', 'Wydarzenie prywatne')=>'menuEventsPrivate',
                    Yii::t('app', 'Wypożyczenie')=>'menuEventsRent',
                    Yii::t('app', 'Urlop')=>'menuEventsVacation',
                    '-',
                    Yii::t('app', 'Klienci')=>'menuCustomers',
                    Yii::t('app', 'Klient')=>'menuCustomersCustomer',
                    Yii::t('app', 'Rabaty')=>'menuCustomersDiscount',
                    Yii::t('app', 'Kontakt')=>'menuCustomersContact',
                    '-',
                    Yii::t('app', 'Miejsca')=>'menuLocations',
                    Yii::t('app', 'Miejsce')=>'menuLocationsLocation',
                    Yii::t('app', 'Załączniki')=>'menuLocationsAttachment',
                    '-',
                    Yii::t('app', 'Sprzęt')=>'menuGears',
                    Yii::t('app', 'Magazyn')=>'menuGearsWarehouse',
                    Yii::t('app', 'Magazyn zewnętrzny')=>'menuGearsOuterWarehouse',
                    Yii::t('app', 'Wydanie z magazynu')=>'menuGearsOutcome',
                    Yii::t('app', 'Przyjęcie do magazynu')=>'menuGearsIncome',
                    Yii::t('app', 'Model')=>'menuGearsGear',
                    Yii::t('app', 'Sprzęty')=>'menuGearsGearItem',
                    Yii::t('app', 'Zestawy')=>'menuGearsGearGroup',
                    Yii::t('app', 'Kategorie')=>'menuGearsCategory',
                    Yii::t('app', 'Załączniki modeli')=>'menuGearsGearAttachment',
                    Yii::t('app', 'Serwis')=>'menuGearsService',
                    Yii::t('app', 'Baza sprzętu')=>'menuGearsBase',
                    Yii::t('app', 'Producenci sprzętu')=>'menuGearsCompany',
                    '-',
                    Yii::t('app', 'Użytkownicy')=>'menuUsers',
                    Yii::t('app', 'Użytkownik')=>'menuUsersUser',
                    Yii::t('app', 'Umiejętności')=>'menuUsersSkill',
                    Yii::t('app', 'Rozliczenie')=>'menuUsersSettlement',
                    '-',
                    Yii::t('app', 'Flota')=>'menuVehicles',
                    Yii::t('app', 'Pojazdy')=>'menuVehiclesVehicle',
                    Yii::t('app', 'Załącznik')=>'menuVehiclesAttachment',
                    '-',
                    Yii::t('app', 'Ustawienia')=>'menuSettings',
                    '-',
                    Yii::t('app', 'Oferty')=>'menuOffers',
                    '-',
                    Yii::t('app', 'Taski')=>'menuTasks',
                    '-',
                    Yii::t('app', 'Rozliczenia')=>'menuSettlements',
                    '-',
                    Yii::t('app', 'Faktury')=>'menuFinances',
                    Yii::t('app', 'Przychody')=>'menuFinancesInvoice',
                    Yii::t('app', 'Wydatki')=>'menuFinancesExpense',
                    Yii::t('app', 'Serie')=>'menuFinancesSerie',
                    Yii::t('app', 'Toolbox')=>'menuToolbox'
                ],
            ],



        ];
        foreach ($permissions as $item)
        {
            $i = 0;
            $group = $item['group'];
            $baseGroup = $group;

            foreach ($item['permissions'] as $k => $pName)
            {
                if ($pName=='-')
                {
                    $group = '_'.$baseGroup.'_'.$i;
                    $i++;
                    continue;
                }
                $permission = $auth->getPermission($pName);
                if ($permission==null)
                {
                    $permission = new Permission();
                    $permission->name = $pName;

                    if (is_int($k))
                    {
                        $d = Yii::t('app', '_Opis_');
                    }
                    else
                    {
                        $d = $k;
                    }

                    $permission->description = $d;
                    $auth->add($permission);
                }
                $list[$group][$permission->name] = $permission->description;

            }
        }
        return $list;
    }
}
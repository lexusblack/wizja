<?php


namespace backend\modules\permission\models;


use Yii;
use yii\rbac\DbManager;

class PermissionTreeStructure {

    private $role;
    private $roots = [];
    private $manager;
    private $permissions;
    private $permissionId = 1;


    public function __construct($role) {
        $this->manager = \Yii::$app->authManager;
        $permissions = $this->manager->getPermissionsByRole($role->name, false);
        $this->permissions = $permissions;

        // **** Chat **** //
        $chatPermissions = $this->createPermission(Yii::t('app', 'Chat'), 'menuChat', false, false);
        $chatCreate = $this->createPermission(Yii::t('app', 'Tworzenie'), 'chatCreate', false, false);
        $chatEdit = $this->createPermission(Yii::t('app', 'Edycja'), 'chatUpdate', false, false);
        $chatDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'chatDelete', false, false);

        $chatPermissions->next = [$chatCreate, $chatEdit, $chatDelete];


        // **** Kokpit **** //
        $cockpitPermissions = $this->createPermission(Yii::t('app', 'Kokpit'), 'menuCockpit', false, false);
        $cockpitToday = $this->createPermission(Yii::t('app', 'Dzisiaj'), 'cockpitToday', true, false);
        $cockpitEvents = $this->createPermission(Yii::t('app', 'Najbliższe wydarzenia'), 'cockpitEvents', true, false);
        $cockpitDepartmentEvents = $this->createPermission(Yii::t('app', 'Najbliższe wydarzenia Twojego działu'), 'cockpitDepartmentEvents', false, false);
        $cockpitNotifications = $this->createPermission(Yii::t('app', 'Powiadomienia'), 'cockpitNotifications', false, false);
        $cockpitTasks = $this->createPermission(Yii::t('app', 'Zadania'), 'cockpitTasks', false, false);
        $cockpitNews = $this->createPermission(Yii::t('app', 'Wiadomości'), 'cockpitNews', false, false);
        $cockpitChecklist = $this->createPermission(Yii::t('app', 'Checklist'), 'cockpitChecklist', false, false);
        $cockpitStatus = $this->createPermission(Yii::t('app', 'Status'), 'cockpitStatus', false, true);

        $cockpitPermissions->next = [$cockpitToday, $cockpitEvents, $cockpitDepartmentEvents, $cockpitNotifications, $cockpitTasks, $cockpitNews, $cockpitChecklist, $cockpitStatus];


        // **** Kalendarz **** //
        $calendarPermissions = $this->createPermission(Yii::t('app', 'Kalendarz'), 'menuCalendar', true, false);
        $calendarEvents = $this->createPermission(Yii::t('app', 'Wydarzenia'), 'calendarEvents', false, false);
        $calendarRents = $this->createPermission(Yii::t('app', 'Wypożyczenia'), 'calendarRents', false, false);
        $calendarMeetings = $this->createPermission(Yii::t('app', 'Spotkania'), 'calendarMeetings', false, false);
        $calendarVacations = $this->createPermission(Yii::t('app', 'Urlopy'), 'calendarVacations', false, false);
        $eventsPrivate = $this->createPermission(Yii::t('app', 'Wydarzenia prywatne'), 'eventsMeetingsPrivate', false, false);
        $calendarFilters = $this->createPermission(Yii::t('app', 'Filtry'), 'calendarFilters', false, false);

        $eventsEventAdd = $this->createPermission(Yii::t('app', 'Dodawanie'), 'eventsEventAdd', false, true);
        //$calendarEvents->next = [$eventsEventAdd];
        $eventsRentsAdd = $this->createPermission(Yii::t('app', 'Dodawanie'), 'eventRentsAdd', false, true);
        //$calendarRents->next = [$eventsRentsAdd];
        $eventsMeetingsAdd = $this->createPermission(Yii::t('app', 'Dodawanie'), 'eventMeetingAdd', false, true);
        //$calendarMeetings->next = [$eventsMeetingsAdd];
        $eventsVacationsAdd = $this->createPermission(Yii::t('app', 'Dodawanie'), 'eventVacationsAdd', false, false);
        //$calendarVacations->next = [$eventsVacationsAdd];

        $calendarFiltersType = $this->createPermission(Yii::t('app', 'Typ'), 'calendarFiltersType', false, false);
        $calendarFiltersDepartment = $this->createPermission(Yii::t('app', 'Dział'), 'calendarFiltersDepartment', false, false);
        $calendarFiltersPm = $this->createPermission(Yii::t('app', 'PM'), 'calendarFiltersPm', false, false);
        $calendarFiltersClients = $this->createPermission(Yii::t('app', 'Kontrahenci'), 'calendarFiltersClients', false, false);
        $calendarFiltersContacts = $this->createPermission(Yii::t('app', 'Kontakty'), 'calendarFiltersContacts', false, false);
        $calendarFiltersUsers = $this->createPermission(Yii::t('app', 'Pracownicy'), 'calendarFiltersUsers', false, false);
        $calendarFiltersProjectStatus = $this->createPermission(Yii::t('app', 'Status projektu'), 'calendarFiltersProjectStatus', false, false);
        $calendarFiltersRentStatus = $this->createPermission(Yii::t('app', 'Status wypożyczenia'), 'calendarFiltersRentStatus', false, false);

        $calendarFilters->next = [$calendarFiltersType, $calendarFiltersDepartment, $calendarFiltersPm, $calendarFiltersClients, $calendarFiltersContacts, $calendarFiltersUsers, $calendarFiltersProjectStatus, $calendarFiltersRentStatus];
        $calendarNoTitles = $this->createPermission(Yii::t('app', 'Szczegóły wydarzeń'), 'calendarDetails', false, false);
        $calendarEventName = $this->createPermission(Yii::t('app', 'Ukryj nazwę wydarzenia'), 'calendarEventName', false, false);
        $calendarEventID = $this->createPermission(Yii::t('app', 'Ukryj ID wydarzenia'), 'calendarEventID', false, false);
        $calendarEventStatut = $this->createPermission(Yii::t('app', 'Ukryj status'), 'calendarEventStatut', false, false);
        $calendarEventPM = $this->createPermission(Yii::t('app', 'Ukryj informacje o PM'), 'calendarEventPM', false, false);
        $calendarDetailsBox = $this->createPermission(Yii::t('app', 'Ukryj okienko ze szczegółami'), 'calendarDetailsBox', false, false);
        $calendarNoTitles->next = [$calendarEventName, $calendarEventID, $calendarEventStatut, $calendarEventPM, $calendarDetailsBox];

       $calendarPermissions->next = [$calendarEvents, $calendarRents, $calendarMeetings, $calendarVacations, $eventsPrivate, $calendarFilters, $calendarNoTitles];
        //$calendarPermissions->next = [$calendarFilters];

        // **** Planboard **** //

        $planboardPermissions = $this->createPermission(Yii::t('app', 'PlanTimeline'), 'menuPlanboard', false, false);


        $planboardEvents = $this->createPermission(Yii::t('app', 'Wydarzenia'), 'planboardEvents', false, true);


        $planboardEventsDeleteUsers = $this->createPermission(Yii::t('app', 'Usuwanie użytkowników'), 'planboardEventsDeleteUsers', false, false);
        $planboardEventsDeleteFleet = $this->createPermission(Yii::t('app', 'Usuwanie floty'), 'planboardEventsDeleteFleet', false, false);
        $planboardEventsEditUsers = $this->createPermission(Yii::t('app', 'Edytowanie użytkowników'), 'planboardEventsEditUsers', false, false);
        $planboardEventsEditFleet = $this->createPermission(Yii::t('app', 'Edytowanie floty'), 'planboardEventsEditFleet', false, false);
        $planboardEventsEditBreaks = $this->createPermission(Yii::t('app', 'Edytowanie przerw'), 'planboardEventsEditBreaks', false, false);
        $planboardEvents->next = [$planboardEventsDeleteUsers, $planboardEventsDeleteFleet, $planboardEventsEditUsers, $planboardEventsEditFleet, $planboardEventsEditBreaks];


        $planboardMenu = $this->createPermission(Yii::t('app', 'Menu Plan Timeline'), 'menuPlanboardMenu', false, true);

        $planboardMenuCrew = $this->createPermission(Yii::t('app', 'Plan Time Ekipa'), 'menuPlanboardMenuCrew', false, false);
        $planboardMenuFlota = $this->createPermission(Yii::t('app', 'Plan Time Flota'), 'menuPlanboardMenuFlota', false, false);
        $planboardMenu->next = [$planboardMenuCrew, $planboardMenuFlota];

        $planboardMenuCrewAdd = $this->createPermission(Yii::t('app', 'Dodaj'), 'menuPlanboardMenuCrewAdd', false, false);
        $planboardMenuCrew->next[] = $planboardMenuCrewAdd;
        $planboardMenuFlotaAdd = $this->createPermission(Yii::t('app', 'Dodaj'), 'menuPlanboardMenuFlotaAdd', false, false);
        $planboardMenuFlota->next[] = $planboardMenuFlotaAdd;

        $planboardPermissions->next[] = $planboardEvents;
        $planboardPermissions->next[] = $planboardMenu;


        // **** Eventy **** //
        $eventPermissions = $this->createPermission(Yii::t('app', 'Wydarzenia'), 'menuEvents', false, false);
        $eventsEvents = $this->createPermission(Yii::t('app', 'Wydarzenia'), 'eventsEvents', true, false);
        $eventsProjects = $this->createPermission(Yii::t('app', 'Projekty'), 'eventsProjects', false, false);

        $eventsEventDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'eventEventDelete', true, true);
        $warehouseInOut = $this->createPermission(Yii::t('app', 'Wydanie/przyjęcie z magazynu'), 'eventRentsMagazin', false, false);
        $eventsEventEditPencil = $this->createPermission(Yii::t('app', 'Edytowanie (ołówek)'), 'eventEventEditPencil', true, true);
        $eventsEventEditStatus = $this->createPermission(Yii::t('app', 'Edytowanie statusu'), 'eventsEventEditStatus', true, false);
        $eventsEventEditEye = $this->createPermission(Yii::t('app', 'Podgląd wydarzenia (oko)'), 'eventEventEditEye', true, false);
        $eventsEventBlockCost = $this->createPermission(Yii::t('app', 'Dodawanie kosztów mimo blokady'), 'eventEventBlockCost', false, true);
        $eventsEventBlockGear = $this->createPermission(Yii::t('app', 'Zarządzanie sprzętem mimo blokady'), 'eventEventBlockGear', false, true);
        $eventsEventBlockWorkingTimes = $this->createPermission(Yii::t('app', 'Zarządzanie czasami pracy mimo blokady'), 'eventEventBlockWorking', false, true);
        $eventsEventBlockEdit = $this->createPermission(Yii::t('app', 'Edytowanie wydarzenia mimo blokady'), 'eventEventBlockEvent', false, true);
        $eventsEventBlockStatus = $this->createPermission(Yii::t('app', 'Edytowanie statusu mimo blokady'), 'eventEventBlockStatus', false, true);
        $eventsEvents->next = [$eventsEventAdd, $eventsEventDelete, $warehouseInOut, $eventsEventEditPencil, $eventsEventEditStatus, $eventsEventEditEye, $eventsEventBlockCost, $eventsEventBlockGear, $eventsEventBlockWorkingTimes, $eventsEventBlockEdit, $eventsEventBlockStatus];


        $eventsEventEditEyeCalendar = $this->createPermission(Yii::t('app', 'Kalendarz'), 'eventEventEditEyeCalendar', false, false);
        $eventsEventEditEyeClientDetails = $this->createPermission(Yii::t('app', 'Informacje o kliencie i miejscu'), 'eventEventEditEyeClientDetails', false, false);
        $eventsEventEditEyeDescription = $this->createPermission(Yii::t('app', 'Opis'), 'eventEventEditEyeDescription', false, false);
        $eventsEventEditEyeDescriptionEdit = $this->createPermission(Yii::t('app', 'Edycja'), 'eventEventEditEyeDescriptionEdit', false, true);
        $eventsEventEditEyeDescription->next[] = $eventsEventEditEyeDescriptionEdit;

        $eventsEventEditEyeGear = $this->createPermission(Yii::t('app', 'Sprzęt'), 'eventEventEditEyeGear', false, false);
        $eventsEventEditEyeGearManage = $this->createPermission(Yii::t('app', 'Zarządzaj'), 'eventEventEditEyeGearManage', false, true);
        $eventsEventEditEyeGearConflict = $this->createPermission(Yii::t('app', 'Dodaj sprzęt mimo konfliktu'), 'eventEventEditEyeGearConflict', false, true);
        $eventsEventEditEyeGearEdit = $this->createPermission(Yii::t('app', 'Edytuj czas pracy'), 'eventEventEditEyeGearEdit', false, true);
        $eventsEventEditEyeGearDelete = $this->createPermission(Yii::t('app', 'Usuń'), 'eventEventEditEyeGearDelete', false, true);
        $eventsEventEditEyeGearBlock = $this->createPermission(Yii::t('app', 'Blokowanie/Odblokowywanie paklist'), 'eventsEventEditEyeGearBlock', false, true);

        $eventsEventEditEyeGear->next = [$eventsEventEditEyeGearManage, $eventsEventEditEyeGearConflict, $eventsEventEditEyeGearEdit, $eventsEventEditEyeGearDelete, $eventsEventEditEyeGearBlock];




        $eventsEventEditEyeOuterGear = $this->createPermission(Yii::t('app', 'Sprzęt zewnętrzny/Usługi'), 'eventEventEditEyeOuterGear', false, false);
        $eventsEventEditEyeOuterGearManage = $this->createPermission(Yii::t('app', 'Zarządzaj'), 'eventEventEditEyeOuterGearManage', false, true);
        $eventsEventEditEyeOuterGearAdd = $this->createPermission(Yii::t('app', 'Edytuj czas pracy'), 'eventEventEditEyeOuterGearEdit', false, true);
        $eventsEventEditEyeOuterGearDelete = $this->createPermission(Yii::t('app', 'Usuń'), 'eventEventEditEyeOuterGearDelete', false, true);
        $eventsEventEditEyeOuterGear->next = [$eventsEventEditEyeOuterGearManage, $eventsEventEditEyeOuterGearAdd, $eventsEventEditEyeOuterGearDelete];

        $eventsEventEditEyeAttachment = $this->createPermission(Yii::t('app', 'Załączniki'), 'eventEventEditEyeAttachment', false, false);
        $eventsEventEditEyeAttachmentAdd = $this->createPermission(Yii::t('app', 'Dodaj'), 'eventEventEditEyeAttachmentAdd', false, true);
        $eventsEventEditEyeAttachmentDownload = $this->createPermission(Yii::t('app', 'Pobierz'), 'eventEventEditEyeAttachmentDownload', false, false);
        $eventsEventEditEyeAttachmentEdit = $this->createPermission(Yii::t('app', 'Edytuj'), 'eventEventEditEyeAttachmentEdit', false, true);
        $eventsEventEditEyeAttachmentDelete = $this->createPermission(Yii::t('app', 'Usuń'), 'eventEventEditEyeAttachmentDelete', false, true);
        $eventsEventEditEyeAttachment->next = [$eventsEventEditEyeAttachmentAdd, $eventsEventEditEyeAttachmentDownload, $eventsEventEditEyeAttachmentEdit, $eventsEventEditEyeAttachmentDelete];

        $eventsEventEditEyeCrew = $this->createPermission(Yii::t('app', 'Ekipa'), 'eventsEventEditEyeCrew', false, false);
        $eventsEventEditEyeCrewManage = $this->createPermission(Yii::t('app', 'Zarządzaj'), 'eventsEventEditEyeCrewManage', false, true);
        $eventsEventEditEyeCrewEdit = $this->createPermission(Yii::t('app', 'Edytuj'), 'eventsEventEditEyeCrewEdit', false, true);
        $eventsEventEditEyeCrewDelete = $this->createPermission(Yii::t('app', 'Usuń'), 'eventsEventEditEyeCrewDelete', false, true);
        $eventsEventEditEyeCrew->next = [$eventsEventEditEyeCrewManage, $eventsEventEditEyeCrewEdit, $eventsEventEditEyeCrewDelete];


        $eventsEventEditEyeOffer = $this->createPermission(Yii::t('app', 'Oferta'), 'eventsEventEditEyeOffer', false, false);
        $eventsEventEditEyeOfferAdd = $this->createPermission(Yii::t('app', 'Dodaj'), 'eventsEventEditEyeOfferAdd', false, true);
        $eventsEventEditEyeOfferImport = $this->createPermission(Yii::t('app', 'Importuj'), 'eventsEventEditEyeOfferImport', false, true);
        $eventsEventEditEyeOfferDelete = $this->createPermission(Yii::t('app', 'Usuń'), 'eventsEventEditEyeOfferDelete', false, true);
        $eventsEventEditEyeOfferGear = $this->createPermission(Yii::t('app', 'Sprzętówka'), 'eventsEventEditEyeOfferGear', false, true);

        $menuOffersViewDuplicate = $this->createPermission(Yii::t('app', 'Duplikuj'), 'menuOffersViewDuplicate', false, true);
        $eventsEventEditEyeOffer->next = [$eventsEventEditEyeOfferAdd, $eventsEventEditEyeOfferImport, $eventsEventEditEyeOfferGear, $eventsEventEditEyeOfferDelete, $menuOffersViewDuplicate];


        $eventsEventEditEyeVehicles = $this->createPermission(Yii::t('app', 'Flota'), 'eventsEventEditEyeVehicles', false, false);
        $eventsEventEditEyeVehiclesManage = $this->createPermission(Yii::t('app', 'Zarządzaj'), 'eventsEventEditEyeVehiclesManage', false, true);
        $eventsEventEditEyeVehiclesEdit = $this->createPermission(Yii::t('app', 'Edytuj czas pracy'), 'eventsEventEditEyeVehiclesEdit', false, true);
        $eventsEventEditEyeVehiclesDelete = $this->createPermission(Yii::t('app', 'Usuń'), 'eventsEventEditEyeVehiclesDelete', false, true);
        $eventsEventEditEyeVehicles->next = [$eventsEventEditEyeVehiclesManage, $eventsEventEditEyeVehiclesEdit, $eventsEventEditEyeVehiclesDelete];

        $eventsEventEditEyeNotifications = $this->createPermission(Yii::t('app', 'Powiadomienia'), 'eventsEventEditEyeNotifications', false, true);



        $eventsEventEditEyeWorkingHours = $this->createPermission(Yii::t('app', 'Godziny pracy'), 'eventsEventEditEyeWorkingHours', true, false);

        $eventsEventEditEyeWorkingHoursWorkingHours = $this->createPermission(Yii::t('app', 'Godziny pracy'), 'eventsEventEditEyeWorkingHoursWorkingHours', false, false);
        $eventsEventEditEyeWorkingHoursUserAdd = $this->createPermission(Yii::t('app', 'Dodaj'), 'eventsEventEditEyeWorkingHoursUserAdd', false, false);
        $eventsEventEditEyeWorkingHoursUserEdit = $this->createPermission(Yii::t('app', 'Edytuj'), 'eventsEventEditEyeWorkingHoursUserEdit', false, false);
        $eventsEventEditEyeWorkingHoursUserDelete = $this->createPermission(Yii::t('app', 'Usuń'), 'eventsEventEditEyeWorkingHoursUserDelete', false, false);
        $eventsEventEditEyeWorkingHoursWorkingHours->next = [$eventsEventEditEyeWorkingHoursUserAdd, $eventsEventEditEyeWorkingHoursUserEdit, $eventsEventEditEyeWorkingHoursUserDelete];

        $eventsEventEditEyeWorkingCosts = $this->createPermission(Yii::t('app', 'Koszty'), 'eventsEventEditEyeWorkingHoursCosts', false, false);
        $eventsEventEditEyeWorkingCostsAdd = $this->createPermission(Yii::t('app', 'Dodaj'), 'eventsEventEditEyeWorkingHoursCostsAdd', false, false);
        $eventsEventEditEyeWorkingCostsEdit = $this->createPermission(Yii::t('app', 'Edytuj'), 'eventsEventEditEyeWorkingHoursCostsEdit', false, false);
        $eventsEventEditEyeWorkingCostsDelete = $this->createPermission(Yii::t('app', 'Usuń'), 'eventsEventEditEyeWorkingHoursCostsDelete', false, false);
        
        $eventsEventEditEyeWorkingCosts->next = [$eventsEventEditEyeWorkingCostsAdd, $eventsEventEditEyeWorkingCostsEdit, $eventsEventEditEyeWorkingCostsDelete];

        $eventsEventEditEyeWorkingDiet = $this->createPermission(Yii::t('app', 'Diety'), 'eventsEventEditEyeWorkingHoursDiet', false, false);
        $eventsEventEditEyeWorkingDietAdd = $this->createPermission(Yii::t('app', 'Dodaj'), 'eventsEventEditEyeWorkingHoursDietAdd', false, false);
        $eventsEventEditEyeWorkingDietEdit = $this->createPermission(Yii::t('app', 'Edytuj'), 'eventsEventEditEyeWorkingHoursDietEdit', false, false);
        $eventsEventEditEyeWorkingDietDelete = $this->createPermission(Yii::t('app', 'Usuń'), 'eventsEventEditEyeWorkingHoursDietDelete', false, false);
        $eventsEventEditEyeWorkingDiet->next = [$eventsEventEditEyeWorkingDietAdd, $eventsEventEditEyeWorkingDietEdit, $eventsEventEditEyeWorkingDietDelete];

        $eventsEventEditEyeWorkingSummary = $this->createPermission(Yii::t('app', 'Podsumowanie'), 'eventsEventEditEyeWorkingHoursSummary', false, false);
        $eventsEventEditEyeWorkingHours->next = [$eventsEventEditEyeWorkingHoursWorkingHours, $eventsEventEditEyeWorkingCosts, $eventsEventEditEyeWorkingDiet, $eventsEventEditEyeWorkingSummary];


        $eventsEventEditEyeFinance = $this->createPermission(Yii::t('app', 'Finanse'), 'eventsEventEditEyeFinance', false, true);
        $eventsEventEditEyeFinanceAddCost = $this->createPermission(Yii::t('app', 'Dodaj koszt'), 'eventsEventEditEyeFinanceAddCost', false, false);
        $eventsEventEditEyeFinanceAddInvoice = $this->createPermission(Yii::t('app', 'Dodaj fakturę'), 'eventsEventEditEyeFinanceAddInvoice', false, false);
        $eventsEventEditEyeFinanceCreateInvoice = $this->createPermission(Yii::t('app', 'Wystaw fakturę'), 'menuInvoicesInvoiceCreate', false, false);
        $eventsEventEditEyeFinanceCreateCosts = $this->createPermission(Yii::t('app', 'Wystaw fakturę wydatki'), 'menuInvoicesExpenseCreate', false, false);
        $eventsEventEditEyeFinanceProjectCosts = $this->createPermission(Yii::t('app', 'Wartość projektu + razem'), 'eventsEventEditEyeFinanceProjectCosts', false, false);
        $eventsEventEditEyeFinanceExtraCosts = $this->createPermission(Yii::t('app', 'Koszty dodatkowe'), 'eventsEventEditEyeFinanceExtraCosts', false, false);
        $eventsEventEditEyeFinanceWorkingHoursCosts = $this->createPermission(Yii::t('app', 'Koszty obsługi według rejestracji czasu pracy'), 'eventsEventEditEyeFinanceWorkingHoursCosts', false, false);
        $eventsEventEditEyeFinanceCrewCosts = $this->createPermission(Yii::t('app', 'Koszty obsługi'), 'eventsEventEditEyeFinanceCrewCosts', false, false);
        $eventsEventEditEyeFinanceProfit = $this->createPermission(Yii::t('app', 'Zysk'), 'eventsEventEditEyeFinanceProfit', false, false);
        $eventsEventEditEyeFinanceInvoiceIn = $this->createPermission(Yii::t('app', 'Faktury przychody'), 'eventsEventEditEyeFinanceInvoiceIn', false, false);
        $eventsEventEditEyeFinanceInvoiceOut = $this->createPermission(Yii::t('app', 'Faktury koszty'), 'eventsEventEditEyeFinanceInvoiceOut', false, false);
        $eventsEventEditEyeFinanceProjectStatus = $this->createPermission(Yii::t('app', 'Status projektu'), 'eventsEventEditEyeFinanceProjectStatus', false, false);
        $eventsEventEditEyeFinanceNotes = $this->createPermission(Yii::t('app', 'Notatki'), 'eventsEventEditEyeFinanceNotes', false, false);
        $eventsEventEditEyeFinanceProvision = $this->createPermission(Yii::t('app', 'Typ prowizji / procent'), 'eventsEventEditEyeFinanceProvision', false, false);
        $eventsEventEditEyeFinanceAttachments = $this->createPermission(Yii::t('app', 'Załączniki'), 'eventsEventEditEyeFinanceAttachments', false, false);

        $eventsEventEditEyeFinanceAttachmentsDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'eventsEventEditEyeFinanceAttachmentsDelete', false, false);
        $eventsEventEditEyeFinanceAttachments->next = [$eventsEventEditEyeFinanceAttachmentsDelete];


        $eventsEventEditEyeFinance->next = [$eventsEventEditEyeFinanceAddCost, $eventsEventEditEyeFinanceAddInvoice, $eventsEventEditEyeFinanceCreateInvoice, $eventsEventEditEyeFinanceCreateCosts,
            $eventsEventEditEyeFinanceProjectCosts, $eventsEventEditEyeFinanceExtraCosts, $eventsEventEditEyeFinanceWorkingHoursCosts, $eventsEventEditEyeFinanceCrewCosts, $eventsEventEditEyeFinanceProfit,
            $eventsEventEditEyeFinanceInvoiceIn, $eventsEventEditEyeFinanceInvoiceOut, $eventsEventEditEyeFinanceProjectStatus, $eventsEventEditEyeFinanceNotes, $eventsEventEditEyeFinanceProvision, $eventsEventEditEyeFinanceAttachments];



        $eventsEventEditEyeFinanceExtraCostsEdit = $this->createPermission(Yii::t('app', 'Edycja'), 'eventsEventEditEyeFinanceExtraCostsEdit', false, false);
        $eventsEventEditEyeFinanceExtraCostsDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'eventsEventEditEyeFinanceExtraCostsDelete', false, false);
        $eventsEventEditEyeWorkingCostsEditProd = $this->createPermission(Yii::t('app', 'Edycja kosztu produkcyjnego'), 'eventsEventEditEyeWorkingHoursCostsEditProd', false, false);
        $eventsEventEditEyeFinanceExtraCosts->next = [$eventsEventEditEyeFinanceExtraCostsEdit, $eventsEventEditEyeFinanceExtraCostsDelete, $eventsEventEditEyeWorkingCostsEditProd];

        $eventsEventEditEyeFinanceProjectStatusEdit = $this->createPermission(Yii::t('app', 'Edycja'), 'eventsEventEditEyeFinanceProjectStatusEdit', false, false);
        $eventsEventEditEyeFinanceProjectStatus->next = [$eventsEventEditEyeFinanceProjectStatusEdit];

        $eventsEventEditEyeFinanceNotesEdit = $this->createPermission(Yii::t('app', 'Edycja'), 'eventsEventEditEyeFinanceNotesEdit', false, false);
        $eventsEventEditEyeFinanceNotes->next = [$eventsEventEditEyeFinanceNotesEdit];

        $eventsEventEditEyeFinanceProvisionEdit = $this->createPermission(Yii::t('app', 'Edycja'), 'eventsEventEditEyeFinanceProvisionEdit', false, false);
        $eventsEventEditEyeFinanceProvision->next = [$eventsEventEditEyeFinanceProvisionEdit];


        $eventsEventEditEyeHistory = $this->createPermission(Yii::t('app', 'Historia'), 'eventsEventEditEyeHistory', false, false);
        $eventsEventEditEye->next = [$eventsEventEditEyeCalendar, $eventsEventEditEyeDescription, $eventsEventEditEyeGear, $eventsEventEditEyeOuterGear, $eventsEventEditEyeAttachment, $eventsEventEditEyeCrew, $eventsEventEditEyeOffer, $eventsEventEditEyeVehicles, $eventsEventEditEyeNotifications, $eventsEventEditEyeWorkingHours, $eventsEventEditEyeFinance, $eventsEventEditEyeHistory, $eventsEventEditEyeClientDetails];
                if (Yii::$app->session->get('company')!=1)
        {
            $eventsEventTask = $this->createPermission(Yii::t('app', 'Zadania'), 'eventEventTasks', false, false);
            $eventsEventEstimate = $this->createPermission(Yii::t('app', 'Kosztorysy'), 'eventEventEstimate', false, false);
            $eventsEventDeal = $this->createPermission(Yii::t('app', 'Umowy'), 'eventEventDeal', false, false);
            $eventsEventBrief = $this->createPermission(Yii::t('app', 'Briefy'), 'eventEventBrief', false, false);
            $eventsEventStat = $this->createPermission(Yii::t('app', 'Statystyki'), 'eventEventStat', false, false);
            
            $eventsEventEditEye->next[] = $eventsEventTask;
            $eventsEventEditEye->next[] = $eventsEventEstimate;
            $eventsEventEditEye->next[] = $eventsEventDeal;
            $eventsEventEditEye->next[] = $eventsEventBrief;
            $eventsEventEditEye->next[] = $eventsEventStat;
            
        }
        $eventsEventNews = $this->createPermission(Yii::t('app', 'Aktualności'), 'eventEventNews', false, false);
        $eventsEventEditEye->next[] = $eventsEventNews;

        $eventPermissions->next[] = $eventsEvents;
        $eventsMeetings = $this->createPermission(Yii::t('app', 'Spotkania'), 'eventsMeetings', true, false);
        $eventPermissions->next[] = $eventsMeetings;
        $eventPermissions->next[] = $eventsPrivate;

        $eventsMeetingsView = $this->createPermission(Yii::t('app', 'Podgląd'), 'eventMeetingView', true, false);
        $eventsMeetingsDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'eventMeetingDelete', true, true);
        $eventsMeetingsEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'eventMeetingEdit', true, true);
        $eventsMeetings->next = [$eventsMeetingsAdd, $eventsMeetingsView, $eventsMeetingsDelete, $eventsMeetingsEdit];


        $eventsRents = $this->createPermission(Yii::t('app', 'Wypożyczenia'), 'eventRents', true, false);
        $eventPermissions->next[] = $eventsRents;
        $eventsVacations = $this->createPermission(Yii::t('app', 'Urlopy'), 'eventVacations', true, false);
        $eventPermissions->next[] = $eventsVacations;


        $eventsVacationsView = $this->createPermission(Yii::t('app', 'Podgląd'), 'eventVacationsView', true, false);
        $eventsVacationsEdit = $this->createPermission(Yii::t('app', 'Usuwanie'), 'eventVacationsDelete', true, false);
        $eventsVacationsDelete = $this->createPermission(Yii::t('app', 'Edytowanie'), 'eventVacationsEdit', true, false);
        $eventsVacationsStatus = $this->createPermission(Yii::t('app', 'Zmiana statusu'), 'eventsVacationsStatus', false, false);

        $menuCalendarProdukcja = $this->createPermission(Yii::t('app', 'Kalendarz produkcji'), 'menuCalendarProdukcja', true, false);
        $eventPermissions->next[] = $menuCalendarProdukcja;
        $eventPermissions->next[] = $eventsProjects;
        $eventsVacations->next = [$eventsVacationsView, $eventsVacationsAdd, $eventsVacationsEdit, $eventsVacationsDelete, $eventsVacationsStatus];

        $eventsRentsEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'eventRentsEdit', true, true);
        $eventsRentsView = $this->createPermission(Yii::t('app', 'Podgląd'), 'eventRentsView', true, false);
        $eventsRentsDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'eventRentsDelete', true, true);
        $eventsRentsOffer = $this->createPermission(Yii::t('app', 'Oferty'), 'eventRentsOffer', false, true);
        $eventsRents->next = [$eventsRentsAdd, $eventsRentsView, $eventsRentsEdit, $eventsRentsDelete, $eventsRentsOffer, $warehouseInOut];

        $eventsRentsWarehouse = $this->createPermission(Yii::t('app', 'Magazyn'), 'eventRentsEdit', false, true);
        $eventsRentsView->next = [$eventsRentsWarehouse, $warehouseInOut];


        // **** Klienci **** //

        $clientPermissions = $this->createPermission(Yii::t('app', 'Kontrahenci'), 'menuClients', false, false);

        $clientClients = $this->createPermission(Yii::t('app', 'Kontrahenci'), 'clientClients', false, false);
        $clientClientsAdd = $this->createPermission(Yii::t('app', 'Dodawanie'), 'clientClientsAdd', false, true);
        $clientClientsEdit = $this->createPermission(Yii::t('app', 'Edycja'), 'clientClientsEdit', false, true);
        $clientClientsDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'clientClientsDelete', false, true);
        $clientClientsSee = $this->createPermission(Yii::t('app', 'Podgląd'), 'clientClientsSee', false, false);
        $clientClients->next = [$clientClientsAdd, $clientClientsEdit, $clientClientsDelete, $clientClientsSee];

        $clientClientsSeeContacts = $this->createPermission(Yii::t('app', 'Kontakty'), 'clientClientsSeeContacts', false, false);
        $clientClientsSeeProjects = $this->createPermission(Yii::t('app', 'Projekty'), 'clientClientsSeeProjects', false, false);
        $clientClientsSee->next = [$clientClientsSeeContacts, $clientClientsSeeProjects];


        $clientDiscount = $this->createPermission(Yii::t('app', 'Rabat'), 'clientDiscount', false, false);
        $clientDiscountView = $this->createPermission(Yii::t('app', 'Podgląd'), 'clientDiscountView', false, false);
        $clientDiscountAdd = $this->createPermission(Yii::t('app', 'Dodawanie'), 'clientDiscountAdd', false, true);
        $clientDiscountEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'clientDiscountEdit', false, true);
        $clientDiscountDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'clientDiscountDelete', false, true);
        $clientDiscount->next = [$clientDiscountView, $clientDiscountAdd, $clientDiscountEdit, $clientDiscountDelete];


        $clientContacts = $this->createPermission(Yii::t('app', 'Kontakty'), 'clientContacts', false, false);
        $clientContactsAdd = $this->createPermission(Yii::t('app', 'Dodawanie'), 'clientContactsAdd', false, true);
        $clientContactsEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'clientContactsEdit', false, true);
        $clientContactsDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'clientContactsDelete', false, true);
        $clientContactsSee = $this->createPermission(Yii::t('app', 'Podgląd'), 'clientContactsSee', false, false);
        $clientContactsSeeProjects = $this->createPermission(Yii::t('app', 'Projekty'), 'clientContactsSeeProjects', false, false);
        $clientContactsSeeMeetings = $this->createPermission(Yii::t('app', 'Spotkania'), 'clientContactsSeeMeetings', false, false);
        $clientContactsSee->next = [$clientContactsSeeProjects, $clientContactsSeeMeetings];
        $clientContacts->next = [$clientContactsAdd, $clientContactsEdit, $clientContactsSee, $clientContactsDelete];

        $clientPermissions->next = [$clientClients, $clientDiscount, $clientContacts];

        // **** Zamówienia **** //
        $orderPermissions = $this->createPermission(Yii::t('app', 'Wypożyczenia i konflikty'), 'menuOrders', false, true);

        // **** Miejsca **** //

        $locationPermissions = $this->createPermission(Yii::t('app', 'Miejsca'), 'menuLocations', false, false);
        $locationLocations = $this->createPermission(Yii::t('app', 'Miejsca'), 'locationLocations', false, false);
        $locationAttachments = $this->createPermission(Yii::t('app', 'Załączniki'), 'locationAttachments', false, false);
        $locationPermissions->next = [$locationLocations, $locationAttachments];
        $locationLocationsAdd = $this->createPermission(Yii::t('app', 'Dodawanie'), 'locationLocationsAdd', false, true);
        $locationLocationsDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'locationLocationsDelete', false, true);
        $locationLocationsEdit = $this->createPermission(Yii::t('app', 'Edycja'), 'locationLocationsEdit', false, true);
        $locationLocationsView = $this->createPermission(Yii::t('app', 'Podgląd'), 'locationLocationsView', false, false);
        $locationLocations->next = [$locationLocationsAdd, $locationLocationsDelete, $locationLocationsEdit, $locationLocationsView];
        $locationLocationsPanorama = $this->createPermission(Yii::t('app', 'Panorama'), 'locationLocationsViewPanorama', false, false);
        $locationLocationsGallery = $this->createPermission(Yii::t('app', 'Galeria'), 'locationLocationsViewGallery', false, false);
        $locationLocationsPlans = $this->createPermission(Yii::t('app', 'Plany techniczne'), 'locationLocationsViewPlans', false, false);
        $locationLocationsVideo = $this->createPermission(Yii::t('app', 'Video'), 'locationLocationsViewVideo', false, false);
        $locationLocationsAttachments = $this->createPermission(Yii::t('app', 'Załączniki'), 'locationLocationsViewAttachments', false, false);
        $locationLocationsView->next = [$locationLocationsPanorama, $locationLocationsGallery, $locationLocationsPlans, $locationLocationsVideo, $locationLocationsAttachments];

        $locationAttachmentsAdd = $this->createPermission(Yii::t('app', 'Dodawanie'), 'locationAttachmentsAdd', false, true);
        $locationAttachmentsEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'locationAttachmentsEdit', false, true);
        $locationAttachmentsDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'locationAttachmentsDelete', false, true);
        $locationAttachmentsView = $this->createPermission(Yii::t('app', 'Podgląd'), 'locationAttachmentsView', false, false);
        $locationAttachments->next = [$locationAttachmentsAdd, $locationAttachmentsEdit, $locationAttachmentsDelete, $locationAttachmentsView];


        $locationLocationsAttachmentsDownload = $this->createPermission(Yii::t('app', 'Download'), 'locationLocationsViewAttachmentsDownload', false, false);
        $locationLocationsAttachments->next = [$locationAttachmentsAdd, $locationAttachmentsEdit, $locationAttachmentsView, $locationAttachmentsDelete,  $locationLocationsAttachmentsDownload];



        // **** Magazyn **** //

        $gearPermissions = $this->createPermission(Yii::t('app', 'Magazyn'), 'menuGear', false, false);
        $gearOurWarehouse = $this->createPermission(Yii::t('app', 'Magazyn wewnętrzny'), 'gearOurWarehouse', false, false);
        $gearOuterWarehouse = $this->createPermission(Yii::t('app', 'Magazyn zewnętrzny'), 'gearOuterWarehouse', false, false);
        $gearWarehouseOutcomes = $this->createPermission(Yii::t('app', 'Wydanie z magazynu'), 'gearWarehouseOutcomes', false, false);
        $gearWarehouseIncomes = $this->createPermission(Yii::t('app', 'Przyjęcie do magazynu'), 'gearWarehouseIncomes', false, false);
        $gearCrossRental = $this->createPermission(Yii::t('app', 'Cross Rental'), 'gearCrossRental', false, false);
        $gearPrice = $this->createPermission(Yii::t('app', 'Ceny'), 'gearPrice', false, false);
        
        $gearModel = $this->createPermission(Yii::t('app', 'Modele'), 'gearModel', false, false);
        $gearGear = $this->createPermission(Yii::t('app', 'Egzemplarze'), 'gearGear', false, false);
        $gearCase = $this->createPermission(Yii::t('app', 'Case'), 'gearCase', false, false);
        $gearSet = $this->createPermission(Yii::t('app', 'Zestawy'), 'gearSet', false, false);
        $gearCategories = $this->createPermission(Yii::t('app', 'Kategorie'), 'gearCategories', false, true);
        $gearAttachments = $this->createPermission(Yii::t('app', 'Załączniki modeli'), 'gearAttachments', false, false);
        $gearService = $this->createPermission(Yii::t('app', 'Serwis'), 'gearService', false, false);
        $gearBase = $this->createPermission(Yii::t('app', 'Baza sprzętu'), 'gearOurWarehouseAddFromGearBase', false, true);
        $gearProducer = $this->createPermission(Yii::t('app', 'Producenci sprzętu'), 'gearProducer', false, true);
        $gearRfid = $this->createPermission(Yii::t('app', 'RFID'), 'gearRfid', false, true);
        $gearManageWarehouse = $this->createPermission(Yii::t('app', 'Zarządzaj magazynami'), 'gearManageWarehouse', false, true);
        $gearPermissions->next = [$gearOurWarehouse, $gearOuterWarehouse, $gearWarehouseOutcomes, $gearWarehouseIncomes, $gearCrossRental, $gearPrice, $gearModel, $gearGear, $gearCase, $gearSet, $gearCategories, $gearAttachments, $gearService, $gearBase, $gearProducer, $gearRfid, $gearManageWarehouse];

        $gearOurWarehouseMoveGear = $this->createPermission(Yii::t('app', 'Przesuwanie'), 'gearOurWarehouseMoveGear', false, true);
        $gearOurWarehouseCreateCase = $this->createPermission(Yii::t('app', 'Utwórz case'), 'gearOurWarehouseCreateCase', false, true);

        $gearCreate = $this->createPermission(Yii::t('app', 'Utworzenie modelu'), 'gearCreate', false, true);
        $gearEdit = $this->createPermission(Yii::t('app', 'Edytowanie model'), 'gearEdit', false, true);
        $gearView = $this->createPermission(Yii::t('app', 'Podgląd modelu'), 'gearView', false, false);
        $gearDelete = $this->createPermission(Yii::t('app', 'Usuwanie model'), 'gearDelete', false, true);
        $gearWarehousePrices = $this->createPermission(Yii::t('app', 'Widoczność cen'), 'gearWarehousePrices', false, false);
        $gearWarehouseQuantity = $this->createPermission(Yii::t('app', 'Widoczność dostępności'), 'gearWarehouseQuantity', false, false);
        $gearWarehouseQuantityEdit = $this->createPermission(Yii::t('app', 'Edycja ilości w magazynach'), 'gearWarehouseQuantityEdit', false, false);
        $gearItemHistory = $this->createPermission(Yii::t('app', 'Historia egzemplarza'), 'gearItemHistory', false, false);
        $gearItemServiceCreate = $this->createPermission(Yii::t('app', 'Serwis egzemplarza'), 'gearItemServiceCreate', false, true);

        $gearItemView = $this->createPermission(Yii::t('app', 'Podgląd egzemplarza'), 'gearItemView', false, false);
        $gearItemCreate = $this->createPermission(Yii::t('app', 'Utworzenie egzemplarza'), 'gearItemCreate', false, true);
        $gearItemEdit = $this->createPermission(Yii::t('app', 'Edycja egzemplarza'), 'gearItemEdit', false, true);
        $gearItemDelete = $this->createPermission(Yii::t('app', 'Usuwanie egzemplarza'), 'gearItemDelete', false, true);

        $gearSetView = $this->createPermission(Yii::t('app', 'Podgląd'), 'gearSetView', false, false);
        $gearSetCreate = $this->createPermission(Yii::t('app', 'Tworzenie'), 'gearSetCreate', false, true);
        $gearSetEdit = $this->createPermission(Yii::t('app', 'Edycja'), 'gearSetEdit', false, true);
        $gearSetDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'gearSetDelete', false, true);

        $gearOurWarehouse->next = [$gearOurWarehouseMoveGear, clone $gearBase, $gearOurWarehouseCreateCase, $warehouseInOut, $gearCreate,
            $gearEdit, $gearView, $gearDelete, $gearWarehousePrices, $gearWarehouseQuantity, $gearWarehouseQuantityEdit,$gearItemHistory, $gearItemServiceCreate, $gearItemView, $gearItemCreate, $gearItemEdit, $gearItemDelete];


        $gearOuterWarehouseMove = $this->createPermission(Yii::t('app', 'Przesuwanie'), 'gearOuterWarehouseMove', false, true);
        $gearOuterWarehouseAddFromWarehouse = $this->createPermission(Yii::t('app', 'Dodawanie z magazynu'), 'gearOuterWarehouseAddFromWarehouse', false, true);
        $outerGearHistory = $this->createPermission(Yii::t('app', 'Historia sprzętu zewnętrznego'), 'outerGearHistory', false, false);
        $outerGearCreate = $this->createPermission(Yii::t('app', 'Utworzenie sprzętu zewnętrznego'), 'outerGearCreate', false, true);
        $outerGearView = $this->createPermission(Yii::t('app', 'Podgląd sprzętu zewnętrznego'), 'outerGearView', false, false);
        $outerGearUpdate = $this->createPermission(Yii::t('app', 'Edycja sprzętu zewnętrznego'), 'outerGearUpdate', false, true);
        $outerGearDelete = $this->createPermission(Yii::t('app', 'Usuwanie sprzętu zewnętrznego'), 'outerGearDelete', false, true);
        $gearOuterWarehouse->next = [$gearOuterWarehouseMove, clone $gearBase, $gearOuterWarehouseAddFromWarehouse, $outerGearHistory, $outerGearCreate, $outerGearView, $outerGearUpdate, $outerGearDelete, $warehouseInOut];


        $gearWarehouseOutcomesView = $this->createPermission(Yii::t('app', 'Podgląd'), 'gearWarehouseOutcomesView', false, false);
        $gearWarehouseOutcomesDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'gearWarehouseOutcomesDelete', false, true);
        $gearWarehouseOutcomesAddUnplannedGear = $this->createPermission(Yii::t('app', 'Wydawanie nieplanowanego sprzętu'), 'gearWarehouseOutcomesAddUnplannedGear', false, false);
        $gearWarehouseOutcomesViewPdf = $this->createPermission(Yii::t('app', 'Podgląd pdf'), 'gearWarehouseOutcomesViewPdf', false, false);
        $gearWarehouseOutcomes->next = [$warehouseInOut, $gearWarehouseOutcomesAddUnplannedGear, $gearWarehouseOutcomesView, $gearWarehouseOutcomesDelete, $gearWarehouseOutcomesViewPdf];

        $gearWarehouseIncomesView = $this->createPermission(Yii::t('app', 'Podgląd'), 'gearWarehouseIncomesView', false, false);
        $gearWarehouseIncomesDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'gearWarehouseIncomesDelete', false, false);
        $gearWarehouseIncomesViewPdf = $this->createPermission(Yii::t('app', 'Podgląd pdf'), 'gearWarehouseIncomesViewPdf', false, false);
        $gearWarehouseIncomes->next = [$warehouseInOut, $gearWarehouseIncomesView, $gearWarehouseIncomesDelete, $gearWarehouseIncomesViewPdf];

        $gearCrossRentalCreate =  $this->createPermission(Yii::t('app', 'Udostępnianie'), 'gearCrossRentalCreate', false, true);
        $gearCrossRentalDelete =  $this->createPermission(Yii::t('app', 'Usuwanie udostępnienia'), 'gearCrossRentalDelete', false, true);
        $gearCrossRental->next = [$gearCrossRentalCreate, $gearCrossRentalDelete];

        $gearModel->next = [$gearCreate, $gearEdit, $gearView, $gearDelete];
        $gearGear->next = [$gearItemCreate, $gearItemEdit, $gearItemView, $gearItemDelete, $warehouseInOut];
        $gearSet->next = [$gearSetCreate, $gearSetEdit, $gearSetView, $gearSetDelete];
        $gearCaseEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'gearCaseEdit', false, true);
        $gearCaseDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'gearCaseDelete', false, true);
        $gearCaseView = $this->createPermission(Yii::t('app', 'Podgląd'), 'gearCaseView', false, false);
        $gearCaseRemoveItem = $this->createPermission(Yii::t('app', 'Usuwanie przedmiotu z case'), 'gearCaseRemoveItem', false, true);
        $gearCase->next = [$gearOurWarehouseCreateCase, $gearCaseEdit, $gearCaseDelete, $gearCaseView, $gearCaseRemoveItem, $warehouseInOut];

        $gearAttachmentsCreate = $this->createPermission(Yii::t('app', 'Utworzenie'), 'gearAttachmentsCreate', false, true);
        $gearAttachmentsEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'gearAttachmentsEdit', false, true);
        $gearAttachmentsDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'gearAttachmentsDelete', false, true);
        $gearAttachmentsView = $this->createPermission(Yii::t('app', 'Podgląd'), 'gearAttachmentsView', false, false);
        $gearAttachments->next = [$gearView, $gearAttachmentsCreate, $gearAttachmentsEdit, $gearAttachmentsDelete, $gearAttachmentsView];

        $gearServiceView = $this->createPermission(Yii::t('app', 'Podgląd'), 'gearServiceView', false, false);
        $gearServiceDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'gearServiceDelete', false, true);
        $gearServiceUpdate = $this->createPermission(Yii::t('app', 'Edytowanie'), 'gearServiceUpdate', false, true);
        $gearServiceStatut = $this->createPermission(Yii::t('app', 'Ustawienia statusów'), 'gearServiceStatut', false, true);
        $gearService->next = [$gearServiceView, $gearServiceDelete, $gearServiceUpdate, $gearServiceStatut];

        $gearBaseCreate = $this->createPermission(Yii::t('app', 'Utworzenie'), 'gearBaseCreate', false, false);
        $gearBaseEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'gearBaseEdit', false, false);
        $gearBaseDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'gearBaseDelete', false, false);
        $gearBaseView = $this->createPermission(Yii::t('app', 'Podgląd'), 'gearBaseView', false, false);
        $gearBaseImport = $this->createPermission(Yii::t('app', 'Import'), 'gearBaseImport', false, false);
        $gearBase->next = [$gearBaseCreate, $gearBaseEdit, $gearBaseDelete, $gearBaseView, $gearBaseImport];

        $gearProducerCreate = $this->createPermission(Yii::t('app', 'Utworzenie'), 'gearProducerCreate', false, false);
        $gearProducerEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'gearProducerEdit', false, false);
        $gearProducerDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'gearProducerDelete', false, false);
        $gearProducerView = $this->createPermission(Yii::t('app', 'Podgląd'), 'gearProducerView', false, false);
        $gearProducer->next = [$gearProducerCreate, $gearProducerEdit, $gearProducerDelete, $gearProducerView];

        // **** Użytkownicy **** //
        $usersPermissions = $this->createPermission(Yii::t('app', 'Użytkownicy'), 'menuUsers', false, false);


        $usersUsers = $this->createPermission(Yii::t('app', 'Użytkownicy'), 'usersUsers', false, false);
        $usersUsersInactive = $this->createPermission(Yii::t('app', 'Nieaktywni'), 'usersUsersInactive', false, true);
        $usersUsersCreate = $this->createPermission(Yii::t('app', 'Utworzenie'), 'usersUsersCreate', false, true);
        $usersUsersEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'usersUsersEdit', false, true);
        $usersUsersDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'usersUsersDelete', false, true);
        $usersUsersView = $this->createPermission(Yii::t('app', 'Podgląd'), 'usersUsersView', false, false);
        $usersUsersHistory = $this->createPermission(Yii::t('app', 'Historia'), 'usersUsersHistory', false, false);
        $usersUsers->next = [$usersUsersInactive, $usersUsersCreate, $usersUsersEdit, $usersUsersDelete, $usersUsersView, $usersUsersHistory];

        $usersSkills = $this->createPermission(Yii::t('app', 'Umiejętności'), 'usersSkills', false, true);
        $usersSkillsCreate = $this->createPermission(Yii::t('app', 'Utworzenie'), 'usersSkillsCreate', false, false);
        $usersSkillsEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'usersSkillsEdit', false, false);
        $usersSkillsDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'usersSkillsDelete', false, false);
        $usersSkillsView = $this->createPermission(Yii::t('app', 'Podgląd'), 'usersSkillsView', false, false);
        $usersSkills->next = [$usersSkillsCreate, $usersSkillsEdit, $usersSkillsDelete, $usersSkillsView];

        $userPayments = $this->createPermission(Yii::t('app', 'Rozliczenia'), 'usersPayments', false, true);
        $usersPaymentsChangeStatus = $this->createPermission(Yii::t('app', 'Zmień status'), 'usersPaymentsChangeStatus', false, false);
        $userPayments->next = [$usersPaymentsChangeStatus];
        $usersPermissions->next = [$usersUsers, $usersSkills, $userPayments];


        // **** Flota **** //
        $menuFleet = $this->createPermission(Yii::t('app', 'Flota'), 'menuFleet', false, false);
        $fleetVehicles = $this->createPermission(Yii::t('app', 'Pojazdy'), 'fleetVehicles', false, false);
        $fleetAttachments = $this->createPermission(Yii::t('app', 'Załączniki'), 'fleetAttachments', false, false);
        $menuFleet->next = [$fleetVehicles, $fleetAttachments];

        $fleetVehiclesCreate = $this->createPermission(Yii::t('app', 'Utworzenie'), 'fleetVehiclesCreate', false, true);
        $fleetVehiclesEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'fleetVehiclesEdit', false, true);
        $fleetVehiclesDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'fleetVehiclesDelete', false, true);
        $fleetVehiclesView = $this->createPermission(Yii::t('app', 'Podgląd'), 'fleetVehiclesView', false, false);
        $fleetVehicles->next = [$fleetVehiclesCreate, $fleetVehiclesEdit, $fleetVehiclesDelete, $fleetVehiclesView];

        $fleetAttachmentsCreate = $this->createPermission(Yii::t('app', 'Utworzenie'), 'fleetAttachmentsCreate', false, true);
        $fleetAttachmentsEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'fleetAttachmentsEdit', false, true);
        $fleetAttachmentsDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'fleetAttachmentsDelete', false, true);
        $fleetAttachmentsView = $this->createPermission(Yii::t('app', 'Podgląd'), 'fleetAttachmentsView', false, false);
        $fleetAttachmentsDownload = $this->createPermission(Yii::t('app', 'Pobranie'), 'fleetAttachmentsDownload', false, false);
        $fleetAttachments->next = [$fleetAttachmentsCreate, $fleetAttachmentsEdit, $fleetAttachmentsDelete, $fleetAttachmentsView, $fleetAttachmentsDownload];

        // **** Ustawienia **** //
        $menuSettings = $this->createPermission(Yii::t('app', 'Ustawienia'), 'menuSettings', false, true);

        $settingsRole = $this->createPermission(Yii::t('app', 'Rola na evencie'), 'settingsRole', false, false);
        $settingsAddons = $this->createPermission(Yii::t('app', 'Dodatki finansowe'), 'settingsAddons', false, false);
        $settingsCompany = $this->createPermission(Yii::t('app', 'Dane firmy'), 'settingsCompany', false, false);
        $settingsPersonalization = $this->createPermission(Yii::t('app', 'Personalizacja'), 'settingsPersonalization', false, false);
        $settingsOffers = $this->createPermission(Yii::t('app', 'Oferty'), 'settingsOffers', false, false);
        $settingsNotifications = $this->createPermission(Yii::t('app', 'Powiadomienia'), 'settingsNotifications', false, false);
        $settingsAccessControl = $this->createPermission(Yii::t('app', 'Uprawnienia'), 'settingsAccessControl', false, false);
        $settingsCompanyDepartments = $this->createPermission(Yii::t('app', 'Działy firmy'), 'settingsCompanyDepartments', false, false);
        $settingsFinances = $this->createPermission(Yii::t('app', 'Finanse'), 'settingsFinances', false, false);
        $settingsLanguage = $this->createPermission(Yii::t('app', 'Język'), 'settingsLanguage', false, false);
        $settingsCompany2 = $this->createPermission(Yii::t('app', 'Firmy'), 'settingsCompany2', false, false);
        $settingsStatuts = $this->createPermission(Yii::t('app', 'Ustawienia statusów'), 'settingsStatuts', false, false);
        $settingsEventTypes = $this->createPermission(Yii::t('app', 'Rodzaje wydarzeń'), 'settingsEventTypes', false, false);
        $settingsEventModels = $this->createPermission(Yii::t('app', 'Typy wydarzeń'), 'settingsEventModels', false, false);
        $settingsOfferDrafts = $this->createPermission(Yii::t('app', 'Schematy ofert'), 'settingsOfferDrafts', false, false);
        $menuSettings->next = [$settingsRole, $settingsAddons, $settingsCompany, $settingsPersonalization, $settingsOffers, $settingsNotifications, $settingsAccessControl, $settingsCompanyDepartments, $settingsFinances, $settingsLanguage, $settingsCompany2, $settingsStatuts, $settingsEventModels, $settingsEventTypes, $settingsOfferDrafts ];


        $settingsRoleAdd = $this->createPermission(Yii::t('app', 'Dodaj rolę'), 'settingsRoleAdd', false, false);
        $settingsRoleView = $this->createPermission(Yii::t('app', 'Podgląd'), 'settingsRoleView', false, false);
        $settingsRoleEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'settingsRoleEdit', false, false);
        $settingsRoleDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'settingsRoleDelete', false, false);
        $settingsRole->next = [$settingsRoleAdd, $settingsRoleView, $settingsRoleEdit, $settingsRoleDelete, clone $settingsAddons];

        $settingsAddonsCreateRate = $this->createPermission(Yii::t('app', 'Dodaj stawkę'), 'settingsAddons', false, false);
        $settingsAddonsRateManage = $this->createPermission(Yii::t('app', 'Zarządzaj'), 'settingsAddonsRateManage', false, false);
        $settingsAddonsSave = $this->createPermission(Yii::t('app', 'Zapis zmian'), 'settingsAddonsSave', false, false);
        $settingsAddons->next = [$settingsAddonsCreateRate, $settingsRoleAdd, $settingsAddonsRateManage, $settingsAddonsSave];

        $settingsAddonsRateManageView = $this->createPermission(Yii::t('app', 'Podgląd'), 'settingsAddonsRateManageView', false, false);
        $settingsAddonsRateManageUpdate = $this->createPermission(Yii::t('app', 'Edytowanie'), 'settingsAddonsRateManageUpdate', false, false);
        $settingsAddonsRateManageDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'settingsAddonsRateManageDelete', false, false);
        $settingsAddonsRateManage->next = [$settingsAddonsCreateRate, $settingsAddonsRateManageView, $settingsAddonsRateManageUpdate, $settingsAddonsRateManageDelete];

        $settingsCompanySave = $this->createPermission(Yii::t('app', 'Zapis zmian'), 'settingsCompanySave', false, false);
        $settingsCompany->next = [$settingsCompanySave];

        $settingsPersonalizationSave = $this->createPermission(Yii::t('app', 'Zapisz'), 'settingsPersonalizationSave', false, false);
        $settingsPersonalization->next = [$settingsPersonalizationSave];

        $settingsOffersSave = $this->createPermission(Yii::t('app', 'Zapis zmian'), 'settingsOffersSave', false, false);
        $settingsOffersAddFile = $this->createPermission(Yii::t('app', 'Dodawanie plików'), 'settingsOffersAddFile', false, false);
        $settingsOffersDeleteFile = $this->createPermission(Yii::t('app', 'Usuwanie plików'), 'settingsOffersDeleteFile', false, false);
        $settingsOffersViewFile = $this->createPermission(Yii::t('app', 'Podgląd plików'), 'settingsOffersViewFile', false, false);
        $settingsOffers->next = [$settingsOffersSave, $settingsOffersAddFile, $settingsOffersDeleteFile, $settingsOffersViewFile];

        $settingsNotificationsSave = $this->createPermission(Yii::t('app', 'Zapis zmian'), 'settingsNotificationsSave', false, false);
        $settingsNotifications->next = [$settingsNotificationsSave];

        $settingsAccessControlAdd = $this->createPermission(Yii::t('app', 'Dodawanie'), 'settingsAccessControlAdd', false, false);
        $settingsAccessControlManage = $this->createPermission(Yii::t('app', 'Zarządzanie'), 'settingsAccessControlManage', false, false);
        $settingsAccessControlSave = $this->createPermission(Yii::t('app', 'Zapisywanie'), 'settingsAccessControlSave', false, false);
        $settingsAccessControl->next = [$settingsAccessControlAdd, $settingsAccessControlManage, $settingsAccessControlSave];

        $settingsAccessControlManageEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'settingsAccessControlManageEdit', false, false);
        $settingsAccessControlManageDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'settingsAccessControlManageDelete', false, false);
        $settingsAccessControlManage->next = [$settingsAccessControlAdd, $settingsAccessControlManageEdit, $settingsAccessControlManageDelete];

        $settingsCompanyDepartmentsAdd = $this->createPermission(Yii::t('app', 'Dodawanie'), 'settingsCompanyDepartmentsAdd', false, false);
        $settingsCompanyDepartmentsView = $this->createPermission(Yii::t('app', 'Podgląd'), 'settingsCompanyDepartmentsView', false, false);
        $settingsCompanyDepartmentsEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'settingsCompanyDepartmentsEdit', false, false);
        $settingsCompanyDepartmentsDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'settingsCompanyDepartmentsDelete', false, false);
        $settingsCompanyDepartments->next = [$settingsCompanyDepartmentsAdd, $settingsCompanyDepartmentsView, $settingsCompanyDepartmentsEdit, $settingsCompanyDepartmentsDelete];


        $settingsFinancesSave = $this->createPermission(Yii::t('app', 'Zapis'), 'settingsFinancesSave', false, false);
        $financesPaymentMethods = $this->createPermission(Yii::t('app', 'Metody płatności'), 'financesPaymentMethods', false, false);
        $financesVatRate = $this->createPermission(Yii::t('app', 'Stawki VAT'), 'financesVatRate', false, false);

        $financesPaymentMethodsCreate = $this->createPermission(Yii::t('app', 'Dodawanie'), 'financesPaymentMethodsCreate', false, false);
        $financesPaymentMethodsUpdate = $this->createPermission(Yii::t('app', 'Edytowanie'), 'financesPaymentMethodsUpdate', false, false);
        $financesPaymentMethodsView = $this->createPermission(Yii::t('app', 'Podgląd'), 'financesPaymentMethodsView', false, false);
        $financesPaymentMethodsDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'financesPaymentMethodsDelete', false, false);
        $financesPaymentMethods->next = [$financesPaymentMethodsCreate, $financesPaymentMethodsUpdate, $financesPaymentMethodsView, $financesPaymentMethodsDelete];

        $financesInvoiceSeries = $this->createPermission(Yii::t('app', 'Serie faktur'), 'financesInvoiceSeries', false, false);
        $financesInvoiceSeriesCreate = $this->createPermission(Yii::t('app', 'Wystawianie'), 'financesInvoiceSeries2Create', false, false);
        $financesInvoiceSeriesEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'financesInvoiceSeries2Edit', false, false);
        $financesInvoiceSeriesDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'financesInvoiceSeries2Delete', false, false);
        $financesInvoiceSeriesView = $this->createPermission(Yii::t('app', 'Podgląd'), 'financesInvoiceSeries2View', false, false);
        $financesInvoiceSeries->next = [$financesInvoiceSeriesView, $financesInvoiceSeriesCreate, $financesInvoiceSeriesEdit, $financesInvoiceSeriesDelete];

        $financesInvoiceSeries2 = $this->createPermission(Yii::t('app', 'Serie faktur'), 'financesInvoiceSeries', false, false);
        $financesInvoiceSeriesCreate2 = $this->createPermission(Yii::t('app', 'Wystawianie'), 'financesInvoiceSeries2Create', false, false);
        $financesInvoiceSeriesEdit2 = $this->createPermission(Yii::t('app', 'Edytowanie'), 'financesInvoiceSeries2Edit', false, false);
        $financesInvoiceSeriesDelete2 = $this->createPermission(Yii::t('app', 'Usuwanie'), 'financesInvoiceSeries2Delete', false, false);
        $financesInvoiceSeriesView2 = $this->createPermission(Yii::t('app', 'Podgląd'), 'financesInvoiceSeries2View', false, false);
        $financesInvoiceSeries2->next = [$financesInvoiceSeriesView2, $financesInvoiceSeriesCreate2, $financesInvoiceSeriesEdit2, $financesInvoiceSeriesDelete2];


        $financesVatRateCreate = $this->createPermission(Yii::t('app', 'Dodawanie'), 'financesVatRateCreate', false, false);
        $financesVatRateEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'financesVatRateEdit', false, false);
        $financesVatRateView = $this->createPermission(Yii::t('app', 'Podgląd'), 'financesVatRateView', false, false);
        $financesVatRateDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'financesVatRateDelete', false, false);
        $financesVatRate->next = [$financesVatRateCreate, $financesVatRateEdit, $financesVatRateView, $financesVatRateDelete];

        $settingsFinances->next = [$settingsFinancesSave, $financesInvoiceSeries, $financesPaymentMethods, $financesVatRate];


        $settingsLanguageLanguages = $this->createPermission(Yii::t('app', 'Języki'), 'settingsLanguageLanguages', false, false);
        $settingsLanguageTranslate = $this->createPermission(Yii::t('app', 'Tłumaczenia'), 'settingsLanguageTranslate', false, false);
        $settingsLanguageRefresh = $this->createPermission(Yii::t('app', 'Załąduj nowe'), 'settingsLanguageRefresh', false, false);
        $settingsLanguage->next = [$settingsLanguageLanguages, $settingsLanguageTranslate, $settingsLanguageRefresh];

        $settingsLanguageLanguagesAdd = $this->createPermission(Yii::t('app', 'Dodawanie'), 'settingsLanguageLanguagesAdd', false, false);
        $settingsLanguageLanguagesView = $this->createPermission(Yii::t('app', 'Podgląd'), 'settingsLanguageLanguagesView', false, false);
        $settingsLanguageLanguagesEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'settingsLanguageLanguagesEdit', false, false);
        $settingsLanguageLanguagesDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'settingsLanguageLanguagesDelete', false, false);
        $settingsLanguageLanguages->next = [$settingsLanguageLanguagesAdd, $settingsLanguageLanguagesView, $settingsLanguageLanguagesEdit, $settingsLanguageLanguagesDelete];

        $settingsLanguageTranslateSave = $this->createPermission(Yii::t('app', 'Zapisywanie'), 'settingsLanguageTranslateSave', false, false);
        $settingsLanguageTranslate->next = [$settingsLanguageTranslateSave];

        // **** Oferty **** //
        $menuOffers = $this->createPermission(Yii::t('app', 'Oferty'), 'menuOffers', true, false);
        $menuOffersAdd = $this->createPermission(Yii::t('app', 'Dodawanie'), 'menuOffersAdd', false, true);
        $menuOffersView = $this->createPermission(Yii::t('app', 'Podgląd - oko'), 'menuOffersView', true, false);
        $menuOffersEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'menuOffersEdit', true, true);
        $menuOffersDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'menuOffersDelete', true, true);
        $menuOffers->next = [$menuOffersAdd, $menuOffersView, $menuOffersEdit, $menuOffersDelete, $menuOffersViewDuplicate];

        $menuOffersViewEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'menuOffersViewEdit', false, true);
        $menuOffersView->next = [$menuOffersViewEdit];
        $menuStats = $this->createPermission(Yii::t('app', 'Statystyki'), 'menuStats', false, true);
        // **** Zadania **** //
        $menuTasks = $this->createPermission(Yii::t('app', 'Zadania'), 'menuTasks', true, false);
        $menuTasksMine = $this->createPermission(Yii::t('app', 'Moje'), 'menuTasksMine', false, false);
        $menuTasksOrdered = $this->createPermission(Yii::t('app', 'Zlecone'), 'menuTasksOrdered', false, false);
        $menuTasksEvents = $this->createPermission(Yii::t('app', 'Wg eventów'), 'menuTasksEvents', false, false);
        $menuTasksOthers = $this->createPermission(Yii::t('app', 'Pozostałe'), 'menuTasksOthers', false, false);
        $menuTasksSchema = $this->createPermission(Yii::t('app', 'Schematy zadań'), 'menuTasksSchema', false, true);
        $menuTasksAdd = $this->createPermission(Yii::t('app', 'Dodawanie'), 'menuTasksAdd', false, true);
        $menuTasksView = $this->createPermission(Yii::t('app', 'Podgląd'), 'menuTasksView', false, false);
        $menuTasksEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'menuTasksEdit', false, true);
        $menuTasksDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'menuTasksDelete', false, true);
        $menuTasksAccept = $this->createPermission(Yii::t('app', 'Akceptacja nie swoich'), 'menuTasksAccept', false, true);
        $menuTasks->next = [$menuTasksMine, $menuTasksOrdered, $menuTasksEvents,  $menuTasksOthers, $menuTasksSchema, $menuTasksAdd,$menuTasksView, $menuTasksEdit, $menuTasksDelete, $menuTasksAccept];

        // **** Rozliczenia **** //
        $menuSettlement = $this->createPermission(Yii::t('app', 'Rozliczenia'), 'menuSettlement', false, false);
        $menuSettlementSave = $this->createPermission(Yii::t('app', 'Miesiąc rozliczony'), 'menuSettlementSave', false, false);
        $menuSettlementSave2 = $this->createPermission(Yii::t('app', 'Cofnij miesiąc rozliczony'), 'menuSettlementSave2', false, false);
        $menuSettlement->next = [$menuSettlementSave, $menuSettlementSave2];

        // **** Faktury **** //
        $menuInvoices = $this->createPermission(Yii::t('app', 'Finanse'), 'menuInvoices', false, true);
        $menuInvoicesInvoice = $this->createPermission(Yii::t('app', 'Przychody'), 'menuInvoicesInvoice', false, false);
        $menuInvoicesExpense = $this->createPermission(Yii::t('app', 'Wydatki'), 'menuInvoicesExpense', false, false);
        $menuInvoicesMonthCost = $this->createPermission(Yii::t('app', 'Koszty miesięczne'), 'menuInvoicesMonthCost', false, false);
        $menuInvoicesInvestition = $this->createPermission(Yii::t('app', 'Inwestycje'), 'menuInvoicesInvestition', false, false);
        $menuInvoicesPurchase = $this->createPermission(Yii::t('app', 'Zakupy'), 'menuInvoicesPurchase', false, false);
        $menuInvoicesAnalize = $this->createPermission(Yii::t('app', 'Analizy'), 'menuInvoicesAnalize', false, false);
        $menuInvoices->next = [$menuInvoicesInvoice, $menuInvoicesExpense, $financesInvoiceSeries2, $menuInvoicesAnalize, $menuInvoicesPurchase, $menuInvoicesInvestition, $menuInvoicesMonthCost];

        $menuInvoicesInvoiceCreate = $this->createPermission(Yii::t('app', 'Wystawianie'), 'menuInvoicesInvoiceCreate', false, false);
        $menuInvoicesInvoiceSend = $this->createPermission(Yii::t('app', 'Wysyłanie'), 'menuInvoicesInvoiceSend', false, false);
        $menuInvoicesInvoiceView = $this->createPermission(Yii::t('app', 'Podgląd'), 'menuInvoicesInvoiceView', false, false);
        $menuInvoicesInvoiceEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'menuInvoicesInvoiceEdit', false, false);
        $menuInvoicesInvoiceDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'menuInvoicesInvoiceDelete', false, false);
        $menuInvoicesInvoice->next = [$menuInvoicesInvoiceCreate, $menuInvoicesInvoiceSend, $menuInvoicesInvoiceView, $menuInvoicesInvoiceEdit, $menuInvoicesInvoiceDelete];

        $menuInvoicesExpenseCreate = $this->createPermission(Yii::t('app', 'Wystawianie'), 'menuInvoicesExpenseCreate', false, false);
        $menuInvoicesExpenseSend = $this->createPermission(Yii::t('app', 'Wysyłanie'), 'menuInvoicesExpenseSend', false, false);
        $menuInvoicesExpenseView = $this->createPermission(Yii::t('app', 'Podgląd'), 'menuInvoicesExpenseView', false, false);
        $menuInvoicesExpenseEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'menuInvoicesExpenseEdit', false, false);
        $menuInvoicesExpenseDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'menuInvoicesExpenseDelete', false, false);
        $menuInvoicesExpense->next = [$menuInvoicesExpenseCreate, $menuInvoicesExpenseSend, $menuInvoicesExpenseView, $menuInvoicesExpenseEdit, $menuInvoicesExpenseDelete];

        $menuInvoicesPurchaseAdd = $this->createPermission(Yii::t('app', 'Dodawanie'), 'menuInvoicesPurchaseAdd', false, false);
        $menuInvoicesPurchaseEdit = $this->createPermission(Yii::t('app', 'Edytowanie'), 'menuInvoicesPurchaseEdit', false, false);
        $menuInvoicesPurchaseDelete = $this->createPermission(Yii::t('app', 'Usuwanie'), 'menuInvoicesPurchaseDelete', false, false);
        $menuInvoicesPurchase->next = [$menuInvoicesPurchaseAdd, $menuInvoicesPurchaseEdit, $menuInvoicesPurchaseDelete];


        // **** Toolbox **** //
        $menuToolbox = $this->createPermission(Yii::t('app', 'Toolbox'), 'menuToolbox', false, false);
        $menuToolboxBlend = $this->createPermission(Yii::t('app', 'Blend Calculator'), 'menuToolboxBlend', false, false);
        $menuToolboxShowTime = $this->createPermission(Yii::t('app', 'ShowTime'), 'menuToolboxShowTime', false, false);
        $menuToolboxSpacePlanner = $this->createPermission(Yii::t('app', 'Space Planner'), 'menuToolboxSpacePlanner', false, false);
        $menuToolboxWireless = $this->createPermission(Yii::t('app', 'Wireless Player - niezrobione, o co chodzi?'), 'menuToolboxWireless', false, false);
        $menuToolboxDmx = $this->createPermission(Yii::t('app', 'DMX Creator - niezrobione, o co chodzi?'), 'menuToolboxDmx', false, false);
        $menuToolbox->next = [$menuToolboxBlend, $menuToolboxShowTime, $menuToolboxSpacePlanner, $menuToolboxWireless, $menuToolboxDmx];

        $requestAll = $this->createPermission(Yii::t('app', 'Widoczność zgłoszeń wszystkich użytkowników'), 'RequestAll', false, false);

        // ------ //
        $this->role = $role;
        $this->roots[] = $cockpitPermissions;
        $this->roots[] = $calendarPermissions;
        $this->roots[] = $planboardPermissions;
        $this->roots[] = $eventPermissions;
        $this->roots[] = $clientPermissions;
        $this->roots[] = $locationPermissions;
        $this->roots[] = $gearPermissions;
        $this->roots[] = $usersPermissions;
        $this->roots[] = $menuFleet;
        $this->roots[] = $menuSettings;
        $this->roots[] = $menuStats;
        $this->roots[] = $menuOffers;
        $this->roots[] = $menuTasks;
        $this->roots[] = $menuSettlement;
        $this->roots[] = $menuInvoices;
        $this->roots[] = $menuToolbox;
        $this->roots[] = $orderPermissions;
        $this->roots[] = $chatPermissions;
        $this->roots[] = $requestAll;
    }

    public function render() {
        foreach ($this->roots as $root) {
            $root->render(0, $this->role->superuser);
        }
    }

    public function load($post) {
        foreach ($this->roots as $root) {
            $root->load($post);
        }
    }

    public function save() {
        foreach ($this->roots as $root) {
            $root->save($this->role, false);
        }
    }

    private function createPermission($label, $dbname, $whoCanSee = false, $superuser) {
        $permission = new BasePermission();
        $permission->label = $label;
        $permission->dbName = $dbname;
        $permission->superuser = $superuser;
        $permission->canSee = key_exists($permission->getPermissionName(), $this->permissions);
        $permission->unique_id = $this->permissionId;
        $this->permissionId++;
        if ($whoCanSee) {
            $permission->whoseCanSee = 1;
            if (key_exists($permission->getPermissionName().BasePermission::SUFFIX[BasePermission::ALL], $this->permissions)) {
                $permission->whoseCanSee = BasePermission::ALL;
            }
            if (key_exists($permission->getPermissionName().BasePermission::SUFFIX[BasePermission::MINE], $this->permissions)) {
                $permission->whoseCanSee = BasePermission::MINE;
            }
        }
        return $permission;
    }
}
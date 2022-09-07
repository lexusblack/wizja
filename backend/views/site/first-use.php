<?php
/* @var $this yii\web\View */
/* @var $dashboard \common\models\form\Dashboard */
use yii\helpers\Html;
use yii\helpers\Url;

$user = Yii::$app->user;

$this->title = Yii::t('app', 'Początek korzystania z systemu');
?>
     <div class="row">

        <div class="col-md-4">
            <div class="widget p-lg text-center" style="height:200px;">
                        <div class="m-b-md">
                            <i class="fa fa fa-thumbs-o-up fa-4x"></i>
                            <h1 class="m-xs">Zaczynamy!</h1>
                            <h3 class="font-bold no-margins">
                                <?= Yii::t('app', 'Witaj w New Event Management') ?>
                            </h3>
                            <p><?= Yii::t('app', 'Zanim przystąpisz do pracy zapoznaj się z naszą instrukcją i uzupełnij niezbędne dane. Możesz wydrukować instrukcję klikając w link poniżej')?></p>
                            <p><?=Html::a('Instrukcja', '/files/instrukcja.pdf')?></p>
                            <p><?=Html::a('Samouczek video', '#video-tutorial')?></p>
                        </div>
                    </div>
            </p>
     </div>
    <div class="col-md-4">
            <div class="ibox float-e-margins">
            <?php if ($model->companyData){ $class = 'black-bg'; }else{ $class= 'navy-bg';} ?>
                    <div class="ibox-title <?=$class?>">
                        <h5><?= Yii::t('app', '1. Uzupełnij dane firmy') ?></h5>
                        <div class="ibox-tools white">
                        <?php if ($class=='navy-bg'){ ?>
                        <i class="fa fa-check-circle"></i>
                        <?php } ?>
                        </div>
                    </div>
                    <div>
                        <div class="ibox-content dashboard-200">
                        <p><?=Yii::t('app', 'Uzupełnij dane firmy, nazwę, adres, dodaj swoje logo. Na podstawie podanego adresu będą wyliczane km w transporcie ofertach oraz odległość do miejsc eventowych.')?></p>
                        <p><?=Html::a(Yii::t('app', 'Zakładka Ustawienia/Dane firmy'), '/admin/setting/index')?></p>
                        <?php if ($class=='navy-bg'){ ?>
                        <div  class="text-center" >
                            <button class="btn btn-primary btn-lg btn-outline" type="button"><i class="fa fa-check"></i> <?= Yii::t('app', 'Zrobione!') ?>
                            </button>
                        </div>
                        <?php } ?>
                        </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ibox float-e-margins">
            <?php if ($model->departments){ $class = 'black-bg'; }else{ $class= 'navy-bg';} ?>
                    <div class="ibox-title <?=$class?>">
                        <h5><?= Yii::t('app', '2. Dodaj działy swojej firmy') ?></h5>
                        <div class="ibox-tools white">
                        <?php if ($class=='navy-bg'){ ?>
                        <i class="fa fa-check-circle"></i>
                        <?php } ?>
                        </div>
                    </div>
                    <div>
                        <div class="ibox-content dashboard-200">
                        <p><?=Yii::t('app', 'Przejdź do zakładki Ustawienia/Działy firmy, dodaj tu działy jakie są w Twojej firmie i przypisz im kolory. Po wybraniu kolorów będą one widoczne na paskach wydarzeń w formie kropek, które symbolizują informacje jakie działy uczestniczą w evencie.')?></p>
                        <p><?=Html::a(Yii::t('app', 'Ustawienia/Działy firmy'), '/admin/department/index')?></p>
                        <?php if ($class=='navy-bg'){ ?>
                        <div  class="text-center" >
                            <button class="btn btn-primary btn-lg btn-outline" type="button"><i class="fa fa-check"></i> <?= Yii::t('app', 'Zrobione!') ?>
                            </button>
                        </div>
                        <?php } ?>
                        </div>
                </div>
            </div>
        </div>
    </div>
     <div class="row">

        <div class="col-md-4">
            <div class="ibox float-e-margins">
                        <?php if ($model->roles){ $class = 'black-bg'; }else{ $class= 'navy-bg';} ?>
                    <div class="ibox-title <?=$class?>">
                        <h5><?= Yii::t('app', '3. Utwórz i ustaw Role na Evencie ') ?></h5>
                        <div class="ibox-tools white">
                        <?php if ($class=='navy-bg'){ ?>
                        <i class="fa fa-check-circle"></i>
                        <?php } ?>                        
                        </div>
                    </div>
                    <div>
                        <div class="ibox-content dashboard-200">
                        <p><?=Yii::t('app', 'Utwórz i ustaw Role na Evencie np. Realizator, Technik oświetlenia, itd.
Wpisz nazwę ROLI i Stawkę dla Klienta. Na podstawie tej stawki będzie wyliczana wartość dla tej roli w ofertach. Zaznacz czy dana rola wymaga zgodności. Jeśli zaznaczysz, że tak Pracownik rozliczając swoje godziny pracy będzie widział tylko te role, które były do niego przypisane w planowaniu ekipy. Jeśli zaznaczysz, że rola nie wymaga zgodności to pracownik będzie mógł ją wybrać nawet kiedy nie była mu przypisana w planowaniu ekipy.
')?></p>
                        <p><?=Html::a(Yii::t('app', 'Ustawienia/Rola Na Evencie'), '/admin/user-event-role/index')?></p>
                        <?php if ($class=='navy-bg'){ ?>
                        <div  class="text-center" >
                            <button class="btn btn-primary btn-lg btn-outline" type="button"><i class="fa fa-check"></i> <?= Yii::t('app', 'Zrobione!') ?>
                            </button>
                        </div>
                        <?php } ?>
                        </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="ibox float-e-margins">
            <?php if ($model->skills){ $class = 'black-bg'; }else{ $class= 'navy-bg';} ?>

                    <div class="ibox-title <?=$class?>">
                        <h5><?= Yii::t('app', '4. Dodaj umiejętności dla pracowników') ?></h5>
                        <div class="ibox-tools white">
                        <?php if ($class=='navy-bg'){ ?>
                        <i class="fa fa-check-circle"></i>
                        <?php } ?>
                        </div>
                    </div>
                    <div>
                        <div class="ibox-content dashboard-200">
                        <p><?=Yii::t('app', 'Przejdź do zakładki Użytkownicy/Umiejętności i dodaj umiejętności np. realizator oświetlenia, technik sceny itd., które będziesz mógł później przypisać odpowiednim użytkownikom. Dzięki nim poszukiwanie odpowiednich osób przy planowaniu ekipy będzie o wiele łatwiejsze.')?></p>
                        <p><?=Html::a(Yii::t('app', 'Zakładka Użytkownicy/Umiejętności'), '/admin/skill/index')?></p>
                        <?php if ($class=='navy-bg'){ ?>
                        <div  class="text-center" >
                            <button class="btn btn-primary btn-lg btn-outline" type="button"><i class="fa fa-check"></i> <?= Yii::t('app', 'Zrobione!') ?>
                            </button>
                        </div>
                        <?php } ?>
                        </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ibox float-e-margins">
            <?php if ($model->users){ $class = 'black-bg'; }else{ $class= 'navy-bg';} ?>

                    <div class="ibox-title <?=$class?>">
                        <h5><?= Yii::t('app', '5. Dodaj użytkowników') ?></h5>
                        <div class="ibox-tools white">
                        <?php if ($class=='navy-bg'){ ?>
                        <i class="fa fa-check-circle"></i>
                        <?php } ?>
                        </div>
                    </div>
                    <div>
                        <div class="ibox-content dashboard-200">
                        <p><?=Yii::t('app', 'Przejdź do zakładki Użytkownicy/Użytkownicy i daj dostęp do systemu swoim pracownikom. Pamiętaj, żeby podać prawidłowy adres e-mail, bo to na niego zostanie wysłane hasło do zalogowania się. Więcej nt. dodawania użytkowników w instrukcji .pdf')?></p>
                        <p><?=Html::a(Yii::t('app', 'Użytkownicy'), '/admin/user/index')?></p>
                        <?php if ($class=='navy-bg'){ ?>
                        <div  class="text-center" >
                            <button class="btn btn-primary btn-lg btn-outline" type="button"><i class="fa fa-check"></i> <?= Yii::t('app', 'Zrobione!') ?>
                            </button>
                        </div>
                        <?php } ?>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <div id="video-tutorial" class="row">
    <div  class="col-md-12">
    <h1><?=Yii::t('app', 'Samouczek video')?></h1>
    </div>
    </div>
    <div class="row">
        <div  class="col-md-3">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Uzupełnianie danych firmy') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/JmHkAf7c9_M" frameborder="0" allowfullscreen class="video"></iframe>
                            </div>
                        </div>
                    </div>
        </div>
        <div  class="col-md-3">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Dodawanie działów firmy') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/L6R2dpf620s" frameborder="0" allowfullscreen class="video"></iframe>
                            </div>
                        </div>
                    </div>
        </div>
        <div  class="col-md-3">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Dodawanie roli na evencie') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/Uca1dXtkX3s" frameborder="0" allowfullscreen class="video"></iframe>
                            </div>
                        </div>
                    </div>
        </div>
        <div  class="col-md-3">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Tworzenie kategorii magazynu') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/7VA5t3RbzjA" frameborder="0" allowfullscreen class="video"></iframe>
                            </div>
                        </div>
                    </div>
        </div>
    </div>
    <div class="row">
        <div  class="col-md-3">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Dodawanie modelu, egzemplarza oraz tworzenie case') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/pn-eCqwdKLg" frameborder="0" allowfullscreen class="video"></iframe>
                            </div>
                        </div>
                    </div>
        </div>
        <div  class="col-md-3">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Import sprzętu z pliku excel') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/LLBTVNSYmX4" frameborder="0" allowfullscreen class="video"></iframe>
                            </div>
                        </div>
                    </div>
        </div>
        <div  class="col-md-3">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Dodawanie sprzętu z bazy sprzętu NEW') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/hNFms4z17XM" frameborder="0" allowfullscreen class="video"></iframe>
                            </div>
                        </div>
                    </div>
        </div>
        <div  class="col-md-3">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Dodawanie floty') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/dfwHWie7DXs" frameborder="0" allowfullscreen class="video"></iframe>
                            </div>
                        </div>
                    </div>
        </div>
    </div>
    <div class="row">
        <div  class="col-md-3">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Dodawanie umiejętności dla pracowników') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/e-nCsb1G22U" frameborder="0" allowfullscreen class="video"></iframe>
                            </div>
                        </div>
                    </div>
        </div>
        <div  class="col-md-3">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Dodawanie użytkowników') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/R1dsNprlBO0" frameborder="0" allowfullscreen class="video"></iframe>
                            </div>
                        </div>
                    </div>
        </div>
        <div  class="col-md-3">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Nadawanie uprawnień użytkownikom') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/fDtTn-jHr68" frameborder="0" allowfullscreen class="video"></iframe>
                            </div>
                        </div>
                    </div>
        </div>
        <div  class="col-md-3">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Dodawanie kontrahenta') ?></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/sqcM9lAR5qg" frameborder="0" allowfullscreen class="video"></iframe>
                            </div>
                        </div>
                    </div>
        </div>
    </div>

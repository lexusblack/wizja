<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = Yii::t('app', 'Update');

?>
<div class="row" style="max-width:1450px; margin:auto;">
		<div class="col-xs-6" style="text-align:center; padding-top:10px;">
				<?=Html::a(Html::img("/files/new-event.svg", ['style'=>'width:100%; max-width:219px; padding-top:15px; padding-bottom:10px;']),['login'])?>
		</div>
	<div class="col-xs-1" ></div>
<div class="col-xs-5" style="text-align:center; padding-top:1px;">
	
	</div>
</div>
<div style="margin:-10px 10px 0;">
<div style="padding-bottom:50px;">
<div class="middle-box blok" style="margin-bottom:50px;">
	<p class="tytul">Wersja 6.1 (05.07)</p>
		<div class="tabela">
	<table>
		<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
					<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
				<td><p>Blokowanie grup sprzętowych (paklist)</p></td></tr>
		<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
					<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
				<td><p>Możliwość edycji stawek VAT w transporcie i obsłudze</p></td></tr>
		<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
					<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
				<td><p>Uzupełnienie pozycji zapisujących się w historii</p></td></tr>
		<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
					<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
				<td><p>Poprawki</p></td></tr>
	</table>
	</div></div></div>
<div class="middle-box blok" style="position:relative;">
	<p class="tytul">Wersja 6.0 (25.04)</p>
	
		 <?=Html::img("/files/zebatka-z.svg", ['style'=>'width:100%; max-width:170px;position:absolute; right:1%; top:-10%;', 'id'=>'kolko'])?>
	<div class="kola">
	<?=Html::img("/files/Zasób 5.png", ['style'=>'width:100%; max-width:270px;position:absolute; left:-27%; top:70%;'])?>
	 <?=Html::img("/files/zebatka@3x.png", ['style'=>'width:100%; max-width:170px;position:absolute; right:-20%; top:40%;'])?>
	</div>
	<div class="tabela">
	<table>
		<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
					<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
			<td>
	<p class="bold"> Wiele magazynów - nowa usługa płatna pozwalająca na tworzenie wielu magazynów, wydawanie i 
przyjmowanie sprzętu na wybrany magazyn, przesunięcia międzymagazynowe sprzętu oraz wiele 
	funkcjonalności z nią połączonych</p>
				<div class="lista">
	<p>Przy dodawaniu egzemplarza od razu określa się do którego magazynu ma przynależeć. Przy imporcie sprzętu wskazanie, do którego magazynu importujemy.
</p>
	<p>
Funkcję dodawania/usuwania sprzętu ilościowego - wskazanie z jakie magazynu usuwamy lub do jakiego dodajemy
 </p>
	<p>
Edycja ilości w magazynach - Funkcja przesunięć, czyli za każdym razem wybieramy skąd jedzie i dokąd przesuwamy sprzęt
</p>
	<p>
Przesunięcie magazynowe w formie wydania/przyjęcia z opcją wyszukiwania w magazynie sprzętu.
</p>
	<p>
Wydanie i przyjęcie do/z konkretnego magazynu i ta informacja zapisywana w bazie. Lista sprzętu do wydania ograniczona do konkretnego magazynu. 
</p>
	<p>
Przy kasowaniu wydania/przyjęcia cofnięcie sprzętu do konkretnego magazynu lub na event. Podobnie przy kasowaniu eventu/wypożyczenia. Blokowanie możliwość kasowania wydania/przyjęcia jeśli sprzęt był już wydawany dalej. Odpowiedni komunikat po kasowaniu, gdzie cofnęło sprzęt. 
</p>
	<p>
W modelu lista sprzętów, gdzie co obecnie się znajduje i ile sztuk.
</p>
	<p>
W modelu i egzemplarzu historia wydań i przyjęć
</p>
	<p>
Sprzęt na eventach - lista eventów przy modelu, ile zostało wydanych na wydarzenie. 
</p>
	<p>
Zablokowane ilości, żeby nie można było wydać/przyjąć więcej niż jest
</p>
	<p>
Wysyłanie na serwis - Informacja na liście sprzętu ile w konkretnym magazynie jest sprzętu w serwisie i w którym jeśli jest
 więcej niż jeden.
</p>
	<p>
Po kliknięciu w pole "na eventach" w magazynie ogólnym pojawia się okno z informacją jaki sprzęt gdzie aktualnie się znajduje.
</p>
	<p>
Na liście sprzętu egzemplarzowym zostało dodane dodatkowe pole z informacją gdzie aktualnie się znajduje. Podobnie w szczegółach egzemplarza. 
</p>
	<p>
Informacje magazynowe - gdzie w danym magazynie leży dany sprzęt - czyli takie krotki model sprzętu- dodane do importu w excelu 
	</p>
	<p>
Przy wprowadzaniu nowego sprzętu obowiązkowy wybór konkretnego magazynu
	</p>
				</div>
			</td>
		</tr>
			<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
			
				
			</td>
				<td><p></p></td></tr>
			<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
					<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
				<td><p>Całkowita przebudowa systemu wydań i przyjęć magazynowych</p></td></tr>
			<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
					<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
				<td><p style="font-weight:bold">Oferty</p>
				<p>Ułamkowe dni pracy<br>
					Brak Vat-u przy ofertach Euro - oznaczenie w stawkach/cenach</p></td></tr>
			<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
					<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
			<td>
				<p style="font-weight:bold">Magazyn</p>
				<p>Dodanie kolumny w excelu do importu „widoczny w ofercie”<br>
					Dodanie kolumny w magazynie wewnętrznym widoczne w magazynie, widoczne w ofercie</p>
				
				</td></tr>
			<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
					<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
			<td>
				<p>Zestawienie - jak wybrane sprzęty zachowują się w wybranej dacie</p>
				</td></tr>
			<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
					<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
			<td>
				<p>Uzupełnienie tłumaczenia na język angielski</p>
				</td></tr>
			<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
					<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
			<td>
				<p>Historia logów w sprzęcie (utworzenie sprzętu, egzemplarza, przesunięcia, miany, itp.)</p>
				</td></tr>
			<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
					<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
			<td>
				<p>Export Cennika do excela z podmodułu CENY</p>
				</td></tr>
			<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
					<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
			<td>
				<p>Export do arkusza excel zestawienia zajętości sprzętu (zielony kalendarz przy sprzęcie)</p>
				</td></tr>
			<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
					<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
			<td>
				<p>Import - rozbudowany import o wiele magazynów, miejsce w magazynie, uwagi, opis, informacje</p>
				</td></tr>
			<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
					<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
			<td>
				<p style="font-weight:bold">Wydarzenie (Event, Wypożyczenie)</p>
				<p>Checkboksy do hurtowego zaznaczenia sprzętów do przenoszenia między grupami<br>
					Packlista - podział na podgrupy jak w magazynie<br>
					Sortowanie sprzętu do Packlisty<br>
					Historia zmian w harmonogramie - uzupełniona o dodatkowe logi jak miana ilości sprzętu ekipy, itp.<br>
					Dodanie Wagi sprzętu w Wypożyczeniu<br>
					Dane kontaktowe do pracowników dodanych do eventu - nowa kolumna<br>
					Klikalność klienta (aktywny link) w wypożyczeniu<br>
					Dodatkowa kolumna w dodawaniu ekipy - Pracownik/Pracownik zewnętrzny<br>
					Wartość sprzętu na packliście - możliwość wydrukowania drugiej packlisty z wartością sprzętu</p>
				</td></tr>
				<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
				<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
			</td>
			<td>
				<p style="font-weight:bold">Pozostałe</p>
				<p>Możliwość kopiowania głównej wartości % prowizji do pozostałych pól - Prowizja PM<br>
					Widok nazw wydarzeń na paskacj w kalendarzu - powiązanie z możliwością edyzji własnych wydarzeń<br>
					Informacja w kalendarzu jeśli jest ustawiony filtr<br>
					Dodatkowa kolumna Serwis w arkuszu w Magazynie Wewnętrznym przy sprzęcie (arkusz></p>
				</td></tr>
		<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
				<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
				
			</td>
			<td>
				<p>Nowy raport finansowy - podsumuje cały rok, dostępny tylko dla wybranych grup użytkowników</p>
				</td></tr>
				<tr>
			<td style="vertical-align: top;  margin:0 5px; width:25px; padding-right:3px;">
				<?=Html::img("/files/Oval Copy@1x.png", ['style'=>'width:100%; max-width:15px;'])?>
			</td>
			<td>
				<p style="font-weight:bold">Poprawki</p>
				<p>Wyświetlanie wszystkich modeli w Module ceny<br>
Daty w urlopie - usunięcie błędu daty<br>
Dodanie pojazdu do oferty - uzupełnienie błędnej funkcji<br>
Odświeżenie packlisty po przeniesieniu z jednej grupy do drugiej<br>
Komunikat o pokrywaniu się czasu pracy przy dodawaniu pracownika do eventu<br>
Usprawnienie tworzenia konfliktów<br>
Powiadomienia mailowe o przydzieleniu do zadania<br>
Kopiowanie ekipy z eventu na event<br>
Optymalizacja Scrollowania sprzętów w magazynach w miarę ładowania kolejnych<br>
Optymalizacja funkcjonowania harmonogramu<br>
Usunięcie błędów przy przenoszeniu sprzętów pomiędzy grupami<br>
Usunięcie błędów przy tworzeniu konfliktów<br>
Drobne poprawki w wyświetlaniu sprzętów w Packlista - cały sprzęt, usunięcie duplikatów<br>
Drobne błędy przy warunkach Statusów Eventu - możliwość kasowania Ekipy<br>
Uzupełnienie modułu „ceny" o liczbę wyświetlanych ilości stawek i cen na jednej stronie oraz ich edycji<br>
Optymalizacja Duplikacji ofert<br>
Dodawanie obsługi w ofercie poprzez DODAJ - koniecznośc wskazania etapu<br>
Możliwośc wyłączenia wyświetlania z szablony oferty kolumny LICZBA<br>
Uzupełnienie funkcji numeracji faktur zaliczkowych<br>
Możliwość blokady wpisów użytkowników po zmianie statusu eventu<br></p>
				</td></tr>
	</table>
</div></div>
	<div style="">
	
		 </div>

</div>

 <?php $this->registerCss('
 	@media screen and (min-width: 701px) {
  body{

	 background-image: url("/files/Zasób 1.svg");
	 background-position: top center;
	 
	 background-attachment: scroll;
	 background-size: cover;
	 }
	 }
	 @media screen and (max-width: 890px) {
	 #kolko{
	 display:none;
	 }
	 body{
	 background-image: url("/files/tlo-update.png");
	 }
	 .kola{
	 display:none;
	 }
	 }
	  @media screen and (max-width: 700px) {
	 #kolko{
	 display:inline;
	 }
	 }
	 .lista p{
 line-height: 1;
 margin:0 0 15px;
	 }
 .tytul{
 color:#9C8D929B;

 font-size:36px;
 font-weight:bold;
 margin:30px;
 }
 
 .tabela{
 max-width:780px;
 margin:auto;
 
 }
 .blok{
 border:1px solid #bbbbbb; margin-bottom:60px; padding:20px; max-width:900px; width:100%; margin:10px auto 60px;  background-color:#ffffff99; border-radius:15px; box-shadow: 0 0 1em #bbb;
 
 }

 .bold{
 font-weight:bold;
 max-width:600px;
 }
 ');
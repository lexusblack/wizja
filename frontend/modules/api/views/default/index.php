<div class="api-default-index">
    <h1>API</h1>
    <h3>Url</h3>
    <div><?php echo \yii\helpers\Url::to(['/api'], 'https'); ?></div>
    <h3>Modele</h3>
    <ul class="list-unstyled">
        <li>answer</li>
        <li>duel</li>
        <li>player-answer</li>
        <li>player</li>
        <li>player-duel</li>
        <li>question-category</li>
        <li>question</li>
        <li>round</li>
        <li>round-question</li>
    </ul>

    <h3>Dla wszytkich</h3>
    <table class="table">
        <tr>
            <th>Typ</th>
            <th>Url</th>
            <th>Opis</th>
        </tr>
        <tr>
            <td>GET</td>
            <td>/{model}</td>
            <td>Lista wszytkich</td>
        </tr>
        <tr>
            <td>GET</td>
            <td>/{model}/{id}</td>
            <td>Szczegóły pojedyńczego</td>
        </tr>
        <tr>
            <td>POST</td>
            <td>/{model}/{id}</td>
            <td>Tworzenie modelu</td>
        </tr>
        <tr>
            <td>PUT</td>
            <td>/{model}/{id}</td>
            <td>Aktualizacja modelu</td>
        </tr>
        <tr>
            <td>DELETE</td>
            <td>/{model}/{id}</td>
            <td>Usunięcie modelu</td>
        </tr>
    </table>

<!--    <h3>Dodatkowe</h3>-->
<!--    <table class="table">-->
<!--        <tr>-->
<!--            <th>Typ</th>-->
<!--            <th>Url</th>-->
<!--            <th>Opis</th>-->
<!--        </tr>-->
<!--        <tr>-->
<!--            <td>GET</td>-->
<!--            <td>/slide?date={date}</td>-->
<!--            <td>Lista slajdów gdzie create_time większe od daty.</td>-->
<!--        </tr>-->
<!--        <tr>-->
<!--            <td>GET</td>-->
<!--            <td>/slide/list/{id}</td>-->
<!--            <td>Slajdy dla prezentacji</td>-->
<!--        </tr>-->
<!--        <tr>-->
<!--            <td>GET</td>-->
<!--            <td>/slide/current</td>-->
<!--            <td>Obecny slide</td>-->
<!--        </tr>-->
<!--        <tr>-->
<!--            <td>GET</td>-->
<!--            <td>/history/list</td>-->
<!--            <td>List slajdów już wyświetlonych</td>-->
<!--        </tr>-->
<!--    </table>-->

</div>

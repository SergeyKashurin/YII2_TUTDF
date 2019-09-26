<?php
/**
 * Created by PhpStorm.
 * User: u92
 * Date: 23.11.2015
 * Time: 11:53
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */

$this->title = 'Формирование кредитной истории юридических лиц. (Ануитентный график)';
$form = ActiveForm::begin();
?>
<div class="site-index">

    <?php //echo $formatTUTDF; //print_r($error); ?>

    <div class="row">
        <div class="col-md-12">

            <h4>Для того чтобы сформировать файлы выгрузки кредитной истории, необходимо выполнить два следующих действия:</h4>
            <!-- Шаги по созданию КИ -->
            <div class="row col-md-offset-0">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-success">
                        <input type="radio" name="options" id="option1">1) Заполнить форму данными организации
                    </label>
                    <label class="btn btn-default">
                        <input type="radio" name="options" id="option4">2) Проверить корректность данных
                    </label>
                </div>
            </div>

            <blockquote>
                <p>Выбор формата</p>
                <footer>
                    Выберите формат TUTDF по которому необходимо сгенерировать кредитную историю <strong>2.0r</strong> (до 01.03.2015 г.) или <strong>4.0r</strong> (после 01.03.2015 г.)
                </footer>
            </blockquote>

            <!-- Выбор типа формата -->
            <div class="row">
                <div class="col-md-5">
                    <?=$form->field($model, 'formatTUTDF')->label('Выберите формат передачи данных в НБКИ:')->radioList(['0'=>'Tutdf 2.0r','1'=>'Tutdf 4.0r'])->error(); ?>
                </div>
            </div>

            <p>&nbsp;</p>

            <blockquote>
                <p>Сегмент общих данных по юридическому лицу</p>
                <footer>
                    Содержит общую информацию о юр. лице
                </footer>
            </blockquote>

            <!-- Название предприятия, Дата регистрации, Статус предприятия -->
            <div class="row">
                <div class="col-md-5">
                    <?=$form->field($model, 'CompanyName')->label('Название предприятия')->textInput(['placeholder' => 'Банк "Прохладный" ООО'])->error(); ?>
                </div>
                <div class="col-md-2">
                    <?=$form->field($model, 'RegistrationDate')->label('Дата регистрации')->textInput(['placeholder' => 'ГГГГММДД', 'type' => 'string', 'maxlength' => 8])->error(); ?>
                </div>
                <div class="col-md-2">
                    <?=$form->field($model, 'CompanyStatus')->label('Статус предприятия')->dropDownList(['0' => '0 – Функционирует', '1' => '1 – Не функционирует', '2' => '2 - Реструктуризация', '3' => '3 - Находится на рассмотрении', '4' => '4 – Перерегистрация', '5' => '5 – Продажа'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Дата регистрации заполняется только в случае если "Статус предприятия" не равен "0"
                </div>
            </div>

            <!-- ОГРН, ИНН -->
            <div class="row">
                <div class="col-md-5">
                    <?=$form->field($model, 'OGRN')->label('ОГРН')->textInput(['placeholder' => '9999999999999', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-4">
                    <?=$form->field($model, 'INN')->label('ИНН')->textInput(['placeholder' => '9999999999', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-3">
                    ОГРН - 13 символов, Индивидуальный номер налогоплательщика (ИНН) - 10.
                </div>
            </div>

            <hr>

            <blockquote>
                <p>Сегмент адресных данных по юридическому лицу</p>
                <footer>
                    Содержит общую информацию об известных адресах субъекта
                </footer>
            </blockquote>

            <!-- Почтовый индекс, район, местоположение, тип улицы -->
            <div class="row">
                <div class="col-md-2">
                    <?=$form->field($model, 'PostIndex')->label('Почтовый индекс')->textInput(['placeholder' => '361000', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-2">
                    <?=$form->field($model, 'HomeArea')->label('Район')->textInput(['placeholder' => 'Прохладненский', 'type' => 'text'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'HomeTown')->label('Местоположение')->textInput(['placeholder' => 'Город или нас. пункт', 'type' => 'text'])->error(); ?>
                </div>
                <div class="col-md-2">
                        <?=$form->field($model, 'HomeStreetType')->label('Тип улицы')->dropDownList(['01' => 'Аллея', '02' => 'Бульвар', '03' => 'Въезд', '04' => 'Дорога', '05' => 'Заезд', '06' => 'Казарма', '07' => 'Квартал', '08' => 'Километр', '09' => 'Кольцо', '10' => 'Линия', '11' => 'Местечко', '12' => 'Микрорайон', '13' => 'Набережная', '14' => 'Парк', '15' => 'Переулок', '16' => 'Переезд', '17' => 'Площадь', '18' => 'Площадка', '19' => 'Проспект', '20' => 'Проезд', '21' => 'Просек', '22' => 'Проселок', '23' => 'Проулок', '24' => 'Строение', '25' => 'Территория', '26' => 'Тракт', '27' => 'Тупик', '28' => 'Улица', '29' => 'Участок', '30' => 'Шоссе'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Коды типов улиц: пер - Переулок; мкр - Микрорайон; ул - Улица.
                </div>
            </div>

            <!-- Название улицы, Дом, Корпус, Строение, Квартира -->
            <div class="row">
                <div class="col-md-5">
                    <?=$form->field($model, 'HomeStreet')->label('Название улицы')->textInput(['placeholder' => 'Промышленная', 'type' => 'text'])->error(); ?>
                </div>
                <div class="col-md-1">
                    <?=$form->field($model, 'HomeHouse')->label('Дом')->textInput(['placeholder' => '115', 'type' => 'text'])->error(); ?>
                </div>
                <div class="col-md-1">
                    <?=$form->field($model, 'HomeKorp')->label('Корпус')->textInput(['placeholder' => '13', 'type' => 'text'])->error(); ?>
                </div>
                <div class="col-md-1">
                    <?=$form->field($model, 'HomeStory')->label('Строение')->textInput(['placeholder' => '13', 'type' => 'text'])->error(); ?>
                </div>
                <div class="col-md-1">
                    <?=$form->field($model, 'HomeKvartira')->label('Квартира')->textInput(['placeholder' => '255', 'type' => 'text'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Любое поле будет удалено, если передаваемые данные некорректны. См. раздел «Искажение данных»
                </div>
            </div>

            <hr>

            <blockquote>
                <p>Сегмент телефонных данных по юридическому лицу</p>
                <footer>
                    Содержит информацию о номерах и типах телефонов субъекта
                </footer>
            </blockquote>

            <!-- Тип телефона, номер телефона -->
            <div class="row">
                <div class="col-md-2">
                    <?=$form->field($model, 'PersonaTel1')->label('Номер телефона №1')->textInput(['placeholder' => '78663174422', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-2">
                    <?=$form->field($model, 'PersonaTel1Type')->label('Тип телефона №1')->dropDownList(['1' => 'Рабочий', '2' => 'Домашний', '3' => 'Факс', '4' => 'Сотовый', '5' => 'Другое'])->error(); ?>
                </div>
                <div class="col-md-2">
                    <?=$form->field($model, 'PersonaTel2')->label('Номер телефона №2')->textInput(['placeholder' => '78663174422', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'PersonaTel2Type')->label('Тип телефона №2')->dropDownList(['1' => 'Рабочий', '2' => 'Домашний', '3' => 'Факс', '4' => 'Сотовый', '5' => 'Другое'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Сегмент (сегменты) PN включает (включают) номер телефона субъекта кредитной истории.
                </div>
            </div>

            <!-- Тип телефона, номер телефона -->
            <div class="row">
                <div class="col-md-2">
                    <?=$form->field($model, 'PersonaTel3')->label('Номер телефона №3')->textInput(['placeholder' => '78663174422', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-2">
                    <?=$form->field($model, 'PersonaTel3Type')->label('Тип телефона №3')->dropDownList(['1' => 'Рабочий', '2' => 'Домашний', '3' => 'Факс', '4' => 'Сотовый', '5' => 'Другое'])->error(); ?>
                </div>
                <div class="col-md-2">
                    <?=$form->field($model, 'PersonaTel4')->label('Номер телефона №4')->textInput(['placeholder' => '78663174422', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'PersonaTel4Type')->label('Тип телефона №4')->dropDownList(['1' => 'Рабочий', '2' => 'Домашний', '3' => 'Факс', '4' => 'Сотовый', '5' => 'Другое'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Сегмент (сегменты) PN включает (включают) номер телефона субъекта кредитной истории.
                </div>
            </div>

            <hr>

            <blockquote>
                <p>Коды юридического лица</p>
                <footer>
                    Содержит перечень кодов принадлежащих юридическому лицу.
                </footer>
            </blockquote>

            <!-- Дата статуса, OKPO, OKOHX -->
            <div class="row">
                <div class="col-md-2">
                    <?=$form->field($model, 'CompanyStatusDate')->label('Дата статуса')->textInput(['placeholder' => 'ГГГГММДД', 'type' => 'string', 'maxlength' => 8, 'id' => 'input_CompanyStatusDate'])->error(); ?>
                </div>
                <div class="col-md-4">
                    <?=$form->field($model, 'OKPO')->label('ОКПО')->textInput(['placeholder' => '04008292', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'OKOHX')->label('ОКОНХ')->textInput(['placeholder' => '84200', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-3">
                    ОКОНХ - старая российская классификация видов экономической деятельности.
                    Дата статуса заполняется только в случае если "Статус предприятия" не равен "0".
                </div>
            </div>

            <!-- ОКВЭД, OKATO, ОКОГУ -->
            <div class="row">
                <div class="col-md-2">
                    <?=$form->field($model, 'OKBED')->label('ОКВЭД')->textInput(['placeholder' => '67.13.51', 'type' => 'string'])->error(); ?>
                </div>
                <div class="col-md-5">
                    <?=$form->field($model, 'OKATO')->label('OKATO')->textInput(['placeholder' => '45296561000', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-2">
                    <?=$form->field($model, 'OKOGU')->label('ОКОГУ')->textInput(['placeholder' => '15001', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-3">
                    ОКОГУ - Российская классификация государственных правительственных и административных подразделений, например, 15001 или 1700034
                </div>
            </div>
            </div>

            <!-- ОКФС, ОКОПФ, КПП, Сокращенное наименование предприятия -->
            <div class="row">
                <div class="col-md-1">
                    <?=$form->field($model, 'OKFS')->label('ОКФС')->textInput(['placeholder' => '22', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-1">
                    <?=$form->field($model, 'OKOPF')->label('ОКОПФ')->textInput(['placeholder' => '47', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-2">
                    <?=$form->field($model, 'KPP')->label('КПП')->textInput(['placeholder' => '781101001', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-5">
                    <?=$form->field($model, 'CompanyNameShort')->label('Сокращенное наименование предприятия')->textInput(['placeholder' => 'Название отделения внутренних дел МВД РФ или другого органа выдачи'])->error(); ?>
                </div>
                <div class="col-md-3">
                    ОКФС - Российская классификация форм собственности (типов собственности), например,  49
                </div>
            </div>

            <hr>

            <blockquote>
                <p>Сегмент сделка</p>
                <footer>
                    Содержит общую информацию о выданном кредите.
                </footer>
            </blockquote>

            <!-- Номер счёта или номер договора, Тип счёта, Отношение к счёту -->
            <div class="row">
                <div class="col-md-3">
                    <?=$form->field($model, 'KredScet')->label('Номер счёта или номер договора')->textInput(['placeholder' => '45407810900000000001', 'type' => 'string'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'KredScetType')->label('Тип счёта')->dropDownList(['10' => '10 - На развитие бизнеса', '11' => '11 - На пополнение оборотных средств', '12' => '12 - На покупку оборудования', '13' => '13 - На строительство', '14' => '14 - На покупку акций', '15' => '15 - Межбанковский кредит'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'KredScetOtn')->label('Отношение к счёту')->dropDownList(['9' => 'Юридическое лицо'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Номер счёта или номер договора: 45407810900000000001 или 15/10/166.
                    Отношение к счёту усечено до одного варианта "9 - Юридическое лицо"
                </div>
            </div>

            <!-- Дата открытия счёта, Дата последней выплаты, Состояние счёта -->
            <div class="row">
                <div class="col-md-3">
                    <?=$form->field($model, 'KredOpenDate')->label('Дата открытия счёта')->textInput(['placeholder' => 'ГГГГММДД', 'type' => 'string', 'maxlength' => 8, 'class' => 'form-control'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'KredLastPay')->label('Дата последней выплаты')->textInput(['placeholder' => 'ГГГГММДД', 'type' => 'string', 'maxlength' => 8])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'KredScetActive')->label('Состояние счёта')->dropDownList(['13' => 'Счет закрыт'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Состояние счёта всегда равно 13. (Усечено до 1 варианта)
                </div>
            </div>

            <!-- Исходная сумма кредита, Баланс, Полная стоимость кредита -->
            <div class="row">
                <div class="col-md-3">
                    <?=$form->field($model, 'KredFullSum')->label('Исх. сумма кредита')->textInput(['placeholder' => '50000', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'KredSum')->label('Баланс')->textInput(['placeholder' => '65000', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'PSK')->label('Полная стоимость кредита')->textInput(['placeholder' => '20,97', 'type' => 'string'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Баланс = сумма кредита + проценты (%).<br>
                    Полная стоимость кредита - по заемщику  - обязательно для ипотечных и потребительских кредитов.
                </div>
            </div>

            <div class="row">
                <hr>
            </div>

            <!-- Скрытое поле для определения типа проводимой операции -->
            <div class="row">
                <div class="col-md-6">
                    <?=$form->field($model, 'operationType')->label('Тип операции: заведение КИ юр. лица')->textInput(['value' => 'urlic', 'type' => 'hidden'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <!-- empty -->
                </div>
            </div>

            <div class="row">
                <hr>
            </div>

            <!-- Дата окончания срока договора, Дата финального платежа, Дата финальной выплаты процентов -->
            <div class="row">
                <div class="col-md-2">
                    <?=$form->field($model, 'KredEndDate')->label('Дата окончания срока договора')->textInput(['placeholder' => 'ГГГГММДД', 'type' => 'string', 'maxlength' => 8, 'class' => 'form-control'])->error(); ?>
                </div>
                <div class="col-md-2">
                    <?=$form->field($model, 'KredEndPay')->label('Дата финального платежа')->textInput(['placeholder' => 'ГГГГММДД', 'type' => 'string', 'maxlength' => 8])->error(); ?>
                </div>
                <div class="col-md-2">
                    <?=$form->field($model, 'KredEndProc')->label('Дата финальной выплаты процентов')->textInput(['placeholder' => 'ГГГГММДД', 'type' => 'string', 'maxlength' => 8])->error(); ?>
                </div>
                <div class="col-md-2">
                    <br/>
                    <?=$form->field($model, 'KodZaloga')->label('Код залога')->dropDownList(['01' => '01 - Автомобиль', '02' => '02 - Товары в обороте', '11' => '11 - Недвижимость', '12' => '12 - Коммерческая недвижимость', '13' => '13 - Здания и сооружения', '14' => '14 - Сельскохозяйственное оборудование и машины', '15' => '15 - Производственное оборудование', '16' => '16 - Предметы домашнего обихода (бытовая техника и проч.)', '17' => '17 - Акции', '18' => '18 - Векселя', '19' => '19 - Долговые расписки, облигации', '20' => '20 - Прочее'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <br>
                    Дата финальной выплаты процентов и Дата финального платежа по идее одна и та же.
                </div>
            </div>

            <hr>

        <!-- /ФОРМАТ Tutdf 3.0r -->
        <div id="tutdf30r">

            <blockquote>
                <p>Сегмент сделки формата TUTDF 4.0r</p>
                <footer>
                    При неправильном форматировании полей, запись отклоняется полностью
                </footer>
            </blockquote>

            <!-- Флаг о наличии поручителя, Объем обязательства, обеспечиваемого поручительством, Сумма поручительства -->
            <div class="row">
                <div class="col-md-3">
                    <?=$form->field($model, 'PoruchFlag')->label('Флаг о наличии поручителя')->dropDownList(['G' => 'G - Есть поручитель', 'N' => 'N - Нет поручителя'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'PoruchObesb')->label('Объем обязательства, обеспечиваемого поручительством')->dropDownList(['F' => 'F - Полностью', 'P' => 'P - Частично'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'PoruchSumm')->label('Сумма поручительства')->textInput(['placeholder' => '65000', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Тип счёта - потребительский кредит. Отношение к счёту - физическое лицо. (Усечено до 1 варианта)
                </div>
            </div>

            <!-- Код залога, Флаг о наличии поручителя, Объем обязательства, обеспечиваемого поручительством, Сумма поручительства, Полная стоимость кредита -->
            <div class="row">
                <div class="col-md-3">
                    <?=$form->field($model, 'DataOtsenkiStoimostiZaloga')->label('Дата оценки стоимости залога')->textInput(['placeholder' => 'ГГГГММДД', 'type' => 'string', 'maxlength' => 8])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'SrokDeystviyaDogovoraZaloga')->label('Срок действия договора залога')->textInput(['placeholder' => 'ГГГГММДД', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'OtsenochnayaStoimostZaloga')->label('Оценочная стоимость залога')->textInput(['placeholder' => '65000', 'type' => 'number'])->error(); ?>
                </div>
            </div>


        <!-- /ФОРМАТ Tutdf 3.0r -->
        </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <?=Html::submitButton('С Ф О Р М И Р О В А Т Ь !', ['class' => 'btn btn-primary', 'id' => 'submitFormCredScetRules']); ?>
        </div>
        <?php ActiveForm::end(); ?>

        <div class="col-md-4">
        </div>

        <div class="col-md-3">
            <?php
            Modal::begin([
                'header' => '
                    <h1 style="text-align: center">Искажение данных</h1>
                    &nbsp;&nbsp;&nbsp;&nbsp;В соответствии с требованиями законодательства, а также письмами и инструкциями ЦККИ ЦБ РФ, передаваемые в Бюро данные должны содержать точную информацию. Все счета, имеющие Дату открытия счета позже 31 мая 2006 года («Дата отсечения»), должны отвечать этому требованию. Счета, открытые до этой даты, допускают использование признаков отсутствия данных («заглушек») вместо самих данных, что означает, что поставщику эта информация неизвестна (например, место и дата выдачи документа, отчество заемщика и т.п.).<br><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;В качестве таких признаков необходимо передавать значения:<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;29990909 в полях формата D<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-9 в полях форматов N и S<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;UNKNOWN в полях форматов A, A/N, P, C<br><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Кроме того, при обработке полученных данных система производит контроль на отсутствие в них искажений. Искажением считается передача в некоторых полях (см. комментарии к полям ниже) условных слов и символов, указывающих, например на то, что данные отсутствуют или являются нереальными, - «Нет данных», «Отсутствует», «Тест», «Ошибка», «-» и т.п. При обнаружении таких значений (список может меняться) система их удалит. Соответственно, при удалении значения из обязательного поля будет отвергнут весь сегмент. Если сегмент является обязательным, то будет отвергнута запись полностью.<br>
                    Если информация по каким-либо необязательным полям отсутствует, следует оставлять эти поля пустыми и не заполнять их сообщениями об отсутствии данных.',
                'toggleButton' => ['label' => 'Раздел: ИСКАЖЕНИЕ ДАННЫХ', 'class' => 'btn btn-warning'],
            ]);
            echo 'Данные приведённые выше следует учитывать при заполнении формы...';
            Modal::end();
            ?>
        </div>
    </div>

</div>
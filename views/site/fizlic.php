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

$this->title = 'Формирование кредитной истории физических лиц. (Дифференцированный график)';
$form = ActiveForm::begin();
?>
<div class="site-index">

    <?php print_r($error); ?>

    <div class="row">
        <div class="col-md-12">

            <h4>Для того чтобы сформировать файлы выгрузки кредитной истории, необходимо выполнить три следующих действия:</h4>
            <!-- Шаги по созданию КИ -->
            <div class="row col-md-offset-0">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-success">
                        <input type="radio" name="options" id="option1">1) Заполнить форму данными субъекта
                    </label>
                    <label class="btn btn-default">
                        <input type="radio" name="options" id="option2">2) Проверить график погашений
                    </label>
                    <label class="btn btn-default">
                        <input type="radio" name="options" id="option4">3) Проверить корректность данных
                    </label>
                </div>
            </div>

            <p>&nbsp;</p>

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
                <p>Сегмент общих данных по физическому лицу</p>
                <footer>
                    Содержит общую информацию о физлице
                </footer>
            </blockquote>

            <!-- Фамилия Имя Отчество субъекта -->
            <div class="row">
                <div class="col-md-3">
                    <?=$form->field($model, 'PersonaFam')->label('Фамилия')->textInput(['placeholder' => 'Иванов'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'PersonaName')->label('Имя')->textInput(['placeholder' => 'Иван'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'PersonaOtc')->label('Отчество')->textInput(['placeholder' => 'Иванович'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Любое поле будет удалено, если передаваемые данные некорректны. См. раздел «Искажение данных»
                </div>
            </div>

            <!-- Дата и место рождения -->
            <div class="row">
                <div class="col-md-2">
                    <?=$form->field($model, 'PersonaBorn')->label('Дата рождения')->textInput(['placeholder' => 'ГГГГММДД', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-7">
                    <?=$form->field($model, 'PersonaBornHome')->label('Место рождения')->textInput(['placeholder' => 'Введите город или населённый пункт, как указано в паспорте'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Дата рождения в формате ГГГГММДД. <br>
                    Место рождения. Введите город или населённый пункт, как указано в паспорте. Допускается 1020 знаков.
                </div>
            </div>

            <hr>

            <blockquote>
                <p>Сегмент личных данных по физическому лицу</p>
                <footer>
                    Содержит персональную информацию о физическом лице
                </footer>
            </blockquote>

            <!-- ИНН -->
            <div class="row">
                <div class="col-md-9">
                    <?=$form->field($model, 'PersonaInn')->label('ИНН')->textInput(['placeholder' => '071601234567', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Индивидуальный номер налогоплательщика (ИНН). Для физ.лиц: 12 знаков
                </div>
            </div>

            <!-- Серия, номер паспорта, когда выдан, кем выдан -->
            <div class="row">
                <div class="col-md-1">
                    <?=$form->field($model, 'PasportSer')->label('Серия')->textInput(['placeholder' => '83 01', 'type' => 'text'])->error(); ?>
                </div>
                <div class="col-md-2">
                    <?=$form->field($model, 'PasportNumb')->label('Номер')->textInput(['placeholder' => '123456', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-2">
                    <?=$form->field($model, 'PasportDate')->label('Дата выдачи')->textInput(['placeholder' => 'ГГГГММДД', 'type' => 'number'])->error(); ?>
                </div>
                <div class="col-md-4">
                    <?=$form->field($model, 'PasportWhere')->label('Кем выдан')->textInput(['placeholder' => 'Название отделения внутренних дел МВД РФ или другого органа выдачи'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Введите серию без тире, но с пробелом, также номер и дату выдачи паспорта. Название отделения внутренних дел МВД РФ или другого органа выдачи.
                </div>
            </div>

            <hr>

            <blockquote>
                <p>Сегмент адресных данных по физическому лицу</p>
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
                <p>Сегмент телефонных данных по физическому лицу</p>
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
                    <?=$form->field($model, 'PersonaTel2Type')->label('Тип телефона №1')->dropDownList(['1' => 'Рабочий', '2' => 'Домашний', '3' => 'Факс', '4' => 'Сотовый', '5' => 'Другое'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Сегмент (сегменты) PN включает (включают) номер телефона субъекта кредитной истории.
                </div>
            </div>

            <hr>

            <blockquote>
                <p>Сегмент сделки</p>
                <footer>
                    При неправильном форматировании полей, запись отклоняется полностью
                </footer>
            </blockquote>

            <!-- Номер счёта, Тип счёта, Отношение к счёту -->
            <div class="row">
                <div class="col-md-3">
                    <?=$form->field($model, 'KredScet')->label('Номер счёта')->textInput(['placeholder' => '45407810900000000001', 'type' => 'string'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'KredScetType')->label('Тип счёта')->dropDownList(['09' => 'Потребительский кредит'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'KredScetOtn')->label('Отношение к счёту')->dropDownList(['1' => 'Физическое лицо'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Тип счёта - потребительский кредит. Отношение к счёту - физическое лицо. (Усечено до 1 варианта)
                </div>
            </div>

            <!-- Дата открытия счёта, Дата последней выплаты, Состояние счёта -->
            <div class="row">
                <div class="col-md-3">
                    <?=$form->field($model, 'KredOpenDate')->label('Дата открытия счёта')->textInput(['placeholder' => 'ГГГГММДД', 'type' => 'string', 'class' => 'form-control MonthCount KredOpenDate'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'KredLastPay')->label('Дата последней выплаты')->textInput(['placeholder' => 'ГГГГММДД', 'type' => 'string'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'KredScetActive')->label('Состояние счёта')->dropDownList(['13' => 'Счет закрыт'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Состояние счёта всегда равно 13. (Усечено до 1 варианта)
                </div>
            </div>

            <!-- Исходная сумма кредита, Баланс -->
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
<!--                <div class="col-md-3">
                    <?/*=$form->field($model, 'KredProc')->label('Процентная ставка по кред. дог.')->textInput(['placeholder' => '13', 'type' => 'number'])->error(); */?>
                </div>-->
                <div class="col-md-3">
                    Баланс = сумма кредита + проценты (%).<br>
                    Процентная ставка по кредитному договору (Как в графике платежей)
                </div>
            </div>

            <div class="row">
                <hr>
            </div>

            <!-- Рассчёт количества месяцев исходя из KredOpenDate и KredEndDate с помощью JS-->
            <div class="row">
                <div class="col-md-9">
                    <?=$form->field($model, 'operationType')->label('Тип операции: заведение КИ физ. лица')->textInput(['value' => 'fizlic', 'type' => 'hidden'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Количество месяцев рассчитывается автоматически при заполнении даты открытия счёта и даты окончания срока договора
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

            <!-- /ФОРМАТ Tutdf 4.0r -->
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

            <!-- /ФОРМАТ Tutdf 4.0r -->
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <?=Html::submitButton('С Ф О Р М И Р О В А Т Ь !', ['class' => 'btn btn-primary']); ?>
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
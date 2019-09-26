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

    <div class="row">
        <div class="col-md-12">

            <h4>Для того чтобы сформировать заявку по физическому лицу или ИП, необходимо выполнить два следующих действия:</h4>
            <!-- Шаги по созданию КИ -->
            <div class="row col-md-offset-0">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-success">
                        <input type="radio" name="options" id="option1">1) Заполнить форму данными субъекта
                    </label>
                    <label class="btn btn-default">
                        <input type="radio" name="options" id="option4">2) Проверить корректность данных
                    </label>
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
                    <?=$form->field($model, 'PersonaTel2Type')->label('Тип телефона №2')->dropDownList(['1' => 'Рабочий', '2' => 'Домашний', '3' => 'Факс', '4' => 'Сотовый', '5' => 'Другое'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Сегмент (сегменты) PN включает (включают) номер телефона субъекта кредитной истории.
                </div>
            </div>

            <hr>

            <blockquote>
                <p>Информационная часть</p>
                <footer>
                    При отсутствии или неправильном форматировании обязательного поля запись отклоняется полностью
                </footer>
            </blockquote>

            <!-- Номер договора поручительства, Дата заявки/Дата договора поручительства, Вид обязательства -->
            <div class="row">
                <div class="col-md-3">
                    <?=$form->field($model, 'NomerDogovoraPoruchitelstva')->label('Номер договора заёмщика / поручителя')->textInput(['placeholder' => '15/08/121/2', 'type' => 'string'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'DataZayavki')->label('Дата заявки/Дата договора поручительства')->textInput(['placeholder' => 'ГГГГММДД', 'type' => 'string'])->error(); ?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($model, 'operationType')->label('Тип операции: заявка')->textInput(['value' => 'zayavka', 'type' => 'hidden'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Тип займодавца - по умолчанию "1 – Кредитная организация"
                </div>
            </div>

            <!-- Тип запрошенного кредита, Дата последней выплаты, Состояние счёта -->
            <div class="row">
                <div class="col-md-3">
                    <?=$form->field($model, 'TipZaproshennogoKredita')->label('Тип запрошенного кредита')->dropDownList([
                        '401' => '401 - Нецелевой потреб.кредит, целевой без залога (кроме POS-кредитов) или на рефинансирование, до 1 года, до 30 000 RUB',
                        '402' => '402 - Нецелевой потреб.кредит, целевой без залога (кроме POS-кредитов) или на рефинансирование, до 1 года, от 30 000 до 100 000 RUB',
                        '403' => '403 - Нецелевой потреб.кредит, целевой без залога (кроме POS-кредитов) или на рефинансирование, до 1 года, от 100 000 до 300 000 RUB',
                        '404' => '404 - Нецелевой потреб.кредит, целевой без залога (кроме POS-кредитов) или на рефинансирование, до 1 года, свыше 300 000 RUB',
                        '405' => '405 - Нецелевой потреб.кредит, целевой без залога (кроме POS-кредитов) или на рефинансирование, свыше 1 года, до 30 000 RUB',
                        '406' => '406 - Нецелевой потреб.кредит, целевой без залога (кроме POS-кредитов) или на рефинансирование, свыше 1 года, от 30 000 до 100 000 RUB',
                        '407' => '407 - Нецелевой потреб.кредит, целевой без залога (кроме POS-кредитов) или на рефинансирование, свыше 1 года, от 100 000 до 300 000 RUB',
                        '408' => '408 - Нецелевой потреб.кредит, целевой без залога (кроме POS-кредитов) или на рефинансирование, свыше 1 года, свыше 300 000 RUB',
                        '501' => '501 - Ипотека',
                        '601' => '601 - Кредит для бизнеса',
                        '999' => '999 - Прочее',
                    ])->error(); ?>
                </div>
                <div class="col-md-6">
                    <?=$form->field($model, 'SrokOkonchaniyaDeystviyaOdobreniya')->label('Срок окончания действия одобрения')->textInput(['placeholder' => 'ГГГГММДД', 'type' => 'string'])->error(); ?>
                </div>
                <div class="col-md-3">
                    Убраны флаги отражающие задолженность / отказы / изменение наименования кредитного договора после одобрения
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <?=Html::submitButton('Перед тем как сформировать заявку - проверьте введённые данные!', ['class' => 'btn btn-primary']); ?>
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
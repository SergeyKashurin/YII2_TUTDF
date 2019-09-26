<?php
/**
 * Created by PhpStorm.
 * User: u92
 * Date: 18.01.2016
 * Time: 8:22
 */

namespace app\models;


use yii\base\Model;
use yii\web\UploadedFile;
use yii\helpers\Url;

# Подключаем библиотеку проверочных функций
use app\models\Ki24;

# setlocale (LC_ALL, 'ru_RU');

class Server extends Model
{
    /*
     *  Обязательный элемент
     */
    public $kiFile;

    /*
     * Элементы для выдачи
     */
    public $answerArr;

    /*
     *  Сегмент целиком
     */
    public $segment;

    public $line;
    public $fileName;

    /*
     * Возможные шаблоны для проверки корректности кредитного номера договора
     */
    public $dogovorNumber = 'YOURPATTERN';

    /*
     * Массив ошибок сегмента
     */
    public $segmentError;

    /*
     * Флаг для признака физического лица
     */
    public $flagFiz;

    /*
     * Массив для условий
     */
    public $attrib = array();

    public static function tableName()
    {
        return 'server';
    }

    public function rules()
    {
        return [
            [['kiFile'], 'file', 'skipOnEmpty' => false, 'extensions' => ''],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Загрузите валидный текстовый файл содержащий кредитные истории',
        ];
    }

    public function upload()
    {
        $this->fileName = 'KI/NAME_'.date('Ymd_His');

        if ($this->validate()) {

            #$file = 'KI/' . $this->kiFile->baseName . '.' . $this->kiFile->extension;
            $file = $this->fileName;

            $this->kiFile->saveAs($file);

            $this->reNewFile($file);
            sleep(2);

            return $this->readKiFile($file);
        } else {
            return false;
        }
    }

    public function readKiFile($fName)
    {

        #$this->reNewFile($fName);

        $in_i = 1;
        $in_out = 0;

        $handle = @fopen($fName, "r");
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {

                $arr = explode("\t", iconv("cp1251", "utf-8", trim(preg_replace("/[ ]{1,}/", " ", $buffer))));

                if($arr[0] === 'TUTDF') { $stroke = $this->TUTDF($arr); $arr[255] = $stroke; }

                if($arr[0] === 'ID01' || $arr[0] === 'ID02' || $arr[0] === 'ID03') { $stroke = $this->ID($arr); $arr[255] = $stroke; }
                if($arr[0] === 'NA01') { $stroke = $this->NA($arr); $arr[255] = $stroke; $name = $arr[1].' '.$arr[3].' '.$arr[2]; }
                if($arr[0] === 'BU01') { $stroke = $this->BU($arr); $arr[255] = $stroke; $name = $arr[1]; }
                if($arr[0] === 'AD01' || $arr[0] === 'AD02') { $stroke = $this->AD($arr, $name); $arr[255] = $stroke; }

                if($arr[0] === 'PN01' || $arr[0] === 'PN02' || $arr[0] === 'PN03' || $arr[0] === 'PN04' || $arr[0] === 'PN05') { $stroke = $this->PN($arr); $arr[255] = $stroke; }
                if($arr[0] === 'PN06') { $stroke = 'Лишний сегмент PN'; $arr[255] = $stroke; }

                if($arr[0] === 'CL01' || $arr[0] === 'CL02' || $arr[0] === 'CL03' || $arr[0] === 'CL04' || $arr[0] === 'CL05' || $arr[0] === 'CL06' || $arr[0] === 'CL07') { $stroke = $this->CL($arr); $arr[255] = $stroke; }
                if($arr[0] === 'GR01' || $arr[0] === 'GR02' || $arr[0] === 'GR03' || $arr[0] === 'GR04' || $arr[0] === 'GR05' || $arr[0] === 'GR06' || $arr[0] === 'GR07') { $stroke = $this->GR($arr); $arr[255] = $stroke; }

                if($arr[0] === 'LE01') { $stroke = $this->LE($arr); $arr[255] = $stroke; }

                if($arr[0] === 'TR01') { $stroke = $this->TR($arr); $arr[255] = $stroke; $schet = $arr[2]; }

                if($arr[0] === 'IP01') { $stroke = $this->IP($arr); $arr[255] = $stroke; $schet = strlen($arr[2]) > 5 ? $arr[2] : $arr[14]; }

                if($arr[0] === "ID01")
                {
                    if(strlen($name) > 5)
                        $this->answerArr[$in_i]['info'] = $name;

                    if(strlen($schet) > 5)
                        $this->answerArr[$in_i]['info'] .= '&'.$schet;

                    $in_i++;
                    $in_out = 1;
                }

                # Добавляем к каждой строке нулевой элемент с заглушкой на случай ошибок
                array_unshift($arr, "-1");

                $this->answerArr[$in_i][$in_out] = $arr;
                $in_out++;
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }

        return isset($this->answerArr) ? $this->answerArr : false;
    }

    /*
     * Функция исправляет базовые ошибки в телефонных номерах и заменяет оригинальный файл
     */
    /**
     * @param $fName
     */
    public function reNewFile($fName) {

        $count = 0;

        $phones = ([]);

        $handle = @fopen($fName, "r");
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                $arr = explode("\t", trim(preg_replace("/[ ]{1,}/", " ", $buffer)));
                $count++;

                /*
                 * TUTDF
                 */
                if($arr[0] === 'TUTDF') {
                    for ($i = 0; $i <= 7; $i++) {
                        $line[] = $i < 7 ? $arr[$i]."\t" : $arr[$i];

                        if ($i === 7)
                            $line[] = "\r\n";
                    }
                }

                /*
                 * ID
                 */
                if(substr($arr[0], 0, 2) === 'ID') {
                    for($i = 0; $i <= 7; $i++){
                        $line[] = $i < 7 ? $arr[$i]."\t" : $arr[$i];

                        if ($i === 7)
                            $line[] = "\r\n";
                    }
                }

                /*
                 * NA
                 */
                if(substr($arr[0], 0, 2) === 'NA') {
                    for($i = 0; $i <= 12; $i++){
                        $line[] = $i < 12 ? trim($arr[$i])."\t" : trim($arr[$i]);

                        if ($i === 12)
                            $line[] = "\r\n";
                    }
                }

                /*
                 * BU
                 */
                if(substr($arr[0], 0, 2) === 'BU') {
                    for($i = 0; $i <= 24; $i++){
                        $line[] = $i < 24 ? trim($arr[$i])."\t" : trim($arr[$i]);

                        if ($i === 24)
                            $line[] = "\r\n";
                    }
                }

                /*
                 * AD
                 */
                if(substr($arr[0], 0, 2) === 'AD') {
                    for($i = 0; $i <= 15; $i++){
                        $line[] = $i < 15 ? $arr[$i]."\t" : $arr[$i];

                        if ($i === 15)
                            $line[] = "\r\n";
                    }
                }

                /*
                 * PN
                 */
                if(substr($arr[0], 0, 2) === 'PN') {
                    for($i = 0; $i <= 2; $i++){

                        if($i === 1) {
                            $line[] = isset($phones[$arr[$i]]) ? $phones[$arr[$i]]."\t" : $arr[$i]."\t";
                        }

                        if ($i === 2)
                            $line[] = $arr[$i]."\r\n";

                        if ($i === 0)
                            $line[] = $arr[$i]."\t";
                    }
                }

                /*
                 * CL
                 */
                if(substr($arr[0], 0, 2) === 'CL') {
                    for($i = 0; $i <= 6; $i++){
                        $line[] = $i < 6 ? $arr[$i]."\t" : $arr[$i];

                        if ($i === 6)
                            $line[] = "\r\n";
                    }
                }

                /*
                 * GR
                 */
                if(substr($arr[0], 0, 2) === 'GR') {
                    for($i = 0; $i <= 5; $i++){
                        $line[] = $i < 5 ? $arr[$i]."\t" : $arr[$i];

                        if ($i === 5)
                            $line[] = "\r\n";
                    }
                }

                /*
                 * TR
                 */
                if(substr($arr[0], 0, 4) === 'TR01') {
                    for($i = 0; $i <= 41; $i++){
                        $line[] = $i < 41 ? $arr[$i]."\t" : $arr[$i];

                        if ($i === 41)
                            $line[] = "\r\n";
                    }
                }

                /*
                 * LE
                 */
                if(substr($arr[0], 0, 2) === 'LE') {
                    for($i = 0; $i <= 7; $i++){
                        $line[] = $i < 7 ? $arr[$i]."\t" : $arr[$i];

                        if ($i === 7)
                            $line[] = "\r\n";
                    }
                }

                /*
                 * IP
                 */
                if(substr($arr[0], 0, 2) === 'IP') {
                    for($i = 0; $i <= 18; $i++){
                        $line[] = $i < 18 ? $arr[$i]."\t" : $arr[$i];

                        if ($i === 18)
                            $line[] = "\r\n";
                    }
                }

                /*
                 * TRLR
                 */
                if($arr[0] === 'TRLR') {
                    for($i = 0; $i <= 1; $i++){

                        if ($i === 1){
                            $line[] = $count;
                            $line[] = "\r\n";
                        } else $line[] = $i < 1 ? $arr[$i]."\t" : $arr[$i];

                    }
                }

            }
        }

        file_put_contents($this->fileName, $line);
    }

    /*
     *  TUTDF
     *  Проверка сегмента
     */
    public function TUTDF($segment) {

        /* Версия (Version) */
        if (trim($segment[1]) !== '4.0R') { $segmentError[] = '[2] Неверный формат TUTDF'; }

        /* Дата опубликования версии (Version Date) */
        if (trim($segment[2]) !== '20150701') { $segmentError[] = '[3] Дата опубликования версии неверна '; }

        /* Имя пользователя (Member Code) */
        if (trim($segment[3]) !== 'NAME') { $segmentError[] = '[4] Имя пользователя указано неверное'; }

        /* Идентификация цикла (Cycle Identification) */
        if (trim($segment[4]) !== '1') { $segmentError[] = '[5] Идентификация цикла неверен'; }

        /* Дата составления отчёта (Reported Date) */ #!!! 20160812
        if (trim($segment[5]) !== date("Ymd")) { $segmentError[] = '[6] Дата составления отчёта неверна'; }

        /* Пароль (Authorization Code) */
        if (trim($segment[6]) !== 'CODE') { $segmentError[] = '[7] Пароль неверен'; }

        /* Данные участника (Member data) */
        if (trim($segment[7]) !== 'Очередная пачка обновлений для НБКИ') { $segmentError[] = '[8] Данные участника неверны'; }

        #array_unshift($segmentError, 'TEST');

        return isset($segmentError) ? $segmentError : false;
    }

    /*
     *  ID
     *  Проверка сегмента
     */
    public function ID($segment) {

        /* Индивидуальный номер налогоплательщика (ИНН) */
        if(Server::sTrim($segment[1]) === "81") {

            /* Номер серии (Series Number) */
            if (Server::sTrim($segment[2]) !== '') { $segmentError[] = '[3] Номер серии должен быть пустым'; }

            /* Номер документа (ID Number) */
            if (strlen(Server::sTrim($segment[3])) !== 10 and strlen(Server::sTrim($segment[3])) !== 12) { $segmentError[] = '[4] Номер ИНН не может быть '.strlen($segment[3]).' знаков'.strlen(trim($segment[4])); }

            /* Остальные поля */
            if (strlen(Server::sTrim($segment[4])) === 0 && strlen(Server::sTrim($segment[5])) === 0 && strlen(Server::sTrim($segment[6])) === 0) {} else { $segmentError[] = '[5-7] Поля не заполняются'; }
        }

        /* Паспорт гражданина РФ */
        elseif(Server::sTrim($segment[1]) === "21") {

            $this->flagFiz[0] = 'fiz';

            /* Номер серии (Series Number) */
            if (preg_match("/^([0-9]{2} [0-9]{2}|[0-9]{4})$/i", Server::sTrim($segment[2])) === 0) { $segmentError[] = '[3] Номер серии паспорта некорректен'; }

            /* Номер документа (ID Number) */
            if (preg_match("/^[0-9]{6}$/i", Server::sTrim($segment[3])) === 0) { $segmentError[] = '[4] Номер паспорта не может быть '.strlen($segment[3]).' знаков'; }

            /* Когда выдан (Issue Date) */
            if (preg_match("/^[0-9]{8}$/i", Server::sTrim($segment[4])) === 0) { $segmentError[] = '[5] Дата выдачи документа некорректна'; }

            /* Кем выдан (Issue Authority) */
            if (!preg_match("/^[А-Я \-,\. 0-9№\"]{15,150}$/ui", Server::sTrim($segment[5]))) { $segmentError[] = '[6] Поле "Кем выдан" содержит недопустимые символы '.$segment[5]; }

            /* Остальные поля */
            if (Server::sTrim($segment[6]) !== '' || Server::sTrim($segment[7]) !== '') { $segmentError[] = '[6-7] Поля не заполняются'; }
        }

        /* Регистрационный номер предпринимателя */
        elseif(Server::sTrim($segment[1]) === "33") {

            /* Номер документа (ID Number) */
            if (preg_match("/^[0-9]{15}$/i", Server::sTrim($segment[3])) === 0) { $segmentError[] = '[4] Регистрационный номер предпринимателя не может быть '.strlen($segment[3]).' знаков'; }

            /* Когда выдан (Issue Date) */
            if (preg_match("/^[0-9]{8}$/i", Server::sTrim($segment[4])) === 0) { $segmentError[] = '[5] Когда выдан '.strlen($segment[4]).' знаков вместо 8'; }

            /* Остальные поля */
            if (Server::sTrim($segment[2]) !== '' || Server::sTrim($segment[5]) !== '' || Server::sTrim($segment[6]) !== '' || Server::sTrim($segment[7])) { $segmentError[] = '[5-7] Поля не заполняются'; }
        }

        /* ОГРН */
        elseif(Server::sTrim($segment[1]) === "34") {

            /* Номер документа (ID Number) */
            if (preg_match("/^[0-9]{13}$/i", Server::sTrim($segment[3])) === 0) { $segmentError[] = '[4] ОГРН не может быть '.strlen($segment[3]).' знаков'; }

            /* Когда выдан (Issue Date) */
            if (preg_match("/^[0-9]{8}$/i", Server::sTrim($segment[4])) === 0) { $segmentError[] = '[5] Когда выдан '.strlen($segment[4]).' знаков вместо 8'; }

            /* Остальные поля */
            if (Server::sTrim($segment[2]) !== '' || Server::sTrim($segment[5]) !== '' || Server::sTrim($segment[6]) !== '' || Server::sTrim($segment[7])) { $segmentError[] = '[5-7] Поля не заполняются'; }
        }

        /* Номер карточки обязательного пенсионного страхования (СНИЛС) */
        elseif(Server::sTrim($segment[1]) === "32") {

            $this->flagFiz[1] = 'snils';

            /* Номер карточки обязательного пенсионного страхования (СНИЛС) */
            if (preg_match("/^[0-9]{11}$/i", Server::sTrim($segment[3])) === 0) { $segmentError[] = '[4] Номер карточки обязательного пенсионного страхования (СНИЛС) не может быть '.strlen($segment[3]).' знаков'; }

            /* Остальные поля */
            if (Server::sTrim($segment[2]) !== '' || Server::sTrim($segment[4]) !== '' || Server::sTrim($segment[5]) !== '' || Server::sTrim($segment[6]) !== '' || Server::sTrim($segment[7])) { $segmentError[] = '[3,5-7] Поля не заполняются'; }
        }

        /* Если нет СНИЛС, то появляется сегмент ID97 [ЗАГЛУШКА] */
        elseif(Server::sTrim($segment[1]) === "97") {
        } else { $segmentError[] = 'НЕИЗВЕСТНЫЙ СЕГМЕНТ ID '.gettype($segment[1]); }
        return isset($segmentError) ? $segmentError : false;
    }

    /*
     *  NA01
     *  Содержит данные о частном лице
     */
    public function NA($segment) {

        /* Раскоментить чтобы узнать у кого из выгрузки нету СНИЛСА
        if($this->flagFiz[0] === 'fiz' && $this->flagFiz[1] !== 'snils') { $segmentError[] = '(СНИЛС) отсутствует!'; }
        $this->flagFiz[0] = ''; $this->flagFiz[1] = ''; */

        /* Фамилия (Surname) */
        if (!preg_match('/^[А-ЯЁ]{2,60}$/ui', Server::sTrim($segment[1]))) { $segmentError[] = '[2] Фамилия указана некорректно '.$segment[1]; }

        /* Отчество (Patronymic Name) */
        if (!preg_match('/^[А-Я -Ё]{0,60}$/ui',Server::sTrim($segment[2]))) { $segmentError[] = '[3] Отчество указано некорректно'; }

        /* Имя (First Name) */
        if (!preg_match('/^[А-ЯЁ]{2,60}$/ui',Server::sTrim($segment[3]))) { $segmentError[] = '[4] Имя указано некорректно'; }

        /* Дата рождения (Date of Birth) */
        if (preg_match('/^[0-9]{8}$/', Server::sTrim($segment[5])) === 0) { $segmentError[] = '[6] Дата рождения некорректна'; }

        /* Место рождения (Place of Birth) */
        if (!preg_match('/^[А-Я \\\.\-,0-9\/ЙЁ\"№I-]{5,150}$/ui', Server::sTrim($segment[6]))) { $segmentError[] = '[7] Поле "Место рождения" содержит недопустимые символы'; }

        /* Поле не используется. */
        if (!preg_match('/^([RU]{2}|[ ]{0})$/ui', Server::sTrim($segment[7]))) { $segmentError[] = '[8] Поле должно содержать значение RU либо оставаться пустым'; }

        /* Остальные поля */
        if (Server::sTrim($segment[4]) !== '' || Server::sTrim($segment[8]) !== '' || Server::sTrim($segment[9]) !== '' || Server::sTrim($segment[10]) !== '' || Server::sTrim($segment[11]) !== '' || Server::sTrim($segment[12]) !== '') { $segmentError[] = '[4,8,9-12] Поля не заполняются'; }

        return isset($segmentError) ? $segmentError : false;
    }

    /*
     *  BU01
     *  Содержит данные о юридическом лице
     */
    public function BU($segment) {

        /* Название предприятия (Business Name) */
        if (!preg_match('/^[А-Я \-0-9\"\.]{20,1020}$/ui', $segment[1])) { $segmentError[] = '[2] Название предприятия указана некорректно '.$segment[1]; }

        /* Дата регистрации (Registration Date) */
        if (!preg_match('/^[0-9]{8}$/',Server::sTrim($segment[2]))) { $segmentError[] = '[3] Дата регистрации некорректна'; }

        /* Статус предприятия (Business Status) */
        if (!preg_match('/^[0-5]{1}$/ui',Server::sTrim($segment[3]))) { $segmentError[] = '[4] Статус предприятия некорректен'; }

        /* Дата определения статуса (Date of Status) */
        if (Server::sTrim($segment[3]) === '0' && strlen(Server::sTrim($segment[4])) > 0) { $segmentError[] = '[5] В данном случае дата определения статуса не заполняется'; }
        if (Server::sTrim($segment[3]) > 0 && !preg_match('/^[0-9]{8}$/', Server::sTrim($segment[4]))) { $segmentError[] = '[5] Дата определения статуса не соответствует правилам заполнения'; }

        /* ОКПО (OKPO) */
        if (!preg_match('/^([0-9]{8}|[ ]{0})$/', Server::sTrim($segment[5]))) { $segmentError[] = '[6] Код ОКПО некорректен'; }

        /* ОКОНХ (OKONH) */
        #if (preg_match('/^[0-9]{8}$/', Server::sTrim($segment[6])) === 0) { $segmentError[] = '[7] Код ОКОНХ некорректен'; }

        /* ОКВЭД (OKVED) */
        if (!preg_match('/^[0-9\.]{4,8}$/', Server::sTrim($segment[7]))) { $segmentError[] = '[8] Коды ОКВЭД некорректны'; }

        /* OKATO (OKATO) */
        if (!preg_match('/^[0-9]{0,11}$/', Server::sTrim($segment[8]))) { $segmentError[] = '[9] Код ОКАТО некорректен'; }

        /* ОКОГУ (OKOGU) */
        if (!preg_match('/^([0-9]{5}|[0-9]{7}|[ ]{0})$/', Server::sTrim($segment[9]))) { $segmentError[] = '[10] Код ОКОГУ некорректен'; }

        /* ОКФС (OKFS) */
        if (!preg_match('/^[0-9]{0,2}$/', Server::sTrim($segment[10]))) { $segmentError[] = '[11] Код ОКФС некорректен'; }

        /* ОКОПФ (OKOPF) */
        if (!preg_match('/^([0-9]{2}|[0-9]{5}|[ ]{0})$/', Server::sTrim($segment[11]))) { $segmentError[] = '[12] Код ОКОПФ некорректен'; }

        /* Код причины постановки на учет (КПП) (Tax Registration Reason (KPP) */
        if (!preg_match('/^([0-9]{9}|[ ]{0})$/', Server::sTrim($segment[12]))) { $segmentError[] = '[13] КПП некорректен'; }

        /* Сокращенное наименование предприятия (Abbreviated Business Name) */
        if (!preg_match('/^[А-Я  \-0-9\"\.]{5,255}$/ui', Server::sTrim($segment[16]))) { $segmentError[] = '[17] Поле "Сокращенное наименование предприятия" содержит недопустимые символы'; }

        /* Наименование на иностранном языке (Foreign language name) */
        if (!preg_match('/^([A-Z \"]{5,255}|[ ]{0})$/ui', Server::sTrim($segment[24]))) { $segmentError[] = '[25] Наименование на иностранном языке содержит недопустимые символы'; }

        /* Остальные поля */
        if (Server::sTrim($segment[13]) !== '' || Server::sTrim($segment[14]) !== '' || Server::sTrim($segment[15]) !== '') { $segmentError[] = '[14-16] Поля не заполняются'; }
        if (Server::sTrim($segment[17]) !== '' || Server::sTrim($segment[18]) !== '' || Server::sTrim($segment[19]) !== '' || Server::sTrim($segment[20]) !== '' || Server::sTrim($segment[21]) !== '' || Server::sTrim($segment[22]) !== '' || Server::sTrim($segment[23]) !== '') { $segmentError[] = '[18-24] Поля не заполняются'; }

        return isset($segmentError) ? $segmentError : false;
    }

    /*
     *  AD01
     *  Включает в себя известные адреса субъектов
     */
    public function AD($segment, $name) {

        /* Тип адреса (Address Type) */
        if (preg_match('/^[0-9]{1}$/', Server::sTrim($segment[1])) === 0) { $segmentError[] = '[2] Тип адреса должен состоять из одной цифры'; }

        /* Почтовый индекс (Postal Code) */
        if (preg_match('/^[0-9]{6}$/', Server::sTrim($segment[2])) === 0) { $segmentError[] = '[3] Почтовый индекс должен состоять из шести цифр ('.$segment[2].')'; }

        /* Страна (Country) */
        if (Server::sTrim($segment[3]) !== 'RU') { $segmentError[] = '[4] Страна должна быть RU'; }

        /* Регион (Region) */
        if (preg_match('/^([0-9- ]{2}|[ ]{0})$/', Server::sTrim($segment[4])) === 0) { $segmentError[] = '[5] Регион некорректен'; }

        /* Район (District) */
        if (!preg_match('/^[А-Я -]{0,80}$/ui', Server::sTrim($segment[6])) || Server::sTrim($segment[6]) === 'р-н') { $segmentError[] = '[7] Район указан некорректно'; }

        /* Местоположение (Location) */
        if (!preg_match('/^[А-Я -\/]{2,80}$/ui', Server::sTrim($segment[7]))) { $segmentError[] = '[8] Местоположение указано некорректно'; }

        /* Улица (Street) */
        if (!preg_match('/^[А-Я \.0-9\"-\/]{3,80}$/ui', Server::sTrim($segment[9]))) { $segmentError[] = '[10] Улица указана некорректно'; }

        /* Номер дома (House Number) */
        if (preg_match('/^[0-9\/А-Я \-\""]{0,40}$/', Server::sTrim($segment[10])) === 0 && $name !== 'Общество с ограниченной ответственностью "Чайный мир"' && $name !== 'ОБЩЕСТВО С ОГРАНИЧЕННОЙ ОТВЕТСТВЕННОСТЬЮ "БАЗИС.РУ"' && $segment[9] !== 'в/ч') { $segmentError[] = '[11] Номер дома заполнен некорректно ('.$segment[10].')'; }

        /* Корпус (Block) */
        if (preg_match('/^[0-9А-Я]{0,10}$/', Server::sTrim($segment[11])) === 0) { $segmentError[] = '[12] Корпус заполнен некорректно'; }

        /* Строение (Building) */
        if (preg_match('/^[0-9]{0,20}$/', Server::sTrim($segment[12])) === 0) { $segmentError[] = '[13] Строение заполнено некорректно'; }

        /* Квартира (Apartment) */
        if (!preg_match('/^[0-9а-я \/]{0,40}$/ui', Server::sTrim($segment[13]))) { $segmentError[] = '[14] Квартира заполнена некорректно'; }


        /* Остальные поля */
        if (Server::sTrim($segment[5]) !== '' || Server::sTrim($segment[8]) !== '' || Server::sTrim($segment[14]) !== '' || Server::sTrim($segment[15]) !== '') { $segmentError[] = '[6,9,15-16] Поля не заполняются'; }

        return isset($segmentError) ? $segmentError : false;
    }

    /*
     *  PN
     *  Включает номер телефона субъекта кредитной истории 
     */
    public function PN($segment) {

        /* Номер (Number) */
        if (preg_match('/^((78|79|74)[0-9]{9}|78662[0-9]{5})$/', Server::sTrim($segment[1])) === 0) { $segmentError[] = '[2] Номер телефона некорректен ('.$segment[1].')'; }

        /* Тип (Type) */
        if (preg_match('/^[1-5]{1}$/', Server::sTrim($segment[2])) === 0) { $segmentError[] = '[3] Тип номер телефона некорректен'; }

        return isset($segmentError) ? $segmentError : false;
    }

    /*
     *  CL
     *  Используется только для передачи информации о дополнительных залогах.
     */
    public function CL($segment) {

        /* Идентификатор залога (Collateral agreement number) */
        if (preg_match($this->dogovorNumber, Server::sTrim($segment[1])) === 0) { $segmentError[] = '[2] Идентификатор залога не совпадает с шаблоном'; }

        /* Код залога (Collateral Code) */
        if (preg_match('/^[0-9]{2}$/', Server::sTrim($segment[2])) === 0) { $segmentError[] = '[3] Код залога неверен'; }

        /* Оценочная стоимость залога (Collateral value) */
        if (preg_match('/^[0-9]{3,10}$/', Server::sTrim($segment[3])) === 0) { $segmentError[] = '[4] Оценочная стоимость залога некорректна'; }

        /* Дата оценки стоимости залога (Collateral date) */
        if (preg_match('/^[0-9]{8}$/', Server::sTrim($segment[4])) === 0) { $segmentError[] = '[5] Дата оценки залога некорректна'; }

        /* Срок действия договора залога (Collateral agreement expiration date) */
        if (preg_match('/^[0-9]{8}$/', Server::sTrim($segment[5])) === 0) { $segmentError[] = '[6] Срок действия договора залога некорректна'; }

        return isset($segmentError) ? $segmentError : false;
    }

    /*
     *  GR
     *  Используется только для передачи данных о дополнительных поручителях по кредиту
     */
    public function GR($segment) {

        /* Идентификатор поручительства (Guarantee agreement number) */
        if (preg_match($this->dogovorNumber, Server::sTrim($segment[1])) === 0) { $segmentError[] = '[2] Идентификатор поручительства не совпадает с шаблоном'; }

        /* Объем обязательства, обеспечиваемого поручительством (Volume of debt secured by guarantee) */
        if (Server::sTrim($segment[2]) !== "F") { $segmentError[] = '[3] Объем обязательства некорректен'; }

        /* Сумма поручительства (Guarantee sum) */
        if (preg_match('/^[0-9]{3,10}$/', Server::sTrim($segment[3])) === 0) { $segmentError[] = '[4] Сумма поручительства некорректна'; }

        /* Срок поручительства (Guarantee term) */
        if (preg_match('/^[0-9]{8}$/', Server::sTrim($segment[4])) === 0) { $segmentError[] = '[5] Срок поручительства некорректна'; }

        return isset($segmentError) ? $segmentError : false;
    }

    /*
     *  LE01
     *  Содержит только информацию по судебным решениям, в том числе касательно банкротства физлиц
     */
    public function LE($segment) {

        /* Номер иска (Claim Number) */
        if (preg_match('/^[0-9 -\/]{5,35}$/ui', Server::sTrim($segment[1])) === 0) { $segmentError[] = '[2] Номер иска указан некорректно'; }

        /* Наименование суда (Court Name) */
        if (!preg_match('/^[А-Я ]{20,170}$/ui', Server::sTrim($segment[2]))) { $segmentError[] = '[3] Наименование суда некорректно'; }

        /* Дата отчёта (Date Reported) */
        if (preg_match('/^[0-9]{8}$/', Server::sTrim($segment[3])) === 0) { $segmentError[] = '[4] Дата отчёта некорректна'; }

        /* Дата исполнения (Date Consideration */
        if (preg_match('/^([0-9]{8}|[ ]{0})$/', Server::sTrim($segment[4])) === 0) { $segmentError[] = '[5] Дата исполнения некорректна'; }

        /* Дата возмещения (Date Satisfied) */
        if (preg_match('/^([0-9]{8}|[ ]{0})$/', Server::sTrim($segment[5])) === 0) { $segmentError[] = '[6] Дата возмещения некорректна'; }

        /* Истец (Plaintiff) */
        if (!preg_match('/^([А-Я - \/]{5,32}|[ ]{0})$/ui', Server::sTrim($segment[6]))) { $segmentError[] = '[7] Истец указан некорректно'; }

        /* Решение (Resolution) */
        if (!preg_match('/^([А-Я0-9 -\/№,\.]{5,500}|[ ]{0})$/ui', Server::sTrim($segment[7]))) { $segmentError[] = '[8] Решение некорректно'; }

        return isset($segmentError) ? $segmentError : false;
    }

    /*
     *  TR
     *  Содержит историю кредита клиента
     */
    public function TR($segment) {

        unset($attrib);

        $attrib = ([
            'MemberCode' =>                                Server::sTrim($segment[1]),  /* Имя пользователя */
            'AccountNumber' =>                             Server::sTrim($segment[2]),  /* Номер счета */
            'AccountType' =>                               Server::sTrim($segment[3]),  /* Тип счёта */
            'AccountRelationship' =>                       Server::sTrim($segment[4]),  /* Отношение к счёту */
            'DateAccountOpened' =>                         Server::sTrim($segment[5]),  /* Дата открытия счёта */
            'DateOfLastPayment' =>                         Server::sTrim($segment[6]),  /* Дата последней выплаты */
            'AccountRating' =>                             Server::sTrim($segment[7]),  /* Состояние счёта */
            'DateAccountRating' =>                         Server::sTrim($segment[8]),  /* Дата состояния счёта */
            'DateReported' =>                              Server::sTrim($segment[9]),  /* Дата составления отчёта */
            'CreditLimit-ContractAmount' =>                Server::sTrim($segment[10]), /* Лимит кредита/ Исходная сумма кредита */
            'Balance' =>                                   Server::sTrim($segment[11]), /* Баланс (Balance) */
            'PastDue' =>                                   Server::sTrim($segment[12]), /* Просрочка */
            'NextPayment' =>                               Server::sTrim($segment[13]), /* Следующий платеж */
            'CreditPaymentFrequency' =>                    Server::sTrim($segment[14]), /* Частота выплат */
            'MOP' =>                                       Server::sTrim($segment[15]), /* Своевременность платежей */
            'CurrencyCode' =>                              Server::sTrim($segment[16]), /* Код валюты */
            'CollateralCode' =>                            Server::sTrim($segment[17]), /* Код залога */
            'DateOfContractTermination' =>                 Server::sTrim($segment[18]), /* Дата окончания срока договора */
            'DatePaymentDue' =>                            Server::sTrim($segment[19]), /* Дата финального платежа */
            'DateInterestPaymentDue' =>                    Server::sTrim($segment[20]), /* Дата финальной выплаты процентов */
            'InterestPaymentFrequency' =>                  Server::sTrim($segment[21]), /* Частота выплат процентов */
            'OldMemberCode' =>                             Server::sTrim($segment[22]), /* Старое имя пользователя */
            'OldAccountNumber' =>                          Server::sTrim($segment[23]), /* Старый номер счета */
            'AmountOutstanding' =>                         Server::sTrim($segment[24]), /* Текущая задолженность */
            'GuarantorIndicator' =>                        Server::sTrim($segment[25]), /* Флаг о наличии поручителя */
            'VolumeOfDebtSecuredByGuarantee' =>            Server::sTrim($segment[26]), /* Объем обязательства, обеспечиваемого поручительством */
            'GuaranteeSum' =>                              Server::sTrim($segment[27]), /* Сумма поручительства */
            'GuaranteeTerm' =>                             Server::sTrim($segment[28]), /* Срок поручительства */
            'BankGuaranteeIndicator' =>                    Server::sTrim($segment[29]), /* Флаг о наличии банковской гарантии */
            'VolumeOfDebtSecuredByBankGuarantee' =>        Server::sTrim($segment[30]), /* Объем обязательства, обеспечиваемого банковской гарантией */
            'BankGuaranteeSum' =>                          Server::sTrim($segment[31]), /* Сумма банковской гарантии */
            'BankGuaranteeTerm' =>                         Server::sTrim($segment[32]), /* Срок банковской гарантии */
            'CollateralValue' =>                           Server::sTrim($segment[33]), /* Оценочная стоимость залога */
            'CollateralDate' =>                            Server::sTrim($segment[34]), /* Дата оценки стоимости залога */
            'CollateralAgreementExpirationDate' =>         Server::sTrim($segment[35]), /* Срок действия договора залога */
            'OverallValueOfCredit' =>                      Server::sTrim($segment[36]), /* Полная стоимость кредита */
            'RightOfClaimAcquirerNames' =>                 Server::sTrim($segment[37]), /* Наименования приобретателя права требования */
            'RightOfClaimAcquirerRegistrationData' =>      Server::sTrim($segment[38]), /* Идентификационные данные приобретателя права требования */
            'RightOfClaimAcquirerTaxpayerID' =>            Server::sTrim($segment[39]), /* ИНН приобретателя права требования */
            'RightOfClaimAcquirerSocialInsuranceNumber' => Server::sTrim($segment[40]), /* СНИЛС приобретателя права требования */
            'FinishDate' =>                                Server::sTrim($segment[41]), /* Дата фактического исполнения обязательств в полном объеме */
        ]);

        /* Группа проверки синтаксиса */
        if(isset($attrib)) {

            /* Имя пользователя (Member Code) */
            if ($attrib['MemberCode'] !== 'NAME') {
                $segmentError[] = '[2] Имя пользователя неверно';
            }

            /* Номер счета (Account Number) */
            if (preg_match($this->dogovorNumber, $attrib['AccountNumber']) === 0) {
                $segmentError[] = '[3] Номер договора не совпадает с шаблоном';
            }

            /* Тип счёта (Account Type) */
            if (preg_match('/^([0-9]{0}|[0-9]{2})$/', $attrib['AccountType']) === 0) {
                $segmentError[] = '[4] Тип счёта некорректен';
            }

            /* Отношение к счёту (Account Relationship) */
            if (preg_match('/^[0-9]{1}$/', $attrib['AccountRelationship']) === 0) {
                $segmentError[] = '[5] Отношение к счёту некорректно';
            }

            /* Дата открытия счёта (Date Account Opened) */
            if (preg_match('/^([0-9]{0}|[0-9]{8})$/', $attrib['DateAccountOpened']) === 0) {
                $segmentError[] = '[6] Дата открытия счёта некорректна';
            }

            /* Дата последней выплаты (Date Of Last Payment) */
            if (preg_match('/^([0-9]{0}|[0-9]{8})$/', $attrib['DateOfLastPayment']) === 0) {
                $segmentError[] = '[7] Дата последней выплаты некорректна';
            }

            /* Состояние счёта (Account Rating) */
            if (preg_match('/^([0-9]{0}|[0-9]{2})$/', $attrib['AccountRating']) === 0) {
                $segmentError[] = '[8] Состояние счёта некорректно';
            }

            /* Дата состояния счёта (Date Account Rating) */
            if (preg_match('/^([0-9]{0}|[0-9]{8})$/', $attrib['DateAccountRating']) === 0) {
                $segmentError[] = '[9] Дата состояния счёта некорректна';
            }

            /* Дата составления отчёта (Date Reported) */
            if (preg_match('/^[0-9]{8}$/', $attrib['DateReported']) === 0) {
                $segmentError[] = '[10] Дата составления отчёта некорректна';
            }

            /* Лимит кредита/ Исходная сумма кредита (Credit Limit/Contract Amount) */
            if (preg_match('/^[0-9]{0,10}$/', $attrib['CreditLimit-ContractAmount']) === 0) {
                $segmentError[] = '[11] Лимит кредита некорректны';
            }

            /* Баланс (Balance) */
            if (preg_match('/^[0-9]{0,10}$/', $attrib['Balance']) === 0) {
                $segmentError[] = '[12] Баланс некорректен';
            }

            /* Просрочка (Past Due) */
            if (preg_match('/^[0-9]{0,10}$/', $attrib['PastDue']) === 0) {
                $segmentError[] = '[13] Просрочка некорректна';
            }

            /* Следующий платеж (Next Payment) */
            if (preg_match('/^[0-9]{0,10}$/', $attrib['NextPayment']) === 0) {
                $segmentError[] = '[14] Следующий платеж некорректен';
            }

            /* Частота выплат (Credit Payment Frequency) */
            if (preg_match('/^[0-9]{0,1}$/', $attrib['CreditPaymentFrequency']) === 0) {
                $segmentError[] = '[15] Частота выплат некорректна';
            }

            /* Своевременность платежей (MOP) */
            if (preg_match('/^[0-9A]{0,1}$/', $attrib['MOP']) === 0) {
                $segmentError[] = '[16] Своевременность платежей некорректна';
            }

            /* Код валюты (Currency Code) */
            if ($attrib['CurrencyCode'] !== "RUB") {
                $segmentError[] = '[17] Код валюты некорректен';
            }

            /* Код залога (Collateral Code) */
            if (preg_match('/^[0-9]{0,2}$/', $attrib['CollateralCode']) === 0) {
                $segmentError[] = '[18] Код залога некорректен';
            }

            /* Дата окончания срока договора (Date of Contract Termination) */
            if (preg_match('/^([0-9]{0}|[0-9]{8})$/', $attrib['DateOfContractTermination']) === 0) {
                $segmentError[] = '[19] Дата окончания срока договора некорректна';
            }

            /* Дата финального платежа (Date Payment Due) */
            if (preg_match('/^([0-9]{0}|[0-9]{8})$/', $attrib['DatePaymentDue']) === 0) {
                $segmentError[] = '[20] Дата финального платежа некорректна';
            }

            /* Дата финальной выплаты процентов (Date Interest Payment Due) */
            if (preg_match('/^([0-9]{0}|[0-9]{8})$/', $attrib['DateInterestPaymentDue']) === 0) {
                $segmentError[] = '[21] Дата финальной выплаты процентов некорректна';
            }

            /* Частота выплат процентов (Interest Payment Frequency) */
            if (preg_match('/^[0-9]{0,1}$/', $attrib['InterestPaymentFrequency']) === 0) {
                $segmentError[] = '[22] Частота выплат процентов некорректна';
            }

            /* Старое имя пользователя (Old Member Code) */
            if (preg_match($this->dogovorNumber, $attrib['OldMemberCode']) === 0) {
                $segmentError[] = '[23] Старое имя пользователя некорректно';
            }

            /* Старый номер счета (Old Account Number) */
            if (preg_match($this->dogovorNumber, $attrib['OldAccountNumber']) === 0) {
                $segmentError[] = '[24] Старый номер счета не совпадает с шаблоном';
            }

            /* Текущая задолженность (Amount Outstanding) */
            if (preg_match('/^[0-9]{0,10}$/', $attrib['AmountOutstanding']) === 0) {
                $segmentError[] = '[25] Частота выплат процентов некорректна';
            }

            /* Флаг о наличии поручителя (Guarantor indicator) */
            if (preg_match('/^[GN]{0,1}$/', $attrib['GuarantorIndicator']) === 0) {
                $segmentError[] = '[26] Флаг о наличии поручителя некорректен';
            }

            /* Объем обязательства, обеспечиваемого поручительством (Volume of debt secured by guarantee) */
            if (preg_match('/^[FP]{0,1}$/', $attrib['VolumeOfDebtSecuredByGuarantee']) === 0) {
                $segmentError[] = '[27] Объем обязательства, обеспечиваемого поручительством  некорректна';
            }

            /* Сумма поручительства (Guarantee sum) */
            if (preg_match('/^[0-9]{0,10}$/', $attrib['GuaranteeSum']) === 0) {
                $segmentError[] = '[28] Сумма поручительства некорректна';
            }

            /* Срок поручительства (Guarantee term) */
            if (preg_match('/^([0-9]{0}|[0-9]{8})$/', $attrib['GuaranteeTerm']) === 0) {
                $segmentError[] = '[29] Срок поручительства некорректен';
            }

            /* Флаг о наличии банковской гарантии (Bank guarantee indicator) */
            if (preg_match('/^[BN]{0,1}$/', $attrib['BankGuaranteeIndicator']) === 0) {
                $segmentError[] = '[30] Флаг о наличии банковской гарантии некорректен';
            }

            /* Объем обязательства, обеспечиваемого банковской гарантией (Volume of debt secured by bank guarantee) */
            if (preg_match('/^[FP]{0,1}$/', $attrib['VolumeOfDebtSecuredByBankGuarantee']) === 0) {
                $segmentError[] = '[31] Объем обязательства, обеспечиваемого банковской гарантией некорректен';
            }

            /* Сумма банковской гарантии (Bank guarantee sum) */
            if (preg_match('/^[0-9]{0,10}$/', $attrib['BankGuaranteeSum']) === 0) {
                $segmentError[] = '[32] Сумма банковской гарантии некорректна';
            }

            /* Срок банковской гарантии (Bank guarantee term) */
            if (preg_match('/^([0-9]{0}|[0-9]{8})$/', $attrib['BankGuaranteeTerm']) === 0) {
                $segmentError[] = '[33] Срок банковской гарантии некорректен';
            }

            /* Оценочная стоимость залога (Collateral value) */
            if (preg_match('/^[0-9]{0,10}$/', $attrib['CollateralValue']) === 0) {
                $segmentError[] = '[34] Оценочная стоимость залога некорректна';
            }

            /* Дата оценки стоимости залога (Collateral date) */
            if (preg_match('/^([0-9]{0}|[0-9]{8})$/', $attrib['CollateralDate']) === 0) {
                $segmentError[] = '[35] Дата оценки стоимости залога некорректна';
            }

            /* Срок действия договора залога (Collateral agreement expiration date) */
            if (preg_match('/^([0-9]{0}|[0-9]{8})$/', $attrib['CollateralAgreementExpirationDate']) === 0) {
                $segmentError[] = '[36] Срок действия договора залога некорректен';
            }

            /* Полная стоимость кредита (Overall value of credit) */
            if (preg_match('/^[0-9,]{0,10}$/', $attrib['OverallValueOfCredit']) === 0) {
                $segmentError[] = '[37] Полная стоимость кредита некорректна';
            }

            /* Наименования приобретателя права требования (Right of Claim Acquirer’s Names) */
            if (!preg_match('/^[А-Я ]{0,2520}$/ui', $attrib['RightOfClaimAcquirerNames'])) {
                $segmentError[] = '[38] Наименования приобретателя права требования некорректна' . $segment[1];
            }

            /* Идентификационные данные приобретателя права требования (Right of Claim Acquirer’s Registration Data) */
            if (!preg_match('/^[А-Я ]{0,738}$/ui', $attrib['RightOfClaimAcquirerRegistrationData'])) {
                $segmentError[] = '[39] Идентификационные данные приобретателя права требования некорректны' . $segment[1];
            }

            /* ИНН приобретателя права требования (Right of Claim Acquirer’s Taxpayer ID) */
            if (preg_match('/^[0-9]{0,12}$/', $attrib['RightOfClaimAcquirerTaxpayerID']) === 0) {
                $segmentError[] = '[40] ИНН приобретателя права требования';
            }

            /* СНИЛС приобретателя права требования (Right of Claim Acquirer’s Social Insurance Number) */
            if (preg_match('/^[0-9]{0,12}$/', $attrib['RightOfClaimAcquirerSocialInsuranceNumber']) === 0) {
                $segmentError[] = '[38] СНИЛС приобретателя права требования';
            }

            /* Дата фактического исполнения обязательств в полном объеме */
            if (preg_match('/^([0-9]{0}|[0-9]{8})$/', $attrib['FinishDate']) === 0) {
                $segmentError[] = '[42] Дата фактического исполнения обязательств в полном объеме некорректна';
            }

        }

        /* Проверка 28-M  */
         if ($attrib['GuarantorIndicator'] === 'G' && strlen(trim($attrib['GuaranteeSum']) <= 0)) {
             $segmentError[] = '[28-M] Если есть поручитель, то сумма поручительства должна быть указана';
         }

        /* Проверка 34-M 35-M */
         if (strlen($attrib['CollateralAgreementExpirationDate']) === 8 && strlen($attrib['CollateralDate'] !== 8) && strlen($attrib['CollateralValue'] <= 0)) {
             $segmentError[] = '[34-M 35-M] Если есть залог, то оценочная стоимость залога и дата оценки должна быть указана';
         }

        return isset($segmentError) ? $segmentError : false;
    }

    /*
     *  IP
     *  Сегмент содержит информационную часть кредитной истории (данные о цикле обработки заявки на получение кредита и его дальнейшем обслуживании в случае одобрения).
     */
    public function IP($segment)
    {

        /* Имя пользователя (Member Code) */
        if (Server::sTrim($segment[1]) !== 'NAME') {
            $segmentError[] = '[2] Имя пользователя неверно';
        }

        /* Номер заявки/Номер договора поручительства (Application Number) */
        if (preg_match($this->dogovorNumber, Server::sTrim($segment[2])) === 0) {
            $segmentError[] = '[3] Номер заявки/Номер договора поручительства не совпадает с шаблоном';
        }

        /* Дата заявки/Дата договора поручительства (Date of Application) */
        if (preg_match('/^[0-9]{8}$/', Server::sTrim($segment[3])) === 0) {
            $segmentError[] = '[4] Дата заявки/Дата договора поручительства некорректна';
        }

        /* Тип займодавца (Creditor Type) */
        if (preg_match('/^[1]{1}$/', Server::sTrim($segment[4])) === 0) {
            $segmentError[] = '[5] Тип займодавца некорректен';
        }

        /* Вид обязательства (Type Flag) */
        if (preg_match('/^[12]{1}$/', Server::sTrim($segment[5])) === 0) {
            $segmentError[] = '[6] Вид обязательства некорректен';
        }

        /* Тип запрошенного кредита (Requested Loan Type) */
        if (preg_match('/^[0-9]{3}$/', Server::sTrim($segment[6])) === 0) {
            $segmentError[] = '[7] Тип запрошенного кредита некорректен';
        }

        /* Способ предоставления заявки (Application Shipment Way) */
        if (preg_match($this->dogovorNumber, Server::sTrim($segment[7])) === 0) {
            if (preg_match('/^[123]{1}$/', Server::sTrim($segment[7])) === 0) {
                $segmentError[] = '[8] Способ предоставления заявки некорректен';
            }
        } else if (preg_match('/^[123]{1}$/', Server::sTrim($segment[7])) <> 0) {
                $segmentError[] = '[8] Способ предоставления заявки некорректен';
            }

        /* Решение об одобрении (Flag of Approval) */
        if (preg_match('/^[Y]{0,1}$/', Server::sTrim($segment[8])) === 0) { $segmentError[] = '[9] Решение об одобрении некорректно'; }

        /* Срок окончания действия одобрения (Approval Expiration) */
        if (preg_match('/^([0-9]{0}|[0-9]{8})$/', Server::sTrim($segment[9])) === 0) { $segmentError[] = '[10] Срок окончания действия одобрения некорректен'; }

        /* Сумма займа по отклоненной заявке (Rejected Amount) */
        if (preg_match('/^[0-9]{0,10}$/', Server::sTrim($segment[10])) === 0) { $segmentError[] = '[11] Сумма займа по отклоненной заявке некорректна'; }

        /* Валюта отклоненной заявки (Rejected Amount Currency) */
        if (preg_match('/^[RUB]{0,3}$/', Server::sTrim($segment[11])) === 0) { $segmentError[] = '[12] Валюта отклоненной заявки некорректна'; }

        /* Дата отказа в предоставлении займа (Rejection date) */
        if (preg_match('/^([0-9]{0}|[0-9]{8})$/', Server::sTrim($segment[12])) === 0) { $segmentError[] = '[13] Дата отказа в предоставлении займа некорректна'; }

        /* Основание отказа в выдаче займа (Rejection Reason */
        if (preg_match('/^[1-5]{0,1}$/', Server::sTrim($segment[13])) === 0) { $segmentError[] = '[14] Основание отказа в выдаче займа некорректно'; }

        /* Номер договора займа (кредита) (Agreement Number) */
        if (preg_match($this->dogovorNumber, Server::sTrim($segment[14])) === 0) { $segmentError[] = '[15] Номер договора займа (кредита) не совпадает с шаблоном'; }

        /* Информация о предоставленном кредите (если отличается от информации о запрошенном кредите) (Approved Loan Type) */
        if (preg_match('/^[0-9]{0,3}$/', Server::sTrim($segment[15])) === 0) { $segmentError[] = '[16] Информация о предоставленном кредите (если отличается от информации о запрошенном кредите) некорректна'; }

        /* Признак дефолта (Default Flag) */
        if (preg_match('/^[D]{0,1}$/', Server::sTrim($segment[16])) === 0) { $segmentError[] = '[17] Признак дефолта некорректен'; }

        /* Кредит погашен (Loan Fully Returned Indicator) */
        if (preg_match('/^[R]{0,1}$/', Server::sTrim($segment[17])) === 0) { $segmentError[] = '[18] Статус погашения кредита некорректен'; }

        /* Старый номер заявки/Старый номер договора поручительства (Old Application Number) */
        if (preg_match($this->dogovorNumber, Server::sTrim($segment[18])) === 0) { $segmentError[] = '[19] Старый номер заявки/Старый номер договора поручительства не совпадает с шаблоном'; }

        return isset($segmentError) ? $segmentError : false;
    }

    /*
     * trim для случаев когда два и больше пробелов подряд + trim убирающий пробелы с начала и с конца
     */
    public function sTrim($line) {
        return trim(preg_replace("/ {2,}/"," ", $line));
    }

}
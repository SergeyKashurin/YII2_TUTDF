<?php
/**
 * Created by PhpStorm.
 * User: u92
 * Date: 11.02.2016
 * Time: 8:06
 */

namespace app\models;


use yii\base\Model;

class PravilaObrabotkiKI extends Model
{

    /*
     * Допустимые руководством типы данных TUTDF
     */
    public $type_A;
    public $type_N;
    public $type_S;
    public $type_D;
    public $type_AN;
    public $type_P;
    public $type_C;

    /*
     * Служебные переменные
     * $version_ki @string TUTDF 2.0r или 4.0r
     * $tutdf_segments @array TUTDF наименования сегментов
     */
    public $version_ki;
    public $version_date;
    public $member_code;
    public $cycle_identification;
    public $authorization_code;
    public $id_type;
    public $tutdf_segments;

    public function __construct()
    {
        $this->type_A =  "A-ZА-Я";              # Буквенные данные
        $this->type_N =  "[0-9]";                 # Численные данные
        $this->type_S =  "0-9";                 # Знаковые числа
        $this->type_D =  "[0-9]{8}";              # Данные о датах
        $this->type_AN = "[A-Za-zА-Яа-я0-9]";     # Буквенно-цифровые данные
        $this->type_P =  ".";                   # Данные для вывода на печать
        $this->type_C =  "A-Za-zА-Яа-я\. ,'-";  # Буквенные данные + спец. символы

        # Перечисляем действующие версии формата TUTDF
        $this->version_ki = [
            '2.0r', '4.0r',
        ];

        # Перечисляем даты опубликования версий TUTDF
        $this->version_date = [
            '20051031', '20150701',
        ];

        # Имя пользователя Банка в НБКИ
        $this->member_code = "NAME";

        # Идентификация цикла отправки в НБКИ
        $this->cycle_identification = "1";

        # Авторизационный пароль в системе НБКИ
        $this->authorization_code = "CODE";

        # Тип документа ID
        $this->id_type = [
            '21', '33', '34', '81', '97',
        ];

        # Перечисление всех использующихся в Банке сегментов TUTDF
        $this->tutdf_segments = [
            'TUTDF',
            'ID01', 'ID02', 'ID03', 'ID04',
            'NA01',
            'BU01',
            'AD01', 'AD02',
            'PN01', 'PN02', 'PN03', 'PN04', 'PN05',
            'CL01', 'CL02', 'CL03', 'CL04', 'CL05', 'CL06', 'CL07', 'CL08', 'CL09', 'CL10',
            'GR01', 'GR02', 'GR03', 'GR04', 'GR05', 'GR06', 'GR07', 'GR08', 'GR09', 'GR10',
            'TR01',
            'IP01',
            'TRLR',
        ];
    }

    /*
     * Обязательность:
     * [M = Mandatory = Обязательное]
     *
     * $incomingData @string
     * return @boolean
     */
    public function Mandatory($incomingData = '')
    {
        return !empty(isset($incomingData)) ? true : false;
    }

    ### Сегмент заголовка TUTDF ###

    /*
     *  Проверка наименования сегмента на корректность
     *
     *  $data @string
     *  return @boolean
     */
    public function SegmentTag($data)
    {
        $trim_data = trim($data);

        return (
            preg_match('/^[A-Z]{2}[0-9]{2}$/', $trim_data) ||
            $trim_data === 'TUTDF' ||
            $trim_data === 'TRLR'
        ) && in_array($trim_data, $this->tutdf_segments) ? true : false;
    }

    /*
     *  Проверка версии формата TUTDF на корректность
     *
     *  $data @string
     *  return @boolean
     */
    public function Version($data)
    {
        $trim_data = trim($data);

        return (
            preg_match('/^[0-9]{1}\.[0-9]{1}[A-Za-z]{1}$/', $trim_data)
        ) && in_array($trim_data, $this->version_ki) ? true : false;
    }

    /*
     *  Дата публикования версии TUTDF
     *
     *  $data @string
     *  return @boolean
     */
    public function VersionDate($data)
    {
        $trim_data = trim($data);

        return (
            preg_match("/^$this->type_D$/", $trim_data)
        ) && in_array($trim_data, $this->version_date) ? true : false;
    }

    /*
     *  Имя пользователя TUTDF
     *
     *  $data @string
     *  return @boolean
     */
    public function MemberCode($data)
    {
        $trim_data = trim($data);

        return (
            preg_match("/^$this->type_AN{12}$/", $trim_data)
        ) && $trim_data === $this->member_code ? true : false;
    }

    /*
     *  Идентификация цикла TUTDF
     *
     *  $data @string
     *  return @boolean
     */
    public function CycleIdentification($data)
    {
        $trim_data = trim($data);

        return (
            preg_match("/^$this->type_AN{12}$/", $trim_data)
        ) && $trim_data === $this->cycle_identification ? true : false;
    }

    /*
     *  Дата составления отчёта TUTDF
     *
     *  $data @string
     *  return @boolean
     */
    public function ReportedDate($data)
    {
        $trim_data = trim($data);

        # Дата в настоящем
        $now_date = date("Ymd");

        return (
            preg_match("/^$this->type_D$/", $trim_data)
        ) && $trim_data === $now_date ? true : false;
    }

    /*
     *  Пароль в системе НБКИ
     *
     *  $data @string
     *  return @boolean
     */
    public function AuthorizationCode($data)
    {
        $trim_data = trim($data);

        return (
            preg_match("/^$this->type_AN{8}$/", $trim_data)
        ) && $trim_data === $this->authorization_code ? true : false;
    }

    /*
     *  Данные участника НБКИ
     *
     *  return @true
     */
    public function MemberData() { return true; }

    ### Идентифицирующий сегмент (ID) ###

    /*
     *  Тип ID
     *
     *  $data @string
     *  return @boolean
     */
    public function IdType($data)
    {
        $trim_data = trim($data);

        return (
            preg_match("/^$this->type_N{2}$/", $trim_data)
        ) && in_array($trim_data, $this->id_type) ? true : false;
    }

    /*
     *  Номер серии
     *
     *  $data @string
     *  return @boolean
     */
    public function SeriesNumber($data)
    {
        $trim_data = trim($data);

        return (
        preg_match("/^$this->type_N{2}$/", $trim_data)
        ) && in_array($trim_data, $this->id_type) ? true : false;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: u92
 * Date: 01.12.2015
 * Time: 13:55
 */

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Urlic extends ActiveRecord
{
    public $counts;
    #Служебные переменные
    private $record, $nowDate, $nowTime, $dateStatusScet, $INCREMENTKredLastPay, $KredPogashenie, $KredPogashenieSum = 0, $KredZadolgennost;
    #Части TUTDF файлов
    private $TUTDF, $ID01, $ID02, $BU01, $AD01, $AD02, $PN01, $TR01, $TRLR=9;

    public static function tableName()
    {
        return 'urlic_reestr';
    }

    public function rules()
    {

        return[
            //[['FlagPoruch', 'ObyemObyazatelstva', 'SummaPoruchitelstva', 'PSK'],'string'],
            [['OtsenochnayaStoimostZaloga', 'DataOtsenkiStoimostiZaloga', 'SrokDeystviyaDogovoraZaloga', 'PSK'],'string'],
            [['HomeKorp', 'HomeStory', 'HomeKvartira', 'OKOHX', 'CompanyStatusDate', 'OKFS', 'OKOPF'],'string'],
            [['PoruchFlag', 'PoruchObesb', 'PoruchSumm', 'OKPO', 'OKBED', 'OKATO', 'OKOGU', 'KPP'],'string'],
            [['PersonaTel2', 'PersonaTel2Type', 'PersonaTel3', 'PersonaTel3Type', 'PersonaTel4', 'PersonaTel4Type'],'string'],

            [[
                'PostIndex', 'HomeArea', 'HomeTown', 'HomeStreetType', 'HomeStreet', 'HomeHouse',
                'PersonaTel1', 'PersonaTel1Type', 'OGRN', 'INN',

                'CompanyName', 'RegistrationDate', 'CompanyStatus', 'KodZaloga',
                'CompanyNameShort', 'KredScet', 'KredScetType', 'KredScetOtn', 'KredOpenDate', 'KredLastPay',
                'KredScetActive', 'KredFullSum', 'KredSum', 'KredEndDate', 'KredEndPay', 'KredEndProc', 'formatTUTDF', 'operationType',
            ], 'required', 'message' => 'Заполните обязательно поле!'],

            #Все даты
            [['DataOtsenkiStoimostiZaloga', 'SrokDeystviyaDogovoraZaloga', 'CompanyStatusDate', 'KredOpenDate', 'KredLastPay',
                'KredEndDate', 'KredEndPay', 'KredEndProc', 'RegistrationDate'], 'match', 'pattern' => '/^[0-9]{8}$/i'],

            /*
             *  Правила обработки данных введённых пользователем
             */
            [['PostIndex'], 'match', 'pattern' => '/^[0-9]{6}$/i'],
            [['KredSum', 'KredFullSum', 'PoruchSumm', 'OtsenochnayaStoimostZaloga'], 'match', 'pattern' => '/^[0-9]{1,}$/i'],
            // 'SummaPoruchitelstva',
            [['OGRN'], 'match', 'pattern' => '/^[0-9]{13}$/i'],
            [['INN'], 'match', 'pattern' => '/^[0-9]{10}$/i'],
            [['OKPO'], 'match', 'pattern' => '/^[0-9]{8}$/i'],
            [['OKOHX'], 'match', 'pattern' => '/^[0-9]{5}$/i'],
            [['OKBED'], 'match', 'pattern' => '/^[0-9\.]{1,8}$/i'],
            [['OKATO'], 'match', 'pattern' => '/^[0-9]{11}$/i'],
            [['OKOGU'], 'match', 'pattern' => '/^[0-9]{5,7}$/i'],
            [['OKFS'], 'match', 'pattern' => '/^[0-9]{2}$/i'],
            [['OKOPF'], 'match', 'pattern' => '/^[0-9]{2,5}$/i'],
            [['KPP'], 'match', 'pattern' => '/^[0-9]{9}$/i'],
            [['PSK'], 'match', 'pattern' => '/^[0-9,]{5}$/i'],
            [['KredScet'], 'match', 'pattern' => '/^([0-9]{20})$/i'],

            [['PersonaTel1', 'PersonaTel2', 'PersonaTel3', 'PersonaTel4'], 'match', 'pattern' => '/^7[0-9]{10}$/i'],

            #Сумма баланса должна быть больше суммы кредита.
            [['KredFullSum', 'KredSum'], function($attribute) {
                $min = $this->KredFullSum - $this->KredSum;
                if($min >= 0) {
                    $this->addError($attribute, 'Сумма баланса должна быть больше исходной суммы кредита');
                }
            }],

            [['KredOpenDate', 'KredEndPay'], function() {
                $this->counts = $this->calcCountMonth($this->KredOpenDate, $this->KredEndPay);

                #Дата формирования записи
                $this->RecDate = date("Y-m-d H:i:s");
                #$counts = $this->counts;
                return $this->counts;
            }],
        ];
    }

    /*
     * Рассчёт кол-ва месяцев.
     * Месяцы = ДатаПогашения - ДатаОткрытияСчёта
     */
    public function calcCountMonth($startKredDate, $endKredDate)
    {
        $date1 = new \DateTime($startKredDate);
        $date2 = new \DateTime($endKredDate);
        $interval = $date1->diff($date2);
        $col = (($interval->y*12)+$interval->m);
        return $col;
    }

    /*
     * Объединяем все данные и создаём файлы выгрузки
     * @var $content array
     */
    public function generateUrlic($counts)
    {
        $this->KredScetActive = "00";
        $this->nowDate = date('Ymd');
        $this->nowTime = date('Hi');

        $this->dateStatusScet = $this->KredOpenDate;

        #Создаём директорию для записи КИ
        $fname_dop = $this->KredScet;
        $fname = str_replace("/", "-", $fname_dop);
        if (!file_exists("URLIC/$this->nowDate" . "_" . $fname)) {
            mkdir("URLIC/$this->nowDate" . "_" . $fname, 0777);

            #Рассчёт погашения кредита. Баланс делим на кол-во месяцев / 2
            $this->KredPogashenie = round($this->KredSum / ($counts + 2), 0);

            # В зависимости от формата передачи данных, меняем заголовки
            if ($this->formatTUTDF === "0" || !isset($this->formatTUTDF)) {
                $this->TUTDF = "TUTDF\t2.0r\t20051031\tNAME\t1\t$this->nowDate\tCODE\tОчередная пачка обновлений для НБКИ";
            } else if ($this->formatTUTDF === "1") {
                $this->TUTDF = "TUTDF\t4.0r\t20150701\tNAME\t1\t$this->nowDate\tCODE\tОчередная пачка обновлений для НБКИ";
            }

            $this->ID01 = "ID01\t81\t \t$this->INN\t \t \t \t ";
            $this->ID02 = "ID02\t34\t \t$this->OGRN\t \t \t \t ";

            // Если статус ЮР лица не 0, то разблокироваем поле "Дата статуса"
            if ($this->CompanyStatus === "0") {
                $this->CompanyStatusDate = "";
            }
            $this->BU01 = "BU01\t$this->CompanyName\t$this->RegistrationDate\t$this->CompanyStatus\t$this->CompanyStatusDate\t$this->OKPO\t$this->OKOHX\t$this->OKBED\t$this->OKATO\t$this->OKOGU\t$this->OKFS\t$this->OKOPF\t$this->KPP\t \t \t \t$this->CompanyNameShort\t \t \t \t \t \t \t \t ";

            $this->AD01 = "AD01\t3\t$this->PostIndex\tRU\t07\t \t$this->HomeArea\t$this->HomeTown\t$this->HomeStreetType\t$this->HomeStreet\t$this->HomeHouse\t$this->HomeKorp\t$this->HomeStory\t$this->HomeKvartira\t \t ";
            $this->AD02 = "AD02\t4\t$this->PostIndex\tRU\t07\t \t$this->HomeArea\t$this->HomeTown\t$this->HomeStreetType\t$this->HomeStreet\t$this->HomeHouse\t$this->HomeKorp\t$this->HomeStory\t$this->HomeKvartira\t \t ";

            $this->PN01 = "PN01\t$this->PersonaTel1\t$this->PersonaTel1Type";

            #Если PN02 заполнен, то показываем его
            if (strlen($this->PersonaTel2) > 5) {
                $this->PN01 .= "\r\nPN02\t$this->PersonaTel2\t$this->PersonaTel2Type";
                $this->TRLR = 10;
            }

            #Если PN03 заполнен, то показываем его
            if (strlen($this->PersonaTel3) > 5) {
                $this->PN01 .= "\r\nPN03\t$this->PersonaTel3\t$this->PersonaTel3Type";
                $this->TRLR = 11;
            }

            #Если PN04 заполнен, то показываем его
            if (strlen($this->PersonaTel4) > 5) {
                $this->PN01 .= "\r\nPN04\t$this->PersonaTel4\t$this->PersonaTel4Type";
                $this->TRLR = 12;
            }

            for ($i = 0; $i <= $counts + 1; $i++) {
                #Первая выплата невозможна в первый день подписания кредита, потому увеличиваем дату на 1 день
                if ($i === 0) {
                    $this->INCREMENTKredLastPay = date('Ymd', strtotime($this->KredOpenDate . '+1 day'));
                } else {
                    #Берём дату последней выплаты, уменьшаем её на разность месяцев от заведения счёта и добавляем +1 месяц (Имитация ежемесячного погашения)
                    $this->INCREMENTKredLastPay = date('Ymd', strtotime($this->KredOpenDate . '+' . $i . ' month'));
                }

                #Рассчёт суммы погашения кредита
                $this->KredPogashenieSum = $this->KredPogashenieSum + $this->KredPogashenie;

                #Рассчёт суммы задолженности по кредиту
                $this->KredZadolgennost = $this->KredSum - $this->KredPogashenieSum;

                #Если договор закрылся, меняем статус счёта и дату изменения статуса счёта
                if ($i >= ($counts + 1)) {
                    $this->KredScetActive = 13;
                    $this->dateStatusScet = $this->KredEndPay;

                    #В последний месяц, устанавливаем крайние значения для закрытия счёта
                    $this->KredZadolgennost = 0;
                    $this->KredPogashenieSum = $this->KredSum;

                    #Дата последней выплаты = дата окончания договора
                    $this->INCREMENTKredLastPay = $this->KredEndPay;
                }

                #file_put_contents("TUTDF/$this->nowDate"."_"."$this->KredScet"."/test.txt", "Дата погашения: ".$this->INCREMENTKredLastPay."\t Баланс: ".$this->KredPogashenieSum."\t Задолженность: ".$this->KredZadolgennost."\n", FILE_APPEND | LOCK_EX);

                # В зависимости от формата передачи данных, сегмент TR01
                if ($this->formatTUTDF === "0" || !isset($this->formatTUTDF)) {
                    $this->TR01 = "TR01\tNAME\t$this->KredScet\t$this->KredScetType\t$this->KredScetOtn\t$this->KredOpenDate\t$this->INCREMENTKredLastPay\t$this->KredScetActive\t$this->dateStatusScet\t$this->INCREMENTKredLastPay\t$this->KredFullSum\t$this->KredPogashenieSum\t0\t \t \t1\tRUB\t$this->KodZaloga\t$this->KredEndDate\t$this->KredEndPay\t$this->KredEndProc\t \t \t \t$this->KredZadolgennost";
                } else if ($this->formatTUTDF === "1") {
                    $this->TR01 = "TR01\tNAME\t$this->KredScet\t$this->KredScetType\t$this->KredScetOtn\t$this->KredOpenDate\t$this->INCREMENTKredLastPay\t$this->KredScetActive\t$this->dateStatusScet\t$this->INCREMENTKredLastPay\t$this->KredFullSum\t$this->KredPogashenieSum\t0\t \t \t1\tRUB\t$this->KodZaloga\t$this->KredEndDate\t$this->KredEndPay\t$this->KredEndProc\t \t \t \t$this->KredZadolgennost\t$this->PoruchFlag\t$this->PoruchObesb\t$this->PoruchSumm\t$this->KredEndDate\tN\t \t \t \t$this->OtsenochnayaStoimostZaloga\t$this->DataOtsenkiStoimostiZaloga\t$this->SrokDeystviyaDogovoraZaloga\t$this->PSK\t \t \t \t ";
                }

                $this->record = $this->TUTDF . "\r\n" . $this->ID01 . "\r\n" . $this->ID02 . "\r\n" . $this->BU01 . "\r\n" . $this->AD01 . "\r\n" . $this->AD02 . "\r\n" . $this->PN01 . "\r\n" . $this->TR01 . "\r\n";
                $this->record .= "TRLR\t$this->TRLR ";

                #Порядковое именование файлов
                if ($i < 9) {
                    $q = "0" . ($i + 1);
                } else {
                    $q = $i + 1;
                }

                file_put_contents("URLIC/$this->nowDate" . "_" . $fname . "/NAME_" . $this->nowDate . "_" . $this->nowTime . "$q", iconv('UTF-8', 'CP1251', $this->record));
            }
        }
    }
}
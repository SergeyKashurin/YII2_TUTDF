<?php
/**
 * Created by PhpStorm.
 * User: u92
 * Date: 23.11.2015
 * Time: 12:35
 */

namespace app\models;

use yii\db\ActiveRecord;
use Yii;


class Zayavka extends ActiveRecord
{

    private $TUTDF, $ID01, $ID02, $NA01, $AD01, $AD02, $PN01, $TR01, $TRLR=9;

    public static function tableName()
    {
        return 'reestr';
    }

    public function rules()
    {
        return[ #Чтобы необязательные данные попадали в модель
            [['HomeKorp', 'HomeStory', 'HomeKvartira'],'string'],

            # Далее перечисление обязательных переменных
            [['PersonaFam', 'PersonaName', 'PersonaOtc', 'PersonaBorn', 'PersonaBornHome', 'PersonaInn', 'PasportSer',
                'PasportNumb', 'PasportDate', 'PasportWhere', 'PostIndex', 'HomeArea', 'HomeTown', 'HomeStreetType', 'HomeStreet', 'HomeHouse',
                'PersonaTel1', 'PersonaTel1Type', 'NomerDogovoraPoruchitelstva', 'DataZayavki', 'TipZaproshennogoKredita','SrokOkonchaniyaDeystviyaOdobreniya', 'operationType',
            ], 'required', 'message' => 'Заполните обязательное поле!'],

            #Фамилия Имя Отчество
            [['PersonaFam', 'PersonaName', 'PersonaOtc'], 'match', 'pattern' => '/[А-Яа-я]{2,}/'],
            #Все даты
            [['PersonaBorn', 'PasportDate'], 'match', 'pattern' => '/^[0-9]{8}$/i'],
            #Индекс и Номер паспорта
            [['PostIndex', 'PasportNumb'], 'match', 'pattern' => '/^[0-9]{6}$/i'],
            #ИНН
            [['PersonaInn'], 'match', 'pattern' => '/^[0-9]{12}$/i'],
            #Серия паспорта
            [['PasportSer'], 'match', 'pattern' => '/^[0-9]{2} [0-9]{2}$/i'],
            #Номер телефона
            [['PersonaTel1', 'PersonaTel2'], 'match', 'pattern' => '/^7[0-9]{10}$/i'],
        ];
    }

    /*
     * Создаём файл выгрузки заявки
     * @var $content array
     */
    public function generateZayavka()
    {
        $nowDate = date('Ymd');
        $nowTime = date('Hi');

        #Проверяем, если файл уже создан, то непересоздаём
        if (!file_exists("ZAYAVKI/".$this->NomerDogovoraPoruchitelstva)) {

            if (!file_exists("ZAYAVKI/".str_replace("/", "-", $this->NomerDogovoraPoruchitelstva))) {
                mkdir("ZAYAVKI/" . str_replace("/", "-", $this->NomerDogovoraPoruchitelstva));

                $this->operationType = 'zayavka';

                $this->TUTDF = "TUTDF\t3.0r\t20140701\tNAME\t1\t$nowDate\tCODE\tОчередная пачка обновлений для НБКИ";
                $this->ID01 = "ID01\t81\t \t$this->PersonaInn\t \t \t \t ";
                $this->ID02 = "ID02\t21\t$this->PasportSer\t$this->PasportNumb\t$this->PasportDate\t$this->PasportWhere\t \t ";
                $this->NA01 = "NA01\t$this->PersonaFam\t$this->PersonaOtc\t$this->PersonaName\t \t$this->PersonaBorn\t$this->PersonaBornHome\t \t \t \t \t \t ";
                $this->AD01 = "AD01\t1\t$this->PostIndex\tRU\t07\t \t$this->HomeArea\t$this->HomeTown\t$this->HomeStreetType\t$this->HomeStreet\t$this->HomeHouse\t$this->HomeKorp\t$this->HomeStory\t$this->HomeKvartira\t \t ";
                $this->AD02 = "AD02\t2\t$this->PostIndex\tRU\t07\t \t$this->HomeArea\t$this->HomeTown\t$this->HomeStreetType\t$this->HomeStreet\t$this->HomeHouse\t$this->HomeKorp\t$this->HomeStory\t$this->HomeKvartira\t \t ";
                $this->PN01 = "PN01\t$this->PersonaTel1\t$this->PersonaTel1Type";

                #Если PN02 заполнен, то показываем его
                if (strlen($this->PersonaTel2) > 5) {
                    $this->PN01 .= "\r\nPN02\t$this->PersonaTel2\t$this->PersonaTel2Type";
                    $this->TRLR = 10;
                }

                $this->TR01 = "IP01\tNAME\t \t$this->DataZayavki\t1\t1\t$this->TipZaproshennogoKredita\t1\tY\t$this->SrokOkonchaniyaDeystviyaOdobreniya\t \t \t \t \t$this->NomerDogovoraPoruchitelstva\t \t \t \t ";

                $record = $this->TUTDF . "\r\n" . $this->ID01 . "\r\n" . $this->ID02 . "\r\n" . $this->NA01 . "\r\n" . $this->AD01 . "\r\n" . $this->AD02 . "\r\n" . $this->PN01 . "\r\n" . $this->TR01 . "\r\n";
                $record .= "TRLR\t$this->TRLR ";

                file_put_contents("ZAYAVKI/" . str_replace("/", "-", $this->NomerDogovoraPoruchitelstva) . "/NAME_" . $nowDate . "_" . $nowTime . "01", iconv('UTF-8', 'CP1251', $record));
            }
        }
    }


}
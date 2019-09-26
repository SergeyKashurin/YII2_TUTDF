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

class Bv extends Model
{
    public static function tableName()
    {
        return 'bv';
    }

    public $line;

    public $txtFile;
    public $buffer;
    public $rezult;
    public $table;

    public $err = 0;
    public $repairData;

    public function rules()
    {
        return [
            [['txtFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'txt'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Загрузите текстовый файл BV',
        ];
    }

    public function upload()
    {

        if ($this->validate()) {

            $this->txtFile->saveAs('bv/' . $this->txtFile->baseName . '.' . $this->txtFile->extension);
            $this->proverkaKorrektnosti();

            if($this->err !== 0) {
                $this->proverkaKorrektnosti('bv/out');
            }

            return true;
        } else {
            return false;
        }
    }

    /*
     *  Проверяем, есть ли управляющие символы в строке, если нет, выводим позицию строки ошибки
     *  @return string|array
     */
    public function proverkaKorrektnosti($path = 'bv')
    {
        $findme = ':';
        $i = 0;
        $q = 0;
        $errorCol = 1;

        # Объявляем таблицу
        $this->rezult[] = '<div class="table-responsive"><table class="table">';

        $handle = fopen($path.'/' . $this->txtFile->baseName . '.' . $this->txtFile->extension, "r");
        while (!feof($handle)) {

            $this->line = iconv("CP866", "UTF-8", trim(fgets($handle, 4096)));
            $pos = mb_strpos(trim($this->line), $findme);

            /*
             *  если нет в строке ':' или спец символов, то сигнализируем
             */
            if (($pos === false && ($this->line !== "###" && $this->line !== "@@@" && $this->line !== "===")))
            {
                    if(!feof($handle)) {
                        // Draw danger column in table
                        $this->rezult[] = '<tr class="danger"><td>';
                            # Line number
                            $q = $i + 1;
                                $this->rezult[] = 'Ошибка в строке: '.$q;
                                # № п/п
                                $errorCol++;
                                    $this->rezult[] = '</td></tr>';

                        /*
                         *  В массив repairData записываем уже исправленный вариант содержимого файла
                         */
                        $this->repairData[count($this->repairData)-1] = $this->repairData[count($this->repairData)-1] . $this->line;
                    }
            } else {
                /*
                 *  В массив repairData записываем корректное содержимое файла
                 */
                $this->repairData[] = $this->line;
            }
            $i++;

        }
        fclose($handle);

        if($q === 0) {
            $this->rezult[] = '<tr class="success"><td>Ошибок не выявлено.</td></tr>';
            $this->rezult[] = '<tr class="default"><td><a href="'.Url::toRoute(['site/download-file', 'id' => 'bv/out/' . $this->txtFile->baseName . '.' . $this->txtFile->extension]).'"><button type="button" class="btn btn-success">Скачать исправленный файл</button></a></td></tr>';
                $this->err = 0;
        } else {

            # Если такой файл уже существует - удаляем
            if (file_exists('bv/out/' . $this->txtFile->baseName . '.' . $this->txtFile->extension)) {
                unlink('bv/out/' . $this->txtFile->baseName . '.' . $this->txtFile->extension);
            }

            # Создаём файл
            $fp = fopen('bv/out/' . $this->txtFile->baseName . '.' . $this->txtFile->extension, "a");
            for($w=0; $w<=count($this->repairData)-1; $w++)
            {
                # Записываем в него исправленные строки в кодировке CP866 как в изначальном файле
                fwrite($fp, iconv("UTF-8", "CP866", trim($this->repairData[$w]))."\r\n");
            }
            $this->err = 1;
                $this->rezult[] = '<tr class="info"><td>Исправляю ошибки, пересобираю файл, проверяю заново.</td></tr>';
        }

        # Close the table
        $this->rezult[] = '</table></div>';

        return $this->rezult;
    }

    /*
     *  Функция исправляет ошибки в BV файле
     *  - ищет строки без спец символов и добавляет к предыдущим
     */
    public function ispravleniyeOshibok()
    {

    }

}
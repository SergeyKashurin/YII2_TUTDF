<?php
/**
 * Created by PhpStorm.
 * User: u92
 * Date: 04.02.2016
 * Time: 8:31
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Server';
?>
<div class="container-fluid">

<div class="row">
    <!--
        <div class="col-lg-6">
            <address>
                <strong>Общая информация:</strong><br>
                Записей загружено: 5<br>
                Физиков: 7<br>
                Юриков: 7<br>
                <abbr title="Phone">Ошибок в БД:</abbr> 25
            </address>
        </div>

        <div class="col-lg-6">
            <dl class="dl-horizontal">
                <dt>Формулировка</dt>
                <dd>Описание</dd>

                <dt>Формулировка №2</dt>
                <dd>Описание №2</dd>
            </dl>

        </div>
    </div>
-->
    <div class="row">
        <div class="col-lg-12">

                    <?php
                        $countLine = 1;
                        $flag = 0;
                        $endFlag = 0;

                        for($i = 0; $i <= count($model['answerArr'])-1; $i++) {

                                for($y = 0; $y <= count((array)$model['answerArr'][$i])-1; $y++) {

                                    if ($y !== count($model['answerArr'][$i])-1)
                                        $countLine++;

                                    for($z = 0; $z <= count((array)$model['answerArr'][$i][$y])-1; $z++) {
                                        # Вывод данных
                                        # echo $model['answerArr'][$i][$y][$z].'<br>';

                                            if ($z === count($model['answerArr'][$i][$y]) - 1 && is_array($model['answerArr'][$i][$y][$z])) {
                                                for ($w = 0; $w <= count($model['answerArr'][$i][$y][$z]) - 1; $w++) {

                                                    if ($flag === 0) {
                                                        $endFlag = 1;
                                                        echo '
                                                        <table class="table table-striped">

                                                            <caption>Лог обработки файла</caption>
                                        
                                                            <tr>
                                                                <th>№<br>строки</th>
                                                                <th>Наименование клиента</th>
                                                                <th>Номер договора</th>
                                                                <th>Ошибка</th>
                                                            </tr>
                                        
                                                            <tbody>';
                                                    }


                                                    $info = explode('&', $model['answerArr'][$i]['info']);
                                                    echo '<tr><td>' . $countLine . ') </td><td>' . $info[0] . '</td><td>' . $info[1] . '</td><td><strong>' . $model['answerArr'][$i][$y][$z][$w] . '</strong></td></tr>';

                                                    $flag = 1;
                                                }
                                            }
                                    }
                                }
                        }

                    if($endFlag === 1) echo '</tbody></table><br><br><strong>Исправьте ошибки и повторите операцию. Для перехода в основное меню нажмите на ссылку слева сверху!</strong>';
                    ?>


                <?php

                if($flag === 0) {
                    echo '<strong>Ошибок не выявлено!!!</strong><br>';
                    echo '<a href="'.Url::toRoute(['site/download-file', 'id' => $model->fileName]).'"><button type="button" class="btn btn-success">Скачать исправленный файл</button></a>';
                }
                
                ?>

            </div>
    </div>

</div>

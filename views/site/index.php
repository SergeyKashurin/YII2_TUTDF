<?php

/* @var $this yii\web\View */

$this->title = 'Работа с Кредитными Историями';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Работа с Кредитными Историями!</h1>

        <p class="lead">Данный сервис направлен на уменьшение количества ошибок при передаче информации о КИ в НБКИ.</p>

        <p><a class="btn btn-lg btn-success" href="?r=site/server-index">Сервер работы с КИ</a></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Физ. лица</h2>

                <p>Формирование кредитной истории физических лиц. (Дифференцированный график)</p>

                <p><a class="btn btn-primary" href="?r=site/fizlic">Начать &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>ИП</h2>

                <p>Формирование кредитной истории индивидуальных предпринимателей. (Ануитентный график)</p>

                <p><a class="btn btn-primary" href="?r=site/ip">Начать &raquo;</a></p>
            </div>
                <div class="col-lg-4">
                    <h2>ЮР. лица</h2>

                    <p>Формирование кредитной истории юридических лиц (организаций). (Ануитентный график)</p>

                    <p><a class="btn btn-primary" href="?r=site/urlic">Начать &raquo;</a></p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <h2>Заявки</h2>

                <p>Формирование заявок физических лиц.</p>

                <p><a class="btn btn-primary" href="?r=site/zayavka">Начать &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>- - -</h2>
            </div>
            <div class="col-lg-4">
                <h2>BV</h2>

                <p>Сканирование и исправление ошибок BV файла</p>

                <p><a class="btn btn-warning" href="?r=site/bv">Перейти &raquo;</a></p>
            </div>
        </div>

    </div>

</div>

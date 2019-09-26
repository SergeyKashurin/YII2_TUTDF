<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;

$this->title = 'Отчёт по BV файлу';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <?='Информация о файле: <strong>'.$model['txtFile']->name.'</strong>'; ?>

    <?php
    foreach($model['rezult'] as $value)
            echo $value;
    ?>

</div>

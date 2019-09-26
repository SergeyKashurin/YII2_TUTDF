<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Загрузка BV файла';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Чтобы начать - загрузите файл:</p>

    <div class="col-lg-offset-1" style="color:#999;">
        1. Нажмите кнопку <strong>выберите файл</strong>, далее <strong>загрузить</strong>.<br>
        2. Все операции связанные с проверкой и корректировкой файла выполнятся <code>автоматически</code>.<br>
        3. По результатам работы, система выдаст вам <strong>полный отчёт</strong> содержащий сведения об ошибках и информацию о внесённых изменениях.<br>
    </div>

    <hr>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data',]]) ?>
        <?= $form->field($model, 'txtFile')->label('Выберите текстовый BV файл')->fileInput() ?>
            <button>Загрузить</button>
    <?php ActiveForm::end() ?>

</div>

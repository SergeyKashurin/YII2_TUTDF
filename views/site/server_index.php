<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Server';
?>

<div class="row">
    <!--
        <div class="col-lg-6">
            <address>
                <strong>Информация о Ки в БД</strong><br>
                Активные кредиты: 5<br>
                Всего кредитов: 7<br>
                <abbr title="Phone">Ошибок в БД:</abbr> 25
            </address>
        </div>
-->
        <div class="col-lg-12">

            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            <?=$form->field($model, 'kiFile')->label('Для проверки файла КИ нажмите кнопку загрузить')->fileInput(); ?>
            <button>Загрузить</button>
            <?php ActiveForm::end(); ?>

        </div>
    </div>

    <!--
    <div class="row">
        <div class="col-lg-12">
            <div class="table-hover">
                <table class="table">
                    <caption>Информация о КИ в БД</caption>
                    <tr>
                        <th>№<br>п/п</th>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>раз</td>
                        <td>два</td>
                        <td>1
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    -->

</div>

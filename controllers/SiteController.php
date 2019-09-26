<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

use app\models\Zayavka;
use app\models\Fizlic;
use app\models\Urlic;
use app\models\Ip;
use app\models\Bv;

/*
 * Подключаемые файлы для работы
 * сервера НБКИ
 */
use app\models\Server;

use yii\web\UploadedFile;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    /*
     * Главная страница Формирование кредитной истории физических лиц. (Дифференцированный график)
     */
    public function actionFizlic()
    {
        $model = new Fizlic();

        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            Fizlic::find()->where(['KredScet' => $model->KredScet])->one() ?  0 : $model->save();

            $model->generateFizlic($model->counts);
            return $this->render('index');
        }
        return $this->render('fizlic', ['model' => $model]);
    }

    /*
     * Главная страница Формирование кредитной истории юридических лиц. (Ануитентный график)
     */
    public function actionUrlic()
    {
        $model = new Urlic();

        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
        Urlic::find()->where(['KredScet' => $model->KredScet])->one() ?  0 : $model->save();

        $model->generateUrlic($model->counts);
        return $this->render('index');
        }
        return $this->render('urlic', ['model' => $model]);
    }

    /*
     * Главная страница Формирование кредитной истории физических лиц. (Дифференцированный график)
     */
    public function actionZayavka()
    {
        $model = new Zayavka();

        if($model->load(Yii::$app->request->post()) && $model->validate())
        {

            Zayavka::find()->where(['NomerDogovoraPoruchitelstva' => $model->NomerDogovoraPoruchitelstva])->one() ?  0 : $model->save();

            $model->generateZayavka();
            return $this->render('index');
        } else
        {
            # Если не были введены данные в форму, выводим форму
            return $this->render('zayavka', ['model' => $model]);
        }

    }

    /*
     * Главная страница Формирование кредитной истории индивидуальных предпринимателей. (Дифференцированный график)
     */
    public function actionIp()
    {
        $model = new Ip();

        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            Ip::find()->where(['KredScet' => $model->KredScet])->one() ?  0 : $model->save();

            $model->generateIp($model->counts);
            return $this->render('index');
        }
        return $this->render('ip', ['model' => $model]);
    }

    /*
     * Работа с BV файлами, обработка загрузки, проверка, исправление и выдача обратно клиенту
     */
    public function actionBv()
    {
        $model = new Bv;
        if (Yii::$app->request->isPost) {
            $model->txtFile = UploadedFile::getInstance($model, 'txtFile');
            if ($model->upload()) {
                // file is uploaded successfully
                return $this->render('bv_answer', ['model' => $model]);
            }
        }
        return $this->render('bv', ['model' => $model]);
    }

    public function actionDownloadFile()
    {
        $file = $_GET['id'];
        // отдаем файл
        \Yii::$app->response->sendFile($file)->send();
    }

    /*
     * Стартовая страница сервера обработки КИ
     */
    public function actionServerIndex()
    {
        $model = new Server;
        if (Yii::$app->request->isPost) {
            $model->kiFile = UploadedFile::getInstance($model, 'kiFile');
            if ($model->upload()) {
                // file is uploaded successfully
                return $this->render('server_index_load_file', ['model' => $model]);
                #return $this->redirect('http://example.com/new', 301);
            }
        }
        return $this->render('server_index', ['model' => $model]);
    }

    /*
     * Загрузка КИ на сервер
     */
/*    public function actionServerIndexLoadFile()
    {
        $model = new Server;
        if (Yii::$app->request->isPost) {
            $model->txtFile = UploadedFile::getInstance($model, 'kiFile');
            if ($model->upload()) {
                // file is uploaded successfully
                return $this->render('server_index_load_file', ['model' => $model]);
            }
        }
        return $this->render('server_index', ['model' => $model]);
    }*/

}

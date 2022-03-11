<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\form\LoginForm;
use app\models\form\SignupForm;
use app\models\Prize;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use app\models\user\Info;

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
		if (Yii::$app->user->isGuest) {
			return $this->redirect('login');
        }
		
		$dataProvider = new ActiveDataProvider([
			'query' => Prize::find()->where(['user_id' => Yii::$app->user->id, 'refused' => 0]),
			'sort' => [
				'defaultOrder' => ['id' => SORT_DESC]
			],
			'pagination' => [
				'pageSize' => 5,
				'pageSizeParam' => false,
			],
		]);
		
        $model = new Prize();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$model->updateAttributes(['value' => $model->generateValue()]);
            return $this->redirect('/');
        }
		
		return $this->render('index', [
            'model' => $model,
			'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
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

		return $this->redirect('login');
    }
	
	public function actionSignup()
    {
		if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
		
        $model = new SignupForm();
		if ($model->load(Yii::$app->request->post()) && $model->signup()) {
			Yii::$app->user->login($model->getUser(), 0);
            return $this->goHome();
        }
		
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

	public function actionRefuse($id)
    {
		$model = $this->findModel($id);
		$model->refused = 1;
		$model->save();
		
		Yii::$app->session->setFlash('info', "{$model->type} prize has been refused.");

        return $this->redirect('/');
    }

	public function actionTransfer($id)
    {
		$model = $this->findModel($id);
		if ($model->type == Prize::TYPE_MONEY) {
			Prize::doMoneyTransfer($model->user_id, $model->value);
			$unit = 'euro'; $acc = 'bank';
		} else if ($model->type == Prize::TYPE_BONUS) {
			Info::doBonusTransfer($model->value);
			$unit = 'points'; $acc = 'loyalty';
		} else {
			$model->refused = 1;
			$model->send = 1;
			$model->save();
			Yii::$app->session->setFlash('success', "we will send {$model->value} to you by post.");
			return $this->redirect('/');
		}
		
		Yii::$app->session->setFlash('success', "{$model->value} {$unit} transfered to your {$acc} account.");
		
        $model->delete();
        return $this->redirect('/');
    }

	public function actionConvert($id)
    {
        $model = $this->findModel($id);
		$model->type = Prize::TYPE_BONUS;
		$value = $model->value;
		$model->value = $value / Prize::LOYALTY_COEFF;
		$model->save();
		
		Yii::$app->session->setFlash('success', "{$value} euros converted to {$model->value} loyalty bonus.");
		
        return $this->redirect('/');
    }

	protected function findModel($id)
    {
        if (($model = Prize::findOne($id)) !== null) {
            return $model;
        }
		
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

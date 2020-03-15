<?php

namespace app\controllers;

use app\models\Question;
use app\models\UserScore;
use jinxing\admin\helpers\Helper;
use jinxing\admin\traits\JsonTrait;
use Yii;
use yii\caching\ZendDataCache;
use yii\filters\AccessControl;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\User;

/**
 * Site controller
 */
class SiteController extends Controller
{
    use JsonTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['logout', 'register', 'captcha'],
                'rules' => [
                    [
                        'actions' => ['register', 'captcha'],
                        'allow'   => true,
                        'roles'   => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'captcha'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class'           => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'maxLength'       => 4,           // 最大显示个数
                'minLength'       => 4,           // 最少显示个数
                'padding'         => 5,           // 间距
                'height'          => 38,          // 高度
                'width'           => 130,         // 宽度
                'offset'          => 4,           // 设置字符偏移量 有效果
            ],
        ];
    }

    public function actionIndex()
    {
        $guanId = Yii::$app->request->get('guan_id', 1);
        $courseId = Yii::$app->request->get('course_id', 1);
        $number = Yii::$app->request->get('number', 1);
        $where = [
            'guan_id' => $guanId,
            'course_id' => $courseId,
        ];
        $count =  Question::find()->where($where)->count();
        $info = Question::find()->select('question_title,id,question_content,question_img,answers,answer_type')->where([
            'guan_id' => $guanId,
            'course_id' => $courseId,
        ])->asArray()->offset($number - 1)->one();
        $info['answers'] = json_decode($info['answers'],true);
        $answerType = Question::getTypeDesc();
        return $this->render('index', compact('info', 'count','answerType'));
    }

    public function actionAnswer(){
        $guanId = Yii::$app->request->get('guan_id', 1);
        $courseId = Yii::$app->request->get('course_id', 1);
        $number = Yii::$app->request->get('number', 1);
        $where = [
            'guan_id' => $guanId,
            'course_id' => $courseId,
        ];
        $count =  Question::find()->where($where)->count();
        $info = Question::find()->select('question_title,id,question_content,question_img,answers,answer_type,answer_id')->where([
            'guan_id' => $guanId,
            'course_id' => $courseId,
        ])->asArray()->offset($number - 1)->one();
        $info['answers'] = json_decode($info['answers'],true);
        $info['answer_id'] = json_decode($info['answer_id'],true);
        $answerType = Question::getTypeDesc();
        $numberLetter = range('A','Z');
        $info['number_letter'] = json_encode($numberLetter);
        $info['answer_string'] = '';
        if(is_array($info['answer_id'])){
            foreach ($info['answer_id'] as $answer) {
                $info['answer_string'] .= $numberLetter[$answer];
            }
        }else{
            $info['answer_string'] = $numberLetter[$info['answer_id']];
        }

        return $this->render('answer', compact('info', 'count','answerType'));
    }
    /**
     * 提交答案
     */
    public function actionSubmit(){
        $guanId = Yii::$app->request->post('guan_id', 1);
        $courseId = Yii::$app->request->post('course_id', 1);
        $myAnswer = Yii::$app->request->post('myAnswer');
        $token = Yii::$app->request->post('token');
        $where = [
            'guan_id' => $guanId,
            'course_id' => $courseId,
        ];
        $list = Question::find()->select('id,answer_id')->where($where)->asArray()->all();
        $total = count($list);
        $true = 0;
        foreach ($list as $item) {
            $answer = json_decode($item['answer_id'],true);
            if(!empty($myAnswer[$item['id']]) && $answer == $myAnswer[$item['id']]){
                $true += 1;
            }
        }
        $post = [
            'guan_id'=> $guanId,
            'course_id'=> $courseId,
            'token'=> $token,
            'total_num'=> $total,
            'true_num'=> $true,
        ];
        return $this->success([]);


    }


    protected function login($message = 'login')
    {
        /* @var $user User */
        $user = Yii::$app->user->identity;
        return $this->success([
            'username' => $user->username,
            'email'    => $user->email,
            'face'     => $user->face,
        ], $message == 'login' ? '登录成功' : '注册成功');
    }

    /**
     * actionLogin() 用户登录
     *
     * @return mixed|string
     */
    public function actionLogin()
    {
        // 用户没有登录
        if (!Yii::$app->user->isGuest) {
            return $this->login();
        }

        $model = new LoginForm();
        if (!$model->load(Yii::$app->request->post(), '') || !$model->login()) {
            return $this->error(1, Helper::arrayToString($model->getErrors()));
        }

        return $this->login();
    }

    /**
     * actionLogout用户退出
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        // 退出之前修改登录信息
        if ($user = User::findOne(Yii::$app->user->id)) {
            $user->last_time = time();
            $user->last_ip   = Helper::getIpAddress();
            $user->save();
        }

        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * actionRegister() 用户注册
     *
     * @return string|\yii\web\Response
     */
    public function actionRegister()
    {
        // 已经登录
        if (!Yii::$app->user->isGuest) {
            return $this->login();
        }

        // 不是ajax 请求
        if (!Yii::$app->request->isAjax) {
            return $this->error();
        }

        $model = new RegisterForm();
        // 数据加载成功
        if (!$model->load(Yii::$app->request->post(), '')) {
            return $this->error();
        }

        if (!($user = $model->register()) || !Yii::$app->getUser()->login($user)) {
            return $this->error(2, Helper::arrayToString($model->getErrors()));
        }

        return $this->login('registerSuccess');
    }
}

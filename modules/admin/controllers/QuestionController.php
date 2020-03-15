<?php

namespace app\modules\admin\controllers;

use app\models\CarType;
use app\models\Chapter;
use app\models\Course;
use app\models\Guan;
use app\models\Question;
use app\models\Special;
use app\models\Subject;
use jinxing\admin\controllers\Controller;
use jinxing\admin\helpers\Helper;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii;

/**
 * Class QuestionController 题库信息
 * @package app\modules\admin\controllers
 */
class QuestionController extends Controller
{
    public $modelClass = 'app\models\Question';

    public $uploadFromClass = 'app\models\UploadForm';

    /**
     * where() 查询处理
     *
     *
     * @return array 返回数组
     */
    public function where()
    {
        return [
            [['status', 'answer_type', 'subject_id', 'chapter_id', 'special_id'], '='],
        ];
    }

    /**
     * actionIndex() 首页显示
     * @return string
     */
    public function actionIndex()
    {
        // 获取数据
        $special = Special::find()->where(['!=', 'pid', 0])->orderBy('sort')->all();
        $course = $this->getCourse();
        // 专项分类
        $special = Helper::map($special, 'id', 'name');

        return $this->render('index', [
            'subject'     => Json::encode($course['course']), // 科目
            'arrSubject'  => $course['course'], //课程,
            'special'     => Json::encode($special), // 专项
            'arrSpecial'  => $special,
            'chapter'     => Json::encode($course['guan']), // 章节
            'arrChapter'  => $course['guan'],
            'status'      => Json::encode(Question::getStatusDesc()),           // 状态
            'color'       => Json::encode(Question::getStatusColor()),           // 状态颜色
            'type'        => Json::encode(Question::getTypeDesc()),               // 答案类型
        ]);
    }

    public function actionCreate()
    {
        $request = \Yii::$app->request;
        if (!$request->isGet && $request->isPost) {
            return parent::actionCreate();
        }

        $intSubject = (int)$request->get('subject_id');
        $intChapter = (int)$request->get('chapter_id');
        if ($intChapter > 0) {
            if ($chapter = Chapter::findOne($intChapter)) {
                $intSubject = $chapter->subject_id;
            } else {
                $intChapter = 0;
            }
        }

        // 查询数据
        $special    = Special::find()->where(['!=', 'pid', 0])->orderBy('sort')->all();
        $chapter    = Chapter::find()->orderBy('sort')->all();
        $chapter[]  = ['id' => 0, 'name' => '请选择'];
        $subject    = Subject::getSubject();
        $subject[0] = '请选择';

        // 载入视图
        return $this->render('create', [
            'subject_id' => $intSubject,
            'chapter_id' => $intChapter,
            'subject'    => $subject,
            'chapter'    => ArrayHelper::map($chapter, 'id', 'name'),
            'types'      => Question::getTypeDesc(),
            'special'    => ArrayHelper::map($special, 'id', 'name')
        ]);
    }

    private function getToken(){
        $json = file_get_contents('http://jajava.cn/getToken?token_key=eyJpdiI6IktDMnYxREZZZ3pUTXU2RGJrYzlBNkE9PSIsInZhbHVlIjoiRFRFODU1YlZrRHhiRzJSZzZFVTNDemIzVUdFbUVsMGFubW9jdEY1aWd3dz0iLCJtYWMiOiIyZGVjYWIzNjAzYzQ2OTAwZGE3ZTNhNTliN2EzMDBlOTUyYWJjYjM5YWJjNGQ3NjljYmEzODc0ZWNhMmU1ZGU5In0=');
        $array = json_decode($json,true);
        if(empty($array['data']['token'])){
            return '';
        }
        return $array['data']['token'];
    }

    private function getCourse($intSid = 0){
//        $json = $this->sendPost(Yii::$app->params['courseUrl'],['token'=>$this->getToken()]);
//        $array = json_decode($json,true);
//        if(empty($array)){
//            return [];
//        }

        $array = [
            'data' => [
                '31' => [
                    [
                        'course_id' => 31,
                        'course_name' => '11111',
                        'guan_id' => 1,
                        'guan_name' => '22222',
                    ],
                    [
                        'course_id' => 31,
                        'course_name' => '22222',
                        'guan_id' => 2,
                        'guan_name' => '24442222',
                    ]
                ]
            ]
        ];
        $result = [];


        foreach ($array['data'] as $courseId => $datum) {
            foreach ($datum as $guanArray) {
                $result['course'][$courseId] = $guanArray['course_name'];
                $result['guan'][$guanArray['guan_id']] = $guanArray['guan_name'];
                $result['guanArray'][] = [
                    'id' => $guanArray['guan_id'],
                    'name' => $guanArray['guan_name'],
                ];
            }
        }
        return $result;
    }



    /**
     * 发送post请求
     * @param string $url 请求地址
     * @param array $post_data post键值对数据
     * @return string
     */
    public function sendPost($url, $post_data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //证书验证关闭
        //        #设置为 1 是检查服务器SSL证书中是否存在一个公用名(common name)。译者注：公用名(Common Name)一般来讲就是填写你将要申请SSL证书的域名 (domain)或子域名(sub domain)。 设置成 2，会检查公用名是否存在，并且是否与提供的主机名匹配。 0 为不检查名称。 在生产环境中，这个值应该是 2（默认值
        //                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, '0');
        //        #禁止 cURL 验证对等证书（peer's certificate）。要验证的交换证书可以在 CURLOPT_CAINFO 选项中设置
        //                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, '0');
        $response = curl_exec($ch);
        return $response;
    }

    /**
     * actionSubject() 获取科目
     * @return mixed|string
     */
    public function actionSubject()
    {
        $request = \Yii::$app->request;
        if (!$request->isAjax || !($intCid = (int)$request->post('cid'))) {
            return $this->error(201);
        }

        return $this->success(Subject::findAll(['status' => 1, 'car_id' => $intCid]));
    }

    /**
     * actionChapter() 获取章节
     *
     * @return mixed|string
     */
    public function actionChapter()
    {
        $request = \Yii::$app->request;
        if (!$request->isAjax || !($intSid = (int)$request->post('sid'))) {
            return $this->error(201);
        }
        return $this->success($this->getCourse($intSid)['guanArray']);
    }

    /**
     * 处理导入数据
     *
     * @return mixed|string
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws yii\db\Exception
     */
    public function actionUploadQuestion()
    {
        // 判断请求方式
        $request = \Yii::$app->request;
        if (!$request->isAjax) {
            return $this->error(201);
        }

        // 接收参数
        $intSubject = (int)$request->post('subject_id');    // 科目信息
        $intChapter = (int)$request->post('chapter_id');    // 章节信息
        $intSpecial = (int)$request->post('special_id');    // 专项信息
        $strFile    = trim($request->post('upload_file'));  // 文件地址

        // 验证提交数据
        if (empty($strFile) || (empty($intSubject) && empty($intChapter))) {
            return $this->error(201);
        }

        // 查询出全部专项信息
        $arrSpecial = Special::find()->where(['!=', 'pid', 0])->indexBy('name')->all();
        if (!$objPHPExcel = \PHPExcel_IOFactory::load('.' . $strFile)) {
            return $this->error(201, '上传文件加载失败');
        }

        $objWorksheet = $objPHPExcel->getActiveSheet();
        $intRows      = $objWorksheet->getHighestRow(); // 获取总行数

        // 读取数据的第二行
        $array  = [];
        $time   = time();
        $fields = [
            'question_title',
            'question_content',
            'answer_type',
            'answers',
            'answer_id',
            'subject_id',
            'chapter_id',
            'created_at',
            'updated_at',
            'special_id',
        ];

        $db = Yii::$app->db;
        for ($i = 2; $i <= $intRows; $i++) {
            // 获取题目类型
            $intAnswerType = (int)$objWorksheet->getCell('C' . $i)->getValue();

            // 获取正确答案信息
            $answer = trim($objWorksheet->getCell('E' . $i)->getValue());
            if ($intAnswerType === Question::ANSWER_TYPE_MULTI) {
                $mixAnswerId = explode(',', $answer);
                $mixAnswerId = array_map(function ($value) {
                    return $value - 1;
                }, $mixAnswerId);
                $mixAnswerId = Json::encode($mixAnswerId);
            } else {
                $mixAnswerId = $answer > 0 ? (int)$answer - 1 : 0;
            }

            // 处理专项信息
            $special = trim($objWorksheet->getCell('F' . $i)->getValue());
            if ($special && isset($arrSpecial[$special])) {
                $special = $arrSpecial[$special]->id;
            } else {
                $special = $intSpecial;
            }

            $array[] = [
                $objWorksheet->getCell('A' . $i)->getValue(),
                $objWorksheet->getCell('B' . $i)->getValue(),
                $intAnswerType,
                Json::encode(explode('|', $objWorksheet->getCell('D' . $i)->getValue())),
                $mixAnswerId,
                $intSubject,
                $intChapter,
                $time,
                $time,
                $special,
            ];

            // 每100条数据新增一次
            if (count($array) >= 100 && $db->createCommand()->batchInsert('{{%question}}', $fields, $array)->execute()) {
                $array = [];
            }
        }

        // 存在数据，并且新增失败、报错误
        if ($array && !$db->createCommand()->batchInsert('{{%question}}', $fields, $array)->execute()) {
            return $this->error(404, '上传题目处理失败');
        }

        return $this->success($array, '上传题目处理成功');
    }
}

<?php

namespace app\modules\admin\controllers;

use Yii;

use app\common\strategy\Substance;
use app\modules\admin\models\Admin;
use app\common\models\UploadForm;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\web\UnauthorizedHttpException;

/**
 * Class    PraiseController
 * @package backend\controllers
 * Desc     后台公共的控制器
 * User     liujx
 * Date     2016-4-8
 */
class Controller extends \app\common\controllers\Controller
{
    // 'enableCsrfValidation' => true // 配置文件关闭CSRF
    public    $admins = null;    //
    protected $sort = 'id';    // 默认排序字段
    protected $strategy = 'DataTables'; // 数据显示使用方式
    protected $pk = 'id';

    /**
     * beforeAction() 请求之前的数据验证
     * @param \yii\base\Action $action
     * @return bool
     * @throws UnauthorizedHttpException
     */
    public function beforeAction($action)
    {
        // 主控制器验证
        if (parent::beforeAction($action)) {
            // 验证权限
            if(!Yii::$app->admin->can($action->controller->id . '/' . $action->id) && Yii::$app->getErrorHandler()->exception === null) {
                // 没有权限AJAX返回
                if (Yii::$app->request->isAjax)
                    exit(Json::encode(['errCode' => 216, 'errMsg' => '对不起，您现在还没获得该操作的权限!', 'data' => []]));
                else
                    throw new UnauthorizedHttpException('对不起，您现在还没获得该操作的权限!');
            }

            // 处理获取数据
            if (!in_array($action->id, ['create', 'update', 'delete'])) {
                $this->admins = ArrayHelper::map(Admin::findAll(['status' => 1]), 'id', 'username');
                // 注入变量信息
                Yii::$app->view->params['admins'] = $this->admins;
                Yii::$app->view->params['user']   = Yii::$app->admin->identity;
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * actionIndex() 首页显示
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * where() 获取查询的配置信息(查询参数)
     * @access protected
     * @param  array $params 查询的请求参数
     * @return array 返回一个数组用来查询
     */
    protected function where($params)
    {
        return [];
    }


    public function handleWhere($params, $where)
    {
        $arrReturn = [];
        if ($where) {
            // 默认查询
            if (isset($where['where']) && !empty($where['where'])) {
                $arrReturn = $where['where'];
                unset($where['where']);
            }

            // 处理其他查询
            if ($where && $params) {
                foreach ($params as $key => $value) {
                    if (isset($where[$key]) && $value !== "") {
                        $v = $where[$key];

                        // 判断字符串类型处理
                        if (is_string($v)) {
                            $arrReturn[] = [$v, $key, $value];
                        // 判断数组处理
                        } else if (is_array($v)) {
                            // 处理函数
                            if (isset($v['func']) && function_exists($v['func'])) {
                                $value = $v['func']($value);
                            }

                            // 对应字段
                            if (!isset($v['field']) || empty($v['field'])) {
                                $v['field'] = $key;
                            }

                            // 链接类型
                            if (!isset($v['and']) || empty($v['and'])) {
                                $v['and'] = '=';
                            }

                            $arrReturn[] = [$v['and'], $v['field'], $value];
                        // 对象处理（匿名函数）
                        } else if (is_object($v)) {
                            $arrReturn[] = $v($key, $value);
                        }
                    }
                }
            }

            if ($arrReturn) array_unshift($arrReturn, 'and');
        }

        return $arrReturn;
    }

    /**
     * afterSearch() 查询之后的数据处理函数
     * @access protected
     * @param  mixed $array 查询出来的数组对象
     * @return void  对数据进行处理
     */
    protected function afterSearch(&$array)
    {

    }

    /**
     * actionSearch() 处理查询数据
     * @return mixed|string
     */
    public function actionSearch()
    {
        // 实例化数据显示类
        $strategy = Substance::getInstance($this->strategy);

        // 获取查询参数
        $search = $strategy->getRequest(); // 处理查询参数
        $search['field'] = $search['field'] ? $search['field'] : $this->sort;
        $search['orderBy'] = [$search['field'] => $search['sort'] == 'asc' ? SORT_ASC : SORT_DESC];
        $search['where'] = $this->handleWhere($search['params'], $this->where($search['params']));

        // 查询之前的处理
        $query = $this->getModel()->find()->where($search['where']);

        // 查询数据条数
        $total = $query->count();
        if ($total) {
            $model = $query->offset($search['offset'])->limit($search['limit'])->orderBy($search['orderBy']);
            $array = $model->all();
            if ($array) $this->afterSearch($array);
            $this->arrJson['other'] = $model->createCommand()->getRawSql();
        } else {
            $array = [];
        }

        // 处理返回数据
        $this->arrJson['errCode'] = 0;
        $this->arrJson['data'] = $strategy->handleResponse($array, $total);

        // 返回JSON数据
        return $this->returnJson();
    }

    /**
     * actionInsert() 处理新增数据
     * @return mixed|string
     */
    public function actionCreate()
    {
        $data = Yii::$app->request->post();
        if ($data) {
            $model  = $this->getModel();
            $arrScenarios = $model->scenarios();
            if (isset($arrScenarios['create'])) {
                $model->scenario = 'create';
            }

            $isTrue = $model->load(['params' => $data], 'params');
            if ($isTrue) {
                $isTrue = $model->save();
                $this->arrJson['errMsg'] = $model->getErrorString();
                if ($isTrue) $this->handleJson($model);
            }

        }

        // 返回数据
        return $this->returnJson();
    }

    /**
     * actionUpdate() 处理修改数据
     * @return mixed|string
     */
    public function actionUpdate()
    {
        // 接收参数判断
        $data = Yii::$app->request->post();
        if ($data && isset($data[$this->pk]) && !empty($data[$this->pk])) {
            // 接收参数
            $model = $this->getModel()->findOne($data[$this->pk]);
            if ($model) {
                $arrScenarios = $model->scenarios();
                if (isset($arrScenarios['update'])) {
                    $model->scenario = 'update';
                }

                // 修改数据
                $this->arrJson['errCode'] = 205;
                $isTrue = $model->load(['params' => $data], 'params');
                if ($isTrue) {
                    $isTrue = $model->save();
                    $this->arrJson['errMsg'] = $model->getErrorString();
                    if ($isTrue) $this->handleJson($model);
                }
            }
        }

        // 返回数据
        return $this->returnJson();
    }

    /**
     * actionDelete() 处理删除数据
     * @return mixed|string
     */
    public function actionDelete()
    {
        $data = Yii::$app->request->post();
        if ($data && isset($data[$this->pk]) && !empty($data[$this->pk])) {
            $model = $this->getModel()->findOne($data[$this->pk]);
            $this->arrJson['errCode'] = 222;
            if ($model) {
                if ($model->delete()) {
                    $this->handleJson($model);
                } else {
                    $this->arrJson['errMsg'] = $model->getErrorString();
                }
            }
        }

        return $this->returnJson();
    }

    /**
     * actionDeleteAll()批量删除操作
     * @return mixed|string
     */
    public function actionDeleteAll()
    {
        $ids = Yii::$app->request->post('ids');
        if ($ids) {
            $model = $this->getModel();
            $this->arrJson['errCode'] = 220; // 查询数据不存在
            if ($model->deleteAll([$this->pk => explode(',', $ids)])) {
                $this->handleJson($ids);
            }
        }

        return $this->returnJson();
    }

    /**
     * actionEditable 处理行内编辑
     * @return mixed|string
     */
    public function actionEditable()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            // 接收参数
            $mixPk    = $request->post('pk');    // 主键值
            $strAttr  = $request->post('name');  // 字段名
            $mixValue = $request->post('value'); // 字段值
            $this->arrJson['errCode'] = 207;
            if ($mixPk && $strAttr  && $mixValue != '') {
                // 查询到数据
                $model = $this->getModel()->findOne($mixPk);
                $this->arrJson['errCode'] = 220;
                if ($model) {
                    $model->$strAttr = $mixValue;
                    $this->arrJson['errCode'] = 206;
                    if ($model->save()) {
                        $this->handleJson($model);
                    } else {
                        $this->arrJson['errMsg'] = $model->getErrorString();
                    }
                }
            }
        }

        // 返回数据
        return $this->returnJson();
    }

    /**
     * getUploadPath() 获取上传文件目录(默认是相对路径 ./public/uploads)
     * @access protected
     * @return string 返回上传文件的目录地址(相对于index.php文件的目录)
     */
    protected function getUploadPath()
    {
        return './public/uploads/';
    }

    /**
     * afterUpload() 文件上传成功的处理信息
     * @access protected
     * @param  object $object     文件上传类
     * @param  string $strFilePath 文件保存路径
     * @param  string $strField    上传文件表单名
     * @return bool 上传成功返回true
     */
    public function afterUpload($object, &$strFilePath, $strField)
    {
        return true;
    }

    /**
     * actionUpload() 处理文件上传操作
     * @return mixed|string
     */
    public function actionUpload()
    {
        // 定义请求数据
        $request = Yii::$app->request;
        if ($request->isPost) {
            // 接收参数
            $strField = $request->get('sField');    // 上传文件表单名称
            if ( ! empty($strField)) {
                // 判断删除之前的文件
                $strFile  = $request->post($strField);   // 旧的地址
                if (! empty($strFile) && file_exists('.'.$strFile)) unlink('.'.$strFile);

                $model = new UploadForm();
                $model->scenario = $strField;
                try {
                    $objFile = $model->$strField = UploadedFile::getInstance($model, $strField);
                    $this->arrJson['errCode'] = 221;
                    if ($objFile) {
                        $isTrue = $model->validate();
                        $this->arrJson['errMsg'] = $model->getFirstError($strField);
                        if ($isTrue) {
                            // 创建目录
                            $dirName = $this->getUploadPath();
                            if ( ! file_exists($dirName)) mkdir($dirName, 0777, true);
                            $this->arrJson['errCode'] = 202;
                            $this->arrJson['data'] = $dirName;
                            if (file_exists($dirName)) {
                                // 生成文件随机名
                                $strFileName = uniqid() . '.';
                                $strFilePath = $dirName. $strFileName. $objFile->extension;
                                $this->arrJson['errCode'] = 204;
                                if ($objFile->saveAs($strFilePath) && $this->afterUpload($objFile, $strFilePath, $strField)) {
                                    $this->handleJson([
                                        'sFilePath' => trim($strFilePath, '.'),
                                        'sFileName' => $objFile->baseName.'.'.$objFile->extension,
                                    ]);
                                }
                            }
                        }
                    }

                } catch (\Exception $e) {
                    $this->handleJson([], 203, $e->getMessage());
                }
            }
        }

        return $this->returnJson();
    }

    /**
     * handleExport() 处理需要导出的数据显示问题
     * @param array $arrObject 查询到的对象数组
     */
    protected function handleExport(&$arrObject)
    {

    }

    /**
     * actionExport() 文件导出处理
     * @return mixed|string
     */
    public function actionExport()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            // 接收参数
            $arrFields = $request->post('fields');         // 字段信息
            $strTitle  = $request->post('title');          // 标题信息
            $params = $request->post('params'); // 查询条件信息

            // 判断数据的有效性
            if ($arrFields && $strTitle) {
                // 获取数据
                $arrKeys   = array_keys($arrFields);        // 所有的字段
                $query = $this->getModel()
                    ->find()
                    ->where($this->handleWhere($params, $this->where($params)))
                    ->orderBy([$this->sort => SORT_DESC]);

                $intCount = $query->count();
                // 判断数据是否存在
                $this->arrJson['errCode'] = 220;
                if ($intCount > 0) {
                    ob_end_clean();
                    ob_start();
                    $objPHPExcel = new \PHPExcel();
                    $objPHPExcel->getProperties()->setCreator("Liujx Admin")
                        ->setLastModifiedBy("Liujx Admin")
                        ->setTitle("Office 2007 XLSX Test Document")
                        ->setSubject("Office 2007 XLSX Test Document")
                        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                        ->setKeywords("office 2007 openxml php")
                        ->setCategory("Test result file");
                    $objPHPExcel->setActiveSheetIndex(0);

                    // 获取显示列的信息
                    $intLength = count($arrFields);
                    $arrLetter = range('A', 'Z');
                    if ($intLength > 26) {
                        $arrLetters = array_slice($arrLetter, 0, $intLength - 26);
                        if ($arrLetters) foreach ($arrLetters as $value) array_push($arrLetter, 'A'.$value);
                    }

                    $arrLetter = array_slice($arrLetter, 0, $intLength);

                    // 确定第一行信息
                    foreach ($arrLetter as $key => $value) {
                        $objPHPExcel->getActiveSheet()->setCellValue($value.'1', $arrFields[$arrKeys[$key]]);
                    }

                    // 写入数据信息
                    $intNum = 2;
                    foreach ($query->batch(1000) as $array) {
                        foreach ($array as $value) {
                            // 处理查询到的数据
                            $this->handleExport($value);
                            // 写入信息数据
                            foreach ($arrLetter as $intKey => $strValue) {
                                $tmpAttribute = $arrKeys[$intKey];
                                $objPHPExcel->getActiveSheet()->setCellValue($strValue.$intNum, $value->$tmpAttribute);
                            }

                            $intNum ++;
                        }
                    }

                    // 设置sheet 标题信息
                    $objPHPExcel->getActiveSheet()->setTitle($strTitle);
                    $objPHPExcel->setActiveSheetIndex(0);

                    // 设置头信息
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="'.$strTitle.'.xlsx"');
                    header('Cache-Control: max-age=0');
                    header('Cache-Control: max-age=1');
                    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');           // Date in the past
                    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');  // always modified
                    header('Cache-Control: cache, must-revalidate');            // HTTP/1.1
                    header('Pragma: public');                                   // HTTP/1.0

                    // 直接输出文件
                    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                    $objWriter->save('php://output');
                    Yii::$app->end();
                }
            }
        }

        return $this->returnJson();
    }

    /**
     * getModel() 获取model对象
     * @return Admin
     */
    protected function getModel()
    {
        return new Admin();
    }
}

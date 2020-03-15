<?php

use yii\helpers\Url;

$this->title = '首页';
$this->registerCssFile('@web/css/index.css', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/exam_files/base.css', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/exam_files/default.css', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/exam_files/exercise.css', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/exam_files/global(1).css', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/exam_files/global.css', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/exam_files/shCoreDefault.css', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/exam_files/ui.css', ['depends' => ['app\assets\AppAsset']]);
$number = empty($_GET['number'])?1:$_GET['number'];

?>

<div id="jsCpn_12_layer_1" class="layer-container" style="width: 100%; height:100% z-index: 1002; top: 103px;display: none;">
    <div class="layer-container-bg"></div>
    <div class="layer-container-content">
        <div id="jsCpn_13_popup_1" class=" pop-box " style="width: 630px; margin-left: 405px; margin-top: 0px;">
            <div class="pop-title"><span class="pop-error-tip" style="display:none;"></span><h1>提前交卷提示</h1></div>
            <div class="pop-content"><div class="pop-subject-tips-wrapper"><img src="//static.nowcoder.com/fe/style/images/common/res/tips/13.png" style="width:80px;height:80px;">
                    <div class="pop-exit-main">你还有部分题目未完成，交卷即可查看试卷全部答案及解析，是否立即交卷？</div>
                </div>
            </div>
            <div class="pop-footer clearfix"><a class="btn btn-primary confirm-btn" href="javascript:void(0);" onclick="tijiao()">立即交卷</a><a class="btn btn-default cancle-btn" href="javascript:void(0);" onclick="guanbi()">继续做题</a>
            </div>
        </div>
    </div>
</div>

<div class="nk-container">


    <div class="nk-main  clearfix">
        <div class="module-box subject-box">
            <div class="nowcoder-topic">
                <div class="com-subject-wrap">
                    <a href="javascript:void(0);" class="com-logo"><img
                                src="../web/exam_files/59_1573270956405_8A9C8878206EDC89EA273AF6F4418EA6"
                                style="height:30px;display:block;"></a>
                    <div class="com-subject-title">  Java工程师能力评估</div>
                    <!--  <a href="javascript:void(0);" class="progress-time" title="暂停">
                          <i class="ico-time-control"></i><span data-left="7200" data-time="0" class="time-text"
                                                                style="font-size: 20px;">00:09:23</span>
                      </a>-->
                </div>
                <div class="subject-title-box">
                    <div class="answer-progress"><?=$number;?>/20</div>
                    <div class="subject-title">
                        [<?=$answerType[$info['answer_type']]?>]
                    </div>
                </div>
                <div class="subject-main">
                    <div class="subject-content">
                        <div class="subject-question">
                            <?=$info['question_title'];?>
                        </div>
                        <?php foreach ($info['answers'] as $key => $value) :?>
                            <a href="javascript:void(0);"  class="subject-options" data-id="0">
                                <label class="radio" id="<?=$key;?>">
                                    <span class="icons"></span>
                                    <input type="radio" data-toggle="radio" value="<?=$key;?>">
                                    <pre><?=$value;?></pre>
                                </label>
                            </a>
                        <?php endforeach; ?>
                        <div class="subject-action clearfix">
                            <div class="subject-next">
                                <input id="aheadFinish" type="submit" name="button" class="btn warning-btn"
                                           value="提前交卷">
                                <input id="next" type="submit" name="button" data-href= "?token=<?=$_GET['token']?>&course_id=<?=$_GET['course_id'];?>&guan_id=<?=$_GET['guan_id'];?>&number=<?=$number+1;?>" class="btn btn-primary submit-one-question" value="下一题">
                            </div>
                        </div>
                    </div>
                    <!-- 展开的时候加class:open -->
                    <div class="answer-sheet-box open">
                        <div class="answer-sheet-type">
                            <i class="icon-nc-make-up"></i>
                            <span>总共<?=$count;?>道</span>
                        </div>
                        <a href="javascript:void(0)" class="card-unfold">收起答题卡</a>
                        <a href="javascript:void(0)" class="card-fold">展开答题卡</a>
                        <ul class="answer-sheet-num clearfix">
                            <?php for($i=1;$i<=$count;$i++)  :?>
                            <li>
                                <a  data-href= "?token=<?=$_GET['token']?>&course_id=<?=$_GET['course_id'];?>&guan_id=<?=$_GET['guan_id'];?>&number=<?=$i;?>"  class="<?=($i==$number)?"answering-num":""?> submit-one-question" data-qid=<?=$i;?>>
                                    <?=$i;?>
                                </a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </div>


                </div>

            </div>


        </div>


    </div>


    <script src="https://cdn.bootcss.com/jquery/3.3.0/jquery.min.js"></script>
    <script type="text/javascript" src="../web/exam_files/main.entry.js"></script>

    <script>
        var question_id = <?=$info['id'];?>;
        var course_id = <?=$_GET['course_id'];?>;
        var guan_id = <?=$_GET['guan_id'];?>;
        var token = <?=$_GET['token'];?>;
        var guanKey = 'question' + course_id + guan_id;

        var answer_type = <?=$info['answer_type'];?>;
        $(".subject-options").click(function () {
            if(answer_type == 1){
                if (($(this).find(".radio").hasClass('checked'))) {
                    $(".radio").removeClass("checked");
                    $(this).find(".radio").removeClass("checked");
                } else {
                    $(".radio").removeClass("checked");
                    $(this).find(".radio").addClass("checked");
                }
            }else if(answer_type == 3){
                if (($(this).find(".radio").hasClass('checked'))) {
                    $(this).find(".radio").removeClass("checked");
                } else {
                    $(this).find(".radio").addClass("checked");
                }
            }

            return false;
        });

        var content = [];

        $("#submitForm").submit(function () {
            $("label").each(function () {
                if ($(this).hasClass('checked')) {
                    content.push($(this).attr("id"));
                }
            });

            $("#answer").val(content.join(','));
        });

        $("#aheadFinish").click(function () {
            $('#jsCpn_12_layer_1').show();
        });
        $(".submit-one-question").click(function(){
            var content = [];
            $("label").each(function () {
                if ($(this).hasClass('checked')) {
                    content.push($(this).attr("id"));
                }
            });
            $("#answer").val(content.join(','));
            var newAnswers = {};
            var answers = '';
            this.loading = layer.load();
            var answer = {[question_id]:content};
            answers = localStorage.getItem(guanKey);
            if(answers){
                newAnswers = JSON.parse(answers);
                newAnswers[question_id] = content;
            }else{
                newAnswers = answer;
            }
            newAnswers = JSON.stringify(newAnswers);
            localStorage.setItem(guanKey, newAnswers);
            window.location.href = $(this).attr('data-href');
        });

        function guanbi() {
            $("#jsCpn_12_layer_1").hide();

        }
        function tijiao() {
            myAnswers = localStorage.getItem(guanKey);
            myAnswers = JSON.parse(myAnswers);
            $.post("<?=Url::toRoute(['site/submit'])?>",{myAnswers:myAnswers,guan_id:guan_id,course_id:course_id,token:token},function (data) {
                if(data.errCode == 0){
                    window.location.href = "<?=Url::toRoute(['site/answer'])?>"+"?token=<?=$_GET['token']?>&course_id=<?=$_GET['course_id'];?>&guan_id=<?=$_GET['guan_id'];?>";
                }
            })
        }
        $(function () {
            myAnswers = localStorage.getItem(guanKey);
            myAnswers = JSON.parse(myAnswers);
            if(myAnswers && myAnswers[question_id]){
                $.each(myAnswers[question_id],function(index,value){
                    $(".subject-options").find('#'+value).addClass("checked");
                });
            }

        });


    </script>
</div>


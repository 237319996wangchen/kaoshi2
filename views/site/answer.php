<?php

$this->title = '答案解析页';
$this->registerCssFile('@web/css/index.css', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/exam_files/base.css', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/exam_files/default.css', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/exam_files/exercise.css', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/exam_files/global(1).css', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/exam_files/global.css', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/exam_files/shCoreDefault.css', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/exam_files/ui.css', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/exam_files/answer.css', ['depends' => ['app\assets\AppAsset']]);


?>
<div class="nk-container">
    <div class="nk-main analytic-page clearfix">
        <span class="js-question-title" style="display:none;"><?=$info['question_title']?></span>
        <div class="module-box clearfix">
            <div class="result-subject-item">
                <h1>每题得分</h1>
                <ul class="subject-num-list">

                    <?php for($i=1;$i<=$count;$i++)  :?>
                        <li class="error-order">
                            <a class="done-hover " href="?course_id=<?=$_GET['course_id'];?>&guan_id=<?=$_GET['guan_id'];?>&number=<?=$i;?>" data-qid="907557">
                                <?=$i;?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>
            <input type="hidden" id="questionType" value="1"/>
            <input type="hidden" id="questionId" value="907557"/>
            <div class="result-question-box">
                <div class="subject-question">
                    <span class="question-number"><?=empty($_GET['number'])?1:$_GET['number'];?></span><?=$info['question_title']?></span></div>
                </div>
            </div>
            <div class="result-subject-item result-subject-answer">
                <h1 id="answer-result">
                </h1>
                <?php foreach ($info['answers'] as $key => $value) :?>
                    <div class="result-answer-item <?=(is_array($info['answer_id']) ? in_array($key,$info['answer_id']): $info['answer_id'] == $key) ? 'green-answer-item' : ''?>">
                        <pre><?=$value;?></pre>
                    </div>
                <?php endforeach;?>
            </div>
            <div id="referAnchor"></div>
            <script></script>
            <script>
                window.problem = {
                    id: 907557,
                    type: 1,
                    isAdmin: false,
                    isMaintainer: false,
                    uuid: 'b62df87aa7494765b01a1166077e37b6',
                    curLanguage: '0',
                    supportLanguages: ''
                };
                window.comment = {
                    count: 0,
                    all: [],
                    draft: {"content":""}
                };
            </script>
        </div>
        <script>
            window.isLogin = true;
            window.globalInfo = {
                ownerId: '48235516',
                ownerName: 'ppap201907242300324',
                ownerTinyHead: 'https://images.nowcoder.com/images/20190724/48235516_1563980426974_605475850F555A9A1D76953CFB3E39A6?x-oss-process=image/resize,m_mfit,h_100,w_100',
                ownerMainHead: 'https://images.nowcoder.com/images/20190724/48235516_1563980426974_605475850F555A9A1D76953CFB3E39A6?x-oss-process=image/resize,m_mfit,h_200,w_200',
                ownerEmail:  true ,
                ownerPhone:  true ,
                ownerType: '1',
                status: '0',
                honorLevel: '2',
                gender: '',
                isCompleteInfo: true,
                canLoadInIframe: 'true'
            };
            window.useNeteaseCaptcha = true;
        </script>
    <script src="https://cdn.bootcss.com/jquery/3.3.0/jquery.min.js"></script>
    <script type="text/javascript">
            window.ncDeployImageRoot = "//static.nowcoder.com/";
            window.ncJsInDev =  false ;
            window.ncJsVersion = "1.03.28";
            window.ncJsPluginVersion = "1.0.17";
            window.ncIsInNowcoderMainSite = true;
        </script>
        <script type="text/javascript">

            (function () {
                var oBody = document.body;
                var sOverflow = oBody.style.overflow;
                var sUa = (window.navigator || {}).userAgent || '';
                var m = sUa.match(/MSIE ([^;]*)|Trident.*; rv(?:\s|:)?([0-9.]+)/) || [];
                var nVersion = +m[1] || +m[2] || 0;
                nVersion && nVersion <= 8 && _fShowMask();
                function _fShowMask() {
                    oBody.style.overflow = 'hidden';
                    var nWidth = window.document.documentElement.clientWidth;
                    var nHeight = window.document.documentElement.clientHeight;
                    var nTop = ('pageYOffset' in window ? window.pageYOffset : document.documentElement.scrollTop) || 0;
                    var sHtml = [
                        '<div style="position:absolute;left:0;top:' + nTop + 'px;z-index:99999;width:' + nWidth + 'px;height:' + nHeight + 'px;">',
                        '<div style="position:absolute;left:0;top:0;width:100%;height:100%;background:#000;opacity:0.5;filter:alpha(opacity=50);"></div>',
                        '<div class="pop-box" style="width:800px;z-index:100000;left:' + (nWidth - 800) / 2 + 'px;top:' + Math.max(0, (nHeight - 624) / 2) + nTop + 'px;">',
                        '<div class="pop-title"><h1>浏览器版本过低提示</h1><a href="javascript:void(0);" onclick="closeIE8GlobalMask()" class="pop-close" title="关闭"></a></div>',
                        '<div class="pop-content clearfix">',
                        '<div class="pop-subject-tips-wrapper">',
                        '<a href="javascript:void(0);"><img src="//static.nowcoder.com/fe/style/images/common/res/tips/13.png"></a>',
                        '<div class="pop-exit-main">你使用的浏览器版本过低，无法正常访问牛客<br />请使用牛客APP或更换浏览器。</div>',
                        '<div class="pop-download-browser">',
                        '<ul class="pop-browser-list">',
                        '<li><p>下载牛客APP</p><div class="pic"><img src="https://uploadfiles.nowcoder.com/app/app_download.png"></div></li>',
                        '<li><a href="https://static.nowcoder.com/b/i/googlechrome74_32.exe" target="_blank"><p>下载Chrome浏览器</p><div class="pic"><img src="//static.nowcoder.com/images/res/browser/chrome.png"></div></a></li>',
                        '<li><a href="https://www.firefox.com.cn/download/#more" target="_blank"><p>下载Firefox浏览器</p><div class="pic"><img src="//static.nowcoder.com/images/res/browser/firefox.png"></div></a></li>',
                        '</ul>',
                        '<a href="javascript:void(0);" onclick="closeIE8GlobalMask()" class="link-green">我知道访问会出错，但是我还要继续用这个浏览器浏览 &gt;&gt;</a>',
                        '</div>',
                        '</div>',
                        '</div>',
                        '</div>',
                        '</div>'].join('');
                    var oDv = document.createElement('div');
                    oDv.innerHTML = sHtml;
                    oBody.appendChild(oDv);
                    window.closeIE8GlobalMask = function () {
                        oBody.removeChild(oDv);
                        oBody.style.overflow = sOverflow;
                        _fShowBar();
                    };
                }
                function _fShowBar() {
                    var oDv = document.createElement('div');
                    oDv.innerHTML = '<div class="tip-block-box" onclick="closeIE8GlobalBar()" style="position:fixed;left:0;top:54px;width:100%;z-index:99999;"><div class="tip-block">你使用的浏览器版本过低，无法正常访问牛客，请使用牛客APP或更换浏览器，<a href="javascript:void(0);">点击查看详情</a>。</div></div>';
                    oBody.appendChild(oDv);
                    window.closeIE8GlobalBar  = function () {
                        oBody.removeChild(oDv);
                        _fShowMask();
                    };
                };
                var question_id = <?=$info['id'];?>;
                var number_letter = <?=$info['number_letter'];?>;
                var course_id = <?=$_GET['course_id'];?>;
                var guan_id = <?=$_GET['guan_id'];?>;
                var guanKey = 'question' + course_id + guan_id;
                var question_res = localStorage.getItem(guanKey);
                question_res = JSON.parse(question_res);
                var my_answer = '';
                if(question_res[question_id]){
                    $.each( question_res[question_id],function(index,value){
                        my_answer += number_letter[value];
                    });
                }
                $('#answer-result').html('正确答案:\n <?=$info['answer_string'];?>\n&nbsp;&nbsp;你的答案:'+my_answer+'\n <span class="font-orange">(错误)</span>');
            })();
        </script>




    </div>

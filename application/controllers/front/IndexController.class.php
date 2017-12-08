<?php
    class IndexController extends Controller {
        // 公共资源，如导航信息，产品分类信息等数据
        public static function indexAction(){
            $sub_path = date('Ymd').'/';
            echo '请求错误';
        }
        
        public static function getCaptchaAction(){ // 获取二维码
            $c = new Captcha();
            $c->generateCode();
            $_SESSION['captcha'] = $c->getCode();
        }
    }
?>